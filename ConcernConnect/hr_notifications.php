<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php'; // ✅ make sure helper functions are loaded

requireLogin();

$db = new Database();
$conn = $db->connect();

// ✅ Use getEmployeeData instead of getUserData
$user = getEmployeeData($conn, $_SESSION['employee_id']);

// ✅ Only HR admin can access
if (!$user || $user['role'] !== 'hr_admin') {
    header('Location: dashboard.php');
    exit();
}

if (isset($_POST['send_notification'])) {
    $employee_id = $_POST['employee_id'] ?? '';
    $message = $_POST['message'] ?? '';
    $notification_type = $_POST['notification_type'] ?? 'general';
    $send_email = isset($_POST['send_email']) ? 1 : 0;
    $send_sms = isset($_POST['send_sms']) ? 1 : 0;
    
    if ($employee_id && $message) {
        $sent_via = [];
        if ($send_email) $sent_via[] = 'email';
        if ($send_sms) $sent_via[] = 'sms';
        
        $stmt = $conn->prepare("INSERT INTO notifications (employee_id, notification_type, message, sent_via) VALUES (:employee_id, :type, :message, :sent_via)");
        $stmt->execute([
            'employee_id' => $employee_id,
            'type' => $notification_type,
            'message' => $message,
            'sent_via' => implode(',', $sent_via)
        ]);
        
        $success = 'Notification sent successfully!';
    }
}

// ✅ Fetch all employees from employees table
$stmt = $conn->prepare("SELECT * FROM employees WHERE role = 'employee' ORDER BY full_name");
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Get notification history (join with employees table)
$stmt = $conn->prepare("
    SELECT n.*, e.full_name, e.employee_id 
    FROM notifications n 
    JOIN employees e ON n.employee_id = e.employee_id 
    ORDER BY n.created_at DESC 
    LIMIT 100
");
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - HR System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/hr_sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <h1>NOTIFICATIONS MANAGEMENT</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($user['full_name']); ?></span>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
        
        <div class="notification-container">
            <div class="notification-form-card">
                <h2>Send Notification</h2>
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Select Employee</label>
                        <select name="employee_id" class="form-control" required>
                            <option value="">Choose an employee...</option>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?php echo $emp['id']; ?>"><?php echo htmlspecialchars($emp['full_name'] . ' (' . $emp['employee_id'] . ')'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Notification Type</label>
                        <select name="notification_type" class="form-control">
                            <option value="general">General</option>
                            <option value="announcement">Announcement</option>
                            <option value="reminder">Reminder</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" class="form-control" rows="4" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="send_email" checked> Send Email Notification
                        </label>
                        <label>
                            <input type="checkbox" name="send_sms"> Send SMS Notification
                        </label>
                    </div>
                    
                    <button type="submit" name="send_notification" class="btn btn-primary">Send Notification</button>
                </form>
            </div>
            
            <div class="notification-history">
                <h2>Notification History</h2>
                <div class="notifications-list">
                    <?php foreach ($notifications as $notif): ?>
                        <div class="notification-item">
                            <div class="notification-header">
                                <strong><?php echo htmlspecialchars($notif['full_name']); ?> (<?php echo htmlspecialchars($notif['employee_id']); ?>)</strong>
                                <span class="notification-date"><?php echo date('M d, Y g:ia', strtotime($notif['created_at'])); ?></span>
                            </div>
                            <p><?php echo htmlspecialchars($notif['message']); ?></p>
                            <div class="notification-meta">
                                <span class="badge badge-info"><?php echo ucfirst($notif['notification_type']); ?></span>
                                <span class="badge badge-secondary">Sent via: <?php echo htmlspecialchars($notif['sent_via'] ?? 'N/A'); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
