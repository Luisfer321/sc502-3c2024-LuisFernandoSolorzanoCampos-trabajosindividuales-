document.addEventListener('DOMContentLoaded', function () {

    const API_URL = "backend/tasks.php";
    const COMMENTS_URL = "backend/comments.php";
    let isEditMode = false;
    let edittingId;
    let tasks = [];

    async function loadTasks() {
        try {
            const response = await fetch(API_URL, { method: 'GET', credentials: 'include' });
            if (response.ok) {
                tasks = await response.json();
                renderTasks(tasks);
            } else {
                if (response.status == 401) {
                    window.location.href = "index.html";
                }
                console.error("Error al obtener tareas");
            }
        } catch (err) {
            console.error(err);
        }
    }

    function renderTasks() {
        const taskList = document.getElementById('task-list');
        taskList.innerHTML = '';
        tasks.forEach(function (task) {

            let commentsList = '';
            if (task.comments && task.comments.length > 0) {
                commentsList = '<ul class="list-group list-group-flush">';
                task.comments.forEach(comment => {
                    commentsList += `<li class="list-group-item">${comment.description} 
                    <button type="button" class="btn btn-sm btn-link remove-comment" data-visitid="${task.id}" data-commentid="${comment.id}">Remove</button>
                    </li>`;
                });
                commentsList += '</ul>';
            }

            const taskCard = document.createElement('div');
            taskCard.className = 'col-md-4 mb-3';
            taskCard.innerHTML = `
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">${task.title}</h5>
                    <p class="card-text">${task.description}</p>
                    <p class="card-text"><small class="text-muted">Due: ${task.due_date}</small></p>
                    ${commentsList}
                    <button type="button" class="btn btn-sm btn-link add-comment" data-id="${task.id}">Add Comment</button>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <button class="btn btn-secondary btn-sm edit-task" data-id="${task.id}">Edit</button>
                    <button class="btn btn-danger btn-sm delete-task" data-id="${task.id}">Delete</button>
                </div>
            </div>
            `;
            taskList.appendChild(taskCard);
        });

        document.querySelectorAll('.edit-task').forEach(button => {
            button.addEventListener('click', handleEditTask);
        });

        document.querySelectorAll('.delete-task').forEach(button => {
            button.addEventListener('click', handleDeleteTask);
        });

        document.querySelectorAll('.add-comment').forEach(button => {
            button.addEventListener('click', async function (e) {
                const taskId = e.target.dataset.id;
                document.getElementById("comment-task-id").value = taskId;
                await cargarComentarios(taskId);
                const modal = new bootstrap.Modal(document.getElementById("commentModal"));
                modal.show();
            });
        });

        document.querySelectorAll('.remove-comment').forEach(button => {
            button.addEventListener('click', async function (e) {
                const commentId = parseInt(e.target.dataset.commentid);
                await fetch(`${COMMENTS_URL}?id=${commentId}`, {
                    method: 'DELETE',
                    credentials: 'include'
                });
                loadTasks();
            });
        });
    }

    async function handleEditTask(event) {
        const taskId = parseInt(event.target.dataset.id);
        const task = tasks.find(t => t.id === taskId);
        document.getElementById('task-title').value = task.title;
        document.getElementById('task-desc').value = task.description;
        document.getElementById('due-date').value = task.due_date;
        isEditMode = true;
        edittingId = taskId;
        const modal = new bootstrap.Modal(document.getElementById("taskModal"));
        modal.show();
    }

    async function handleDeleteTask(event) {
        const id = parseInt(event.target.dataset.id);
        await fetch(`${API_URL}?id=${id}`, {
            credentials: 'include',
            method: 'DELETE'
        });
        loadTasks();
    }

    document.getElementById('comment-form').addEventListener('submit', async function (e) {
        e.preventDefault();
        const comment = document.getElementById('task-comment').value;
        const taskId = parseInt(document.getElementById('comment-task-id').value);
        await guardarComentario(taskId, comment);
        const modal = bootstrap.Modal.getInstance(document.getElementById('commentModal'));
        modal.hide();
        loadTasks();
    });

    document.getElementById('task-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        const title = document.getElementById("task-title").value;
        const description = document.getElementById("task-desc").value;
        const dueDate = document.getElementById("due-date").value;

        if (isEditMode) {
            await fetch(`${API_URL}?id=${edittingId}`, {
                method: 'PUT',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ title, description, due_date: dueDate })
            });
        } else {
            await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': "application/json"
                },
                body: JSON.stringify({ title, description, due_date: dueDate }),
                credentials: 'include'
            });
        }

        const modal = bootstrap.Modal.getInstance(document.getElementById('taskModal'));
        modal.hide();
        loadTasks();
    });

    document.getElementById('commentModal').addEventListener('show.bs.modal', function () {
        document.getElementById('comment-form').reset();
    });

    document.getElementById('taskModal').addEventListener('show.bs.modal', function () {
        if (!isEditMode) {
            document.getElementById('task-form').reset();
        }
    });

    document.getElementById("taskModal").addEventListener('hidden.bs.modal', function () {
        edittingId = null;
        isEditMode = false;
    });

    loadTasks();

    // Helpers para comentarios
    async function cargarComentarios(taskId) {
        const res = await fetch(`${COMMENTS_URL}?task_id=${taskId}`, { credentials: 'include' });
        const comentarios = await res.json();
        console.log("Comentarios cargados:", comentarios);
    }

    async function guardarComentario(taskId, comentario) {
        const res = await fetch(COMMENTS_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify({ task_id: taskId, comment: comentario })
        });

        const data = await res.json();
        console.log(data);
    }
});
