<?php
session_start();
if (!isset($_SESSION['user'])) {
    exit('Unauthorized');
}
include('db.php');

$user = $_SESSION['user'];
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'load_tasks') {
    // Load tasks based on user role.
    if ($user['role'] == 'admin') {
        $stmt = $pdo->query("SELECT tasks.*, users.username FROM tasks 
                             LEFT JOIN users ON tasks.assigned_to = users.id 
                             ORDER BY tasks.created_at DESC");
    } else {
        $stmt = $pdo->prepare("SELECT tasks.*, users.username FROM tasks 
                               LEFT JOIN users ON tasks.assigned_to = users.id 
                               WHERE assigned_to = ? 
                               ORDER BY tasks.created_at DESC");
        $stmt->execute([$user['id']]);
    }
    
    $tasks = $stmt->fetchAll();
    if ($tasks) {
        // Wrap the table in a responsive container.
        echo '<div class="table-responsive">';
        echo '<table class="table table-bordered table-hover">';
        echo '<thead class="table-light"><tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>';
        if ($user['role'] == 'admin') {
            echo '<th>Assigned To</th>';
        }
        echo '<th>Status</th>
              <th>Created At</th>
              <th>Action</th>
              </tr></thead><tbody>';
        
        foreach ($tasks as $task) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($task['id']) . '</td>';
            echo '<td>' . htmlspecialchars($task['title']) . '</td>';
            echo '<td>' . htmlspecialchars($task['description']) . '</td>';
            if ($user['role'] == 'admin') {
                echo '<td>' . htmlspecialchars($task['username']) . '</td>';
            }
            // Set colored text for the status.
            $status = htmlspecialchars($task['status']);
            if ($status === 'pending') {
                $statusText = '<span class="text-danger">' . $status . '</span>';
            } elseif ($status === 'to be approve') {
                $statusText = '<span class="text-warning">' . $status . '</span>';
            } elseif ($status === 'completed') {
                $statusText = '<span class="text-success">' . $status . '</span>';
            } else {
                $statusText = $status;
            }
            echo '<td>' . $statusText . '</td>';
            echo '<td>' . htmlspecialchars($task['created_at']) . '</td>';
            
            echo '<td>';
            if ($user['role'] == 'admin') {
                // Display action icons horizontally.
                echo '<div class="d-flex flex-row align-items-center">';
                echo '<button class="edit-task btn btn-sm btn-info me-1" data-id="' . $task['id'] . '" title="Edit">
                        <i class="bi bi-pencil"></i>
                      </button>';
                echo '<button class="delete-task btn btn-sm btn-danger" data-id="' . $task['id'] . '" title="Delete">
                        <i class="bi bi-trash"></i>
                      </button>';
                echo '</div>';
            } else {
                // For employees.
                if ($task['status'] == 'pending' && empty($task['accomplishment_report'])) {
                    echo '<button class="report-task btn btn-sm btn-warning" 
                                data-id="' . $task['id'] . '" 
                                data-title="' . htmlspecialchars($task['title']) . '" 
                                data-description="' . htmlspecialchars($task['description']) . '" 
                                data-status="' . htmlspecialchars($task['status']) . '">Report Accomplishment</button>';
                } elseif ($task['status'] == 'to be approve') {
                    echo '<span class="badge bg-info">Report Submitted</span>';
                } elseif ($task['status'] == 'completed') {
                    echo '<span class="badge bg-success">Completed</span>';
                } else {
                    echo '--';
                }
            }
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo '</div>'; // End responsive container.
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
    
// Employee submits an accomplishment report.
} elseif ($action == 'submit_report' && $user['role'] == 'employee') {
    $task_id = intval($_POST['task_id']);
    $report = trim($_POST['report']);
    $documentation = "";
    
    if (isset($_FILES['documentation']) && $_FILES['documentation']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filename = basename($_FILES['documentation']['name']);
        $targetFile = $uploadDir . time() . "_" . $filename;
        if (move_uploaded_file($_FILES['documentation']['tmp_name'], $targetFile)) {
            $documentation = $targetFile;
        }
    }
    $stmt = $pdo->prepare("UPDATE tasks SET accomplishment_report = ?, documentation = ?, status = 'to be approve' WHERE id = ? AND assigned_to = ?");
    if ($stmt->execute([$report, $documentation, $task_id, $user['id']])) {
        echo "Report submitted successfully.";
    } else {
        echo "Error submitting report.";
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
    // New "Employee List" shows all registered employees.
    $stmt = $pdo->prepare("SELECT u.id, u.username, 
                            GROUP_CONCAT(t.title SEPARATOR ', ') AS tasks, 
                            GROUP_CONCAT(t.status SEPARATOR ', ') AS statuses,
                            COUNT(t.id) AS task_count
                           FROM users u 
                           LEFT JOIN tasks t ON u.id = t.assigned_to 
                           WHERE u.role = 'employee'
                           GROUP BY u.id");
    $stmt->execute();
    $employees = $stmt->fetchAll();
    if ($employees) {
      echo '<div class="table-responsive"><table class="table table-bordered table-hover">';
      echo '<thead class="table-light"><tr>
              <th>Employee Name</th>
              <th>Task Titles</th>
              <th>Task Statuses</th>
            </tr></thead><tbody>';
      foreach ($employees as $emp) {
        $tasks = !empty($emp['tasks']) ? htmlspecialchars($emp['tasks']) : 'No assigned tasks';
        $statuses = $emp['task_count'] > 0 ? htmlspecialchars($emp['statuses']) : 'Vacant';
        echo '<tr>';
        echo '<td>' . htmlspecialchars($emp['username']) . '</td>';
        echo '<td>' . $tasks . '</td>';
        echo '<td>' . $statuses . '</td>';
        echo '</tr>';
      }
      echo '</tbody></table></div>';
    } else {
      echo '<p>No employee data found.</p>';
    }
    
} elseif ($action == 'approve_report' && $user['role'] == 'admin') {
    $task_id = intval($_POST['task_id']);
    $stmt = $pdo->prepare("UPDATE tasks SET status = 'completed' WHERE id = ?");
    if ($stmt->execute([$task_id])) {
        echo "Task marked as completed.";
    } else {
        echo "Error approving report.";
    }
    
} else {
    echo "Invalid action or insufficient permissions.";
}
?>
