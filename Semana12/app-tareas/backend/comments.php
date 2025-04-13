<?php
require('db.php');
session_start();
header('Content-Type: application/json');

// Funciones
function getComments($taskId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE task_id = :task_id");
    $stmt->execute(['task_id' => $taskId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addComment($taskId, $comment) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO comments (task_id, comment) VALUES (:task_id, :comment)");
    $stmt->execute(['task_id' => $taskId, 'comment' => $comment]);
    return $pdo->lastInsertId();
}

function updateComment($id, $comment) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE comments SET comment = :comment WHERE id = :id");
    $stmt->execute(['comment' => $comment, 'id' => $id]);
    return $stmt->rowCount() > 0;
}

function deleteComment($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->rowCount() > 0;
}

function getJsonInput() {
    return json_decode(file_get_contents("php://input"), true);
}

// Seguridad
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Sesión no activa']);
    exit;
}

// Manejo de métodos
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['task_id'])) {
                $comments = getComments($_GET['task_id']);
                echo json_encode($comments);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Falta task_id']);
            }
            break;

        case 'POST':
            $data = getJsonInput();
            if (isset($data['task_id'], $data['comment'])) {
                $id = addComment($data['task_id'], $data['comment']);
                echo json_encode(['message' => 'Comentario agregado', 'id' => $id]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Faltan datos']);
            }
            break;

        case 'PUT':
            if (isset($_GET['id'])) {
                $data = getJsonInput();
                if (isset($data['comment'])) {
                    $ok = updateComment($_GET['id'], $data['comment']);
                    echo json_encode(['message' => $ok ? 'Comentario actualizado' : 'No se actualizó nada']);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Falta el comentario']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Falta id']);
            }
            break;

        case 'DELETE':
            if (isset($_GET['id'])) {
                $ok = deleteComment($_GET['id']);
                echo json_encode(['message' => $ok ? 'Comentario eliminado' : 'No se eliminó nada']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Falta id']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
}