document.addEventListener('DOMContentLoaded', function() {
    const tasks = [
        { id: 1, title: "Complete project report", description: "Prepare and submit the project report", dueDate: "2024-12-01" },
        { id: 2, title: "Team Meeting", description: "Get ready for the season", dueDate: "2024-12-01" },
        { id: 3, title: "Code Review", description: "Check partners code", dueDate: "2024-12-01" }
    ];

    function loadTasks() {
        const taskList = document.getElementById('task-list');
        taskList.innerHTML = '';
        tasks.forEach(function(task) {
            const taskCard = document.createElement('div');
            taskCard.className = 'col-md-4 mb-3';
            taskCard.innerHTML = `
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">${task.title}</h5>
                    <p class="card-text">${task.description}</p>
                    <p class="card-text"><small class="text-muted">Due: ${task.dueDate}</small> </p>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <button class="btn btn-secondary btn-sm edit-task" data-id="${task.id}">Edit</button>
                    <button class="btn btn-danger btn-sm delete-task" data-id="${task.id}">Delete</button>
                </div>
            </div>
            `;
            taskList.appendChild(taskCard);
        });

        document.querySelectorAll('.edit-task').forEach(function(button) {
            button.addEventListener('click', handleEditTask);
        });

        document.querySelectorAll('.delete-task').forEach(function(button) {
            button.addEventListener('click', handleDeleteTask);
        });
    }

    function handleEditTask(event) {
        const taskId = event.target.dataset.id;
        const task = tasks.find(t => t.id == taskId);

        // Fill the modal form with task data
        document.getElementById('task-id').value = task.id;
        document.getElementById('task-title').value = task.title;
        document.getElementById('task-desc').value = task.description;
        document.getElementById('due-date').value = task.dueDate;

        // Show modal
        $('#taskModal').modal('show');
    }

    function handleDeleteTask(event) {
        const taskId = event.target.dataset.id;
        const taskIndex = tasks.findIndex(t => t.id == taskId);
        tasks.splice(taskIndex, 1);
        loadTasks();
    }

    document.getElementById('task-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const taskId = document.getElementById('task-id').value;
        const taskTitle = document.getElementById('task-title').value;
        const taskDesc = document.getElementById('task-desc').value;
        const dueDate = document.getElementById('due-date').value;

        if (taskId) {
            // Update existing task
            const task = tasks.find(t => t.id == taskId);
            task.title = taskTitle;
            task.description = taskDesc;
            task.dueDate = dueDate;
        } else {
            // Add new task
            const newTask = {
                id: tasks.length + 1, 
                title: taskTitle, 
                description: taskDesc, 
                dueDate: dueDate
            };
            tasks.push(newTask);
        }

        loadTasks();
        // Close modal
        $('#taskModal').modal('hide');
    });

    loadTasks();
});