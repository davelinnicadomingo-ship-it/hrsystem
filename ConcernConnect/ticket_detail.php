<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$db = new Database();
$conn = $db->connect();

$ticket_id = $_GET['id'] ?? 0;
$user = getEmployeeData($conn, $_SESSION['employee_id']);
$success = '';
$error = '';

$stmt = $conn->prepare("SELECT * FROM tickets WHERE id = :id AND employee_id = :employee_id");
$stmt->execute(['id' => $ticket_id, 'employee_id' => $_SESSION['employee_id']]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    header('Location: tickets.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'] ?? '';
    
    if ($new_status) {
        $old_status = $ticket['status'];
        
        $stmt = $conn->prepare("UPDATE tickets SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
        $stmt->execute(['status' => $new_status, 'id' => $ticket_id]);
        
        updateStatusTracking($conn, $_SESSION['employee_id'], $old_status, $new_status);
        
        $success = 'Ticket status updated successfully!';
        
        $stmt = $conn->prepare("SELECT * FROM tickets WHERE id = :id");
        $stmt->execute(['id' => $ticket_id]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Details - HR System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <h1>TICKET DETAILS</h1>
            <div class="user-info">
                <a href="tickets.php" class="btn btn-secondary">Back to Tickets</a>
            </div>
        </div>
        
        <div class="ticket-detail-container">
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="ticket-detail-card">
                <div class="ticket-detail-header">
                    <h2><?php echo htmlspecialchars($ticket['title']); ?></h2>
                    <span class="badge <?php echo getStatusClass($ticket['status']); ?>"><?php echo strtoupper($ticket['status']); ?></span>
                </div>
                
                <div class="ticket-meta-info">
                    <div class="meta-item">
                        <strong>Ticket Number:</strong> <?php echo htmlspecialchars($ticket['ticket_number']); ?>
                    </div>
                    <div class="meta-item">
                        <strong>Category:</strong> <?php echo htmlspecialchars($ticket['category_id']); ?>
                    </div>
                    <div class="meta-item">
                        <strong>Priority:</strong> 
                        <span class="badge <?php echo getPriorityClass($ticket['priority']); ?>"><?php echo htmlspecialchars($ticket['priority']); ?></span>
                    </div>
                    <div class="meta-item">
                        <strong>Created:</strong> <?php echo date('M d, Y g:ia', strtotime($ticket['created_at'])); ?>
                    </div>
                    <div class="meta-item">
                        <strong>Last Updated:</strong> <?php echo date('M d, Y g:ia', strtotime($ticket['updated_at'])); ?>
                    </div>
                    <div class="meta-item">
                        <strong>Assigned To:</strong> <?php echo htmlspecialchars($ticket['assigned_to']); ?>
                    </div>
                </div>
                
                <div class="ticket-description">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>
                </div>
                
                <div class="ticket-actions">
                    <h3>Update Status</h3>
                    <form method="POST" action="">
                        <div class="status-update-form">
                            <select name="status" class="form-control">
                                <option value="Pending" <?php echo $ticket['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="In Progress" <?php echo $ticket['status'] == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="Resolved" <?php echo $ticket['status'] == 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                                <option value="Closed" <?php echo $ticket['status'] == 'Closed' ? 'selected' : ''; ?>>Closed</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
