// js/script.js
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
    
    // Function to load employee pool (for admin)
    function loadEmployeePool(){
      $.ajax({
        url: 'ajax.php',
        method: 'POST',
        data: { action: 'load_employee_pool' },
        success: function(response){
          $('#employeePoolContainer').html(response);
        }
      });
    }
    
    // Initial load
    loadTasks();
    // For admin, load employee pool as well
    if($('body').find('#employeePoolContainer').length){
      loadEmployeePool();
    }
    
    // Auto-refresh every 60 seconds
    setInterval(function(){
      loadTasks();
      if($('body').find('#employeePoolContainer').length){
        loadEmployeePool();
      }
    }, 60000);
    
    // Add Task form submission (admin)
    $('#addTaskForm').on('submit', function(e){
      e.preventDefault();
      var title = $('#taskTitle').val().trim();
      var assignedTo = $('#assignedTo').val().trim();
      if(title === '' || assignedTo === ''){
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
          loadEmployeePool();
        }
      });
    });
    
    // Mark task as completed (employee)
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
    
    // Delete task (admin)
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
            loadEmployeePool();
          }
        });
      }
    });
    
    // Open Edit Modal (admin)
    $('#tasksContainer').on('click', '.edit-task', function(){
      var taskId = $(this).data('id');
      var row = $(this).closest('tr');
      var title = row.find('td:eq(1)').text();
      var description = row.find('td:eq(2)').text();
      
      // For simplicity, prompt for employee ID; you can improve this by storing the ID in a data attribute.
      var assignedTo = prompt("Enter the Employee ID for this task:", "");
      if(assignedTo == null || assignedTo.trim() === ""){
        alert("Assigned To field is required.");
        return;
      }
      
      $('#editTaskId').val(taskId);
      $('#editTaskTitle').val(title);
      $('#editTaskDescription').val(description);
      $('#editAssignedTo').val(assignedTo);
      
      // Open Bootstrap modal
      var editModal = new bootstrap.Modal(document.getElementById('editModal'));
      editModal.show();
    });
    
    // Submit edited task (admin)
    $('#editTaskForm').on('submit', function(e){
      e.preventDefault();
      $.ajax({
        url: 'ajax.php',
        method: 'POST',
        data: $(this).serialize(),
        success: function(response){
          alert(response);
          var modalEl = document.getElementById('editModal');
          var modal = bootstrap.Modal.getInstance(modalEl);
          modal.hide();
          loadTasks();
          loadEmployeePool();
        }
      });
    });
  });
  