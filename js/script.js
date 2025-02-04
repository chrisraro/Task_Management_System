// js/script.js
$(document).ready(function(){

  // Function to load tasks via AJAX.
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
  
  // Function to load the employee pool (for admin).
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
  
  // Initial load.
  loadTasks();
  if ($('#employeePoolContainer').length) {
    loadEmployeePool();
  }
  
  // Auto-refresh every 60 seconds.
  setInterval(function(){
    loadTasks();
    if ($('#employeePoolContainer').length) {
      loadEmployeePool();
    }
  }, 60000);
  
  // Admin: Add Task form submission.
  $('#addTaskForm').on('submit', function(e){
    e.preventDefault();
    var title = $('#taskTitle').val().trim();
    var assignedTo = $('#assignedTo').val().trim();
    if (title === '' || assignedTo === '') {
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
  
  // Employee: Open Report Modal when "Report Accomplishment" is clicked.
  $('#tasksContainer').on('click', '.report-task', function(){
    // Retrieve data attributes from the clicked button.
    var taskId = $(this).data('id');
    var title = $(this).data('title');
    var description = $(this).data('description');
    var status = $(this).data('status');
    
    console.log("Opening Report Modal for Task ID:", taskId);
    
    // Populate the modal fields.
    $('#reportTaskId').val(taskId);
    $('#reportTaskTitle').text(title);
    $('#reportTaskStatus').text(status);
    $('#reportTaskDescription').text(description);
    $('#accomplishmentReport').val('');
    $('#documentation').val('');
    
    // Open the modal using Bootstrap's Modal API.
    var reportModalEl = document.getElementById('reportModal');
    if (reportModalEl) {
      var reportModal = new bootstrap.Modal(reportModalEl, { backdrop: true, keyboard: true });
      reportModal.show();
      
      // Set focus on the textarea after the modal is shown.
      setTimeout(function(){
        $('#accomplishmentReport').focus();
      }, 500);
    } else {
      console.error("Modal element with id 'reportModal' not found.");
    }
  });
  
  // Employee: Submit Report form submission.
  $('#reportForm').on('submit', function(e){
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
      url: 'ajax.php',
      method: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function(response){
        alert(response);
        var reportModalEl = document.getElementById('reportModal');
        var modalInstance = bootstrap.Modal.getInstance(reportModalEl);
        if (modalInstance) {
          modalInstance.hide();
        }
        loadTasks();
      }
    });
  });
  
  // Admin: Open Edit Modal when "Edit" is clicked.
  $('#tasksContainer').on('click', '.edit-task', function(){
    var taskId = $(this).data('id');
    var row = $(this).closest('tr');
    var title = row.find('td:eq(1)').text();
    var description = row.find('td:eq(2)').text();
    // For simplicity, prompt for employee ID.
    var assignedTo = prompt("Enter the Employee ID for this task:", "");
    if (assignedTo == null || assignedTo.trim() === "") {
      alert("Assigned To field is required.");
      return;
    }
    
    $('#editTaskId').val(taskId);
    $('#editTaskTitle').val(title);
    $('#editTaskDescription').val(description);
    $('#editAssignedTo').val(assignedTo);
    
    var editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
  });
  
  // Admin: Submit Edit Task form submission.
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
        if (modal) {
          modal.hide();
        }
        loadTasks();
        loadEmployeePool();
      }
    });
  });
  
  // Admin: Delete Task action.
  $('#tasksContainer').on('click', '.delete-task', function(){
    var taskId = $(this).data('id');
    if (confirm("Are you sure you want to delete this task?")) {
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
});
