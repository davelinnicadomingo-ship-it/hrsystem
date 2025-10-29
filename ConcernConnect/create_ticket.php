<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$db = new Database();
$conn = $db->connect();

$user = getEmployeeData($conn, $_SESSION['employee_id']);
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = $_POST['category'] ?? '';
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $priority = $_POST['priority'] ?? 'Medium';

    if ($category_id && $title && $description) {
        $ticket_number = generateTicketNumber();
        $employee_id = $_SESSION['employee_id'];

        try {
            // ✅ Correct field names
            $stmt = $conn->prepare("
                INSERT INTO tickets (ticket_number, employee_id, category_id, title, description, priority, status, created_at, updated_at)
                VALUES (:ticket_number, :employee_id, :category_id, :title, :description, :priority, 'pending', NOW(), NOW())
            ");
            
            $stmt->execute([
                ':ticket_number' => $ticket_number,
                ':employee_id' => $employee_id,
                ':category_id' => $category_id,
                ':title' => $title,
                ':description' => $description,
                ':priority' => strtolower($priority)
            ]);

            updateStatusTracking($conn, $employee_id, '', 'Pending');
            $success = '✅ Ticket created successfully! Ticket Number: ' . $ticket_number;

        } catch (PDOException $e) {
            $error = '❌ Failed to create ticket. Error: ' . $e->getMessage();
        }
    } else {
        $error = '⚠️ Please fill in all required fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Ticket - HR System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <h1>CREATE NEW TICKET</h1>
            <div class="user-info">
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
        
        <div class="form-container">
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="category">Category *</label>
                    <select id="category" name="category" class="form-control" required>
                        <option value="">Select a category</option>
                        <option value="1">Equipment Request</option>
                        <option value="2">Leave Request</option>
                        <option value="3">Payroll Query</option>
                        <option value="4">Benefits Inquiry</option>
                        <option value="5">IT Support</option>
                        <option value="6">General</option>
                        <option value="7">ID Lost</option>
                    </select>
                    <small>Choose the category that best matches your concern or request</small>
                </div>
                
                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="Brief summary of your concern" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" class="form-control" rows="6" placeholder="Provide detailed information about your concern or request" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="priority">Priority</label>
                    <select id="priority" name="priority" class="form-control">
                        <option value="Low">Low</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Submit Ticket</button>
                    <a href="tickets.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
