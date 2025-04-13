<?php
require('db.php');

function createTask($userId, $title, $description, $dueDate)
{
    global $pdo;
    try {
        $sql = "insert into tasks (user_id, title, description, due_date) value (:user_id, :title, :description, :due_date)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'title' => $title,
            'description' => $description,
            'due_date' => $dueDate
        ]);
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        echo $e->getMessage();
        return 0;
    }
}

function getTasksByUser($userId)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $ex) {
        echo "Error al obtener las tareas del usuario" . $ex->getMessage();
        return [];
    }
}

function editTask($id, $title, $description, $dueDate)
{
    global $pdo;
    try {
        $sql = "UPDATE tasks SET title = :title, description = :description, due_date = :due_date WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'title' => $title,
            'description' => $description,
            'due_date' => $dueDate,
            'id' => $id
        ]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}

function deleteTask($id)
{
    global $pdo;
    try {
        $sql = "DELETE FROM tasks WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}

function validateInput($input)
{
    return isset($input['title'], $input['description'], $input['due_date']);
}

function getJsonInput()
{
    return json_decode(file_get_contents("php://input"), associative: true);
}

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["error" => "Sesion no activa"]);
    exit;
}

$userId = $_SESSION["user_id"];
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $tareas = getTasksByUser($userId);
            foreach ($tareas as &$tarea) {
                $stmt = $pdo->prepare("SELECT id, comment AS description FROM comments WHERE task_id = :task_id");
                $stmt->execute(['task_id' => $tarea['id']]);
                $tarea['comments'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            echo json_encode($tareas);
            break;

        case 'POST':
            $input = getJsonInput();
            if (validateInput($input)) {
                $idTask = createTask($userId, $input['title'], $input['description'], $input['due_date']);
                if ($idTask > 0) {
                    http_response_code(201);
                    echo json_encode(["message" => "Tarea creada exitosamente. Id:" . $idTask]);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => "Error general creando la tarea"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Datos insuficientes"]);
            }
            break;

        case 'PUT':
            $input = getJsonInput();
            if (validateInput($input)) {
                if (editTask($_GET['id'], $input['title'], $input['description'], $input['due_date'])) {
                    http_response_code(201);
                    echo json_encode(['message' => "Tarea actualizada exitosamente"]);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => "Error interno al actualizar la tarea"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Datos insuficientes']);
            }
            break;

        case 'DELETE':
            if ($_GET['id']) {
                if (deleteTask($_GET['id'])) {
                    http_response_code(200);
                    echo json_encode(['message' => "Tarea eliminada exitosamente"]);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => "Error interno al eliminar una tarea"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => "Peticion invalida"]);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Metodo no permitido"]);
    }
} catch (Exception $exp) {
    http_response_code(500);
    echo json_encode(['error' => "Error al procesar el request"]);
}