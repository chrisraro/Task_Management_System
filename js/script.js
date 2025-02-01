$(document).ready(function(){

    // Function to load tasks via AJAX
    function loadTasks(){
        $.ajax({
            url: 'ajax.php',
            method: 'POST',
            data: { action: 'load_tasks' },
            success: function(response){
                $('#tasksContainer').html(response);
            }
        });
    }
    
    // Initial load of tasks
    loadTasks();
    
    // Auto-refresh tasks every 60 seconds (60000ms)
    setInterval(loadTasks, 60000);
    
    // Admin: Add a new task with client-side validation
    $('#addTaskForm').on('submit', function(e){
        e.preventDefault();
        
        $('#taskTitle, #assignedTo').removeClass('error');
        var title = $('#taskTitle').val().trim();
        var assignedTo = $('#assignedTo').val().trim();
        var valid = true;
        if(title === ''){
            $('#taskTitle').addClass('error');
            valid = false;
        }
        if(assignedTo === ''){
            $('#assignedTo').addClass('error');
            valid = false;
        }
        if(!valid){
            alert("Please fill in all required fields.");
            return;
        }
        
        $.ajax({
            url: 'ajax.php',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response){
                alert(response);
                $('#addTaskForm')[0].reset();
                loadTasks();
            }
        });
    });
    
    // Employee: Update task status with confirmation
    $('#tasksContainer').on('click', '.update-status', function(){
        var taskId = $(this).data('id');
        if(confirm("Are you sure you want to mark this task as completed?")){
            $.ajax({
                url: 'ajax.php',
                method: 'POST',
                data: { action: 'update_status', id: taskId },
                success: function(response){
                    alert(response);
                    loadTasks();
                }
            });
        }
    });
    
    // Admin: Delete a task with confirmation
    $('#tasksContainer').on('click', '.delete-task', function(){
        var taskId = $(this).data('id');
        if(confirm("Are you sure you want to delete this task?")){
            $.ajax({
                url: 'ajax.php',
                method: 'POST',
                data: { action: 'delete_task', id: taskId },
                success: function(response){
                    alert(response);
                    loadTasks();
                }
            });
        }
    });
    
    // Admin: Open edit modal when clicking "Edit"
    $('#tasksContainer').on('click', '.edit-task', function(){
        var taskId = $(this).data('id');
        // For simplicity, we load the current row data into the modal fields
        // You can expand this by performing an AJAX call to get the task details if needed.
        var row = $(this).closest('tr');
        var title = row.find('td:eq(1)').text();
        var description = row.find('td:eq(2)').text();
        // Note: Assigned To (employee name) is shown; you may need to store the actual ID in a data attribute.
        // For this example, we assume the "Assigned To" ID is available in a hidden data attribute.
        // Otherwise, you can modify the UI to include the ID or use another AJAX request.
        var assignedTo = prompt("Enter the Employee ID for this task:", "");
        if(assignedTo == null || assignedTo.trim() === ""){
            alert("Assigned To field is required.");
            return;
        }
        
        $('#editTaskId').val(taskId);
        $('#editTaskTitle').val(title);
        $('#editTaskDescription').val(description);
        $('#editAssignedTo').val(assignedTo);
        
        $('#editModal').show();
    });
    
    // Admin: Close modal
    $('#closeModal').on('click', function(){
        $('#editModal').hide();
    });
    
    // Admin: Submit edited task
    $('#editTaskForm').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url: 'ajax.php',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response){
                alert(response);
                $('#editModal').hide();
                loadTasks();
            }
        });
    });
});