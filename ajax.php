<?php
session_start();
if (!isset($_SESSION['user'])) {
    exit('Unauthorized');
}
include('db.php');

$user = $_SESSION['user'];
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'load_tasks') {
    // Load tasks based on user role
    if ($user['role'] == 'admin') {
        $stmt = $pdo->query("SELECT tasks.*, users.username FROM tasks LEFT JOIN users ON tasks.assigned_to = users.id ORDER BY tasks.created_at DESC");
    } else {
        $stmt = $pdo->prepare("SELECT tasks.*, users.username FROM tasks LEFT JOIN users ON tasks.assigned_to = users.id WHERE assigned_to = ? ORDER BY tasks.created_at DESC");
        $stmt->execute([$user['id']]);
    }
    
    $tasks = $stmt->fetchAll();
    if ($tasks) {
        echo '<table>';
        echo '<tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Assigned To</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Action</th>
              </tr>';
        
        foreach ($tasks as $task) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($task['id']) . '</td>';
            echo '<td>' . htmlspecialchars($task['title']) . '</td>';
            echo '<td>' . htmlspecialchars($task['description']) . '</td>';
            echo '<td>' . htmlspecialchars($task['username']) . '</td>';
            echo '<td>' . htmlspecialchars($task['status']) . '</td>';
            echo '<td>' . htmlspecialchars($task['created_at']) . '</td>';
            
            // Actions: Admin sees Edit and Delete buttons; Employee sees "Mark Completed"
            if ($user['role'] == 'admin') {
                echo '<td>
                        <button class="edit-task btn" data-id="' . $task['id'] . '">Edit</button>
                        <button class="delete-task btn" data-id="' . $task['id'] . '">Delete</button>
                      </td>';
            } else {
                if ($task['status'] == 'pending') {
                    echo '<td><button class="update-status btn" data-id="' . $task['id'] . '">Mark Completed</button></td>';
                } else {
                    echo '<td>--</td>';
                }
            }
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo 'No tasks found.';
    }
    
} elseif ($action == 'add_task' && $user['role'] == 'admin') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $assigned_to = intval($_POST['assigned_to']);
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'employee'");
    $stmt->execute([$assigned_to]);
    if (!$stmt->fetch()) {
        exit("Invalid employee ID.");
    }
    
    $stmt = $pdo->prepare("INSERT INTO tasks (title, description, assigned_to) VALUES (?, ?, ?)");
    if ($stmt->execute([$title, $description, $assigned_to])) {
        echo "Task added successfully.";
    } else {
        echo "Error adding task.";
    }
    
} elseif ($action == 'update_status' && $user['role'] == 'employee') {
    $id = intval($_POST['id']);
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND assigned_to = ?");
    $stmt->execute([$id, $user['id']]);
    $task = $stmt->fetch();
    if (!$task) {
        exit("Task not found or unauthorized.");
    }
    
    $stmt = $pdo->prepare("UPDATE tasks SET status = 'completed' WHERE id = ?");
    if ($stmt->execute([$id])) {
        echo "Task marked as completed.";
    } else {
        echo "Error updating task.";
    }
    
} elseif ($action == 'delete_task' && $user['role'] == 'admin') {
    $id = intval($_POST['id']);
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        exit("Task not found.");
    }
    
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    if ($stmt->execute([$id])) {
        echo "Task deleted successfully.";
    } else {
        echo "Error deleting task.";
    }
    
} elseif ($action == 'update_task' && $user['role'] == 'admin') {
    // Handle task editing: update title, description, and assigned_to.
    $id = intval($_POST['task_id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $assigned_to = intval($_POST['assigned_to']);
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'employee'");
    $stmt->execute([$assigned_to]);
    if (!$stmt->fetch()) {
        exit("Invalid employee ID.");
    }
    
    $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, assigned_to = ? WHERE id = ?");
    if ($stmt->execute([$title, $description, $assigned_to, $id])) {
        echo "Task updated successfully.";
    } else {
        echo "Error updating task.";
    }
} elseif ($action == 'load_employee_pool' && $user['role'] == 'admin') {
    // Query to group tasks by employee
    $stmt = $pdo->query("SELECT u.username, 
                             GROUP_CONCAT(t.title SEPARATOR ', ') AS tasks, 
                             GROUP_CONCAT(t.status SEPARATOR ', ') AS statuses 
                          FROM tasks t 
                          JOIN users u ON t.assigned_to = u.id 
                          WHERE u.role = 'employee'
                          GROUP BY u.username");
    $employees = $stmt->fetchAll();
    if ($employees) {
      echo '<div class="table-responsive"><table class="table table-bordered table-hover">';
      echo '<thead class="table-light"><tr>
              <th>Employee Name</th>
              <th>Task Titles</th>
              <th>Task Statuses</th>
            </tr></thead><tbody>';
      foreach ($employees as $emp) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($emp['username']) . '</td>';
        echo '<td>' . htmlspecialchars($emp['tasks']) . '</td>';
        echo '<td>' . htmlspecialchars($emp['statuses']) . '</td>';
        echo '</tr>';
      }
      echo '</tbody></table></div>';
    } else {
      echo '<p>No employee data found.</p>';
    }
} else {
    echo "Invalid action or insufficient permissions.";
}
?>