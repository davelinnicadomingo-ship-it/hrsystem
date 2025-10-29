<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$db = new Database();
$conn = $db->connect();

// ✅ Fetch current logged-in HR admin
$employee = getEmployeeData($conn, $_SESSION['employee_id']);

if ($employee['role'] !== 'hr_admin') {
    header('Location: dashboard.php');
    exit();
}

// ✅ Get ticket ID
$ticket_id = $_GET['id'] ?? 0;
$success = '';

// ✅ Fetch ticket details (joined with employee info)
$stmt = $conn->prepare("
    SELECT t.*, e.full_name, e.employee_id, e.email, e.department 
    FROM tickets t 
    JOIN employees e ON t.employee_id = e.employee_id 
    WHERE t.id = :id
");
$stmt->execute(['id' => $ticket_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    header('Location: hr_dashboard.php');
    exit();
}

// ✅ Handle HR response submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_response'])) {
    $response_text = trim($_POST['response_text'] ?? '');
    $is_internal = isset($_POST['is_internal']) ? 1 : 0;
    $send_email = isset($_POST['send_email']) ? 1 : 0;
    $send_sms = isset($_POST['send_sms']) ? 1 : 0;

    if ($response_text !== '') {
        // Insert response
        $stmt = $conn->prepare("
            INSERT INTO ticket_responses (ticket_id, employee_id, response_text, is_internal) 
            VALUES (:ticket_id, :employee_id, :response_text, :is_internal)
        ");
        $stmt->execute([
            'ticket_id' => $ticket_id,
            'employee_id' => $_SESSION['employee_id'],
            'response_text' => $response_text,
            'is_internal' => $is_internal
        ]);

        // Notify employee (only if not internal)
        if (!$is_internal) {
            $notification_message = "HR responded to your ticket: " . $ticket['ticket_number'];
            $sent_via = [];
            if ($send_email) $sent_via[] = 'email';
            if ($send_sms) $sent_via[] = 'sms';

            $stmt = $conn->prepare("
                INSERT INTO notifications (employee_id, ticket_id, notification_type, message, sent_via) 
                VALUES (:employee_id, :ticket_id, :type, :message, :sent_via)
            ");
            $stmt->execute([
                'employee_id' => $ticket['employee_id'],
                'ticket_id' => $ticket_id,
                'type' => 'ticket_response',
                'message' => $notification_message,
                'sent_via' => implode(',', $sent_via)
            ]);
        }

        $success = 'Response sent successfully!';
    }
}

// ✅ Fetch ticket response history
$stmt = $conn->prepare("
    SELECT r.*, e.full_name 
    FROM ticket_responses r 
    JOIN employees e ON r.employee_id = e.employee_id 
    WHERE r.ticket_id = :ticket_id 
    ORDER BY r.created_at DESC
");
$stmt->execute(['ticket_id' => $ticket_id]);
$responses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Management - HR System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/hr_sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h1>TICKET MANAGEMENT</h1>
            <div class="user-info">
                <a href="hr_dashboard.php" class="btn btn-secondary btn-sm">Back to Dashboard</a>
            </div>
        </div>

        <div class="ticket-detail-container">
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="ticket-detail-card">
                <div class="ticket-detail-header">
                    <div>
                        <h2><?php echo htmlspecialchars($ticket['title']); ?></h2>
                        <p class="ticket-employee-info">
                            <strong>Employee:</strong> <?php echo htmlspecialchars($ticket['full_name']); ?> (<?php echo htmlspecialchars($ticket['employee_id']); ?>)<br>
                            <strong>Department:</strong> <?php echo htmlspecialchars($ticket['department']); ?><br>
                            <strong>Email:</strong> <?php echo htmlspecialchars($ticket['email']); ?>
                        </p>
                    </div>
                    <span class="badge <?php echo getStatusClass($ticket['status']); ?>"><?php echo strtoupper($ticket['status']); ?></span>
                </div>

                <div class="ticket-meta-info">
                    <div class="meta-item">
                        <strong>Ticket Number</strong>
                        <?php echo htmlspecialchars($ticket['ticket_number']); ?>
                    </div>
                    <div class="meta-item">
                        <strong>Category</strong>
                        <?php echo htmlspecialchars($ticket['category_id']); ?>
                    </div>
                    <div class="meta-item">
                        <strong>Priority</strong>
                        <span class="badge <?php echo getPriorityClass($ticket['priority']); ?>"><?php echo htmlspecialchars($ticket['priority']); ?></span>
                    </div>
                    <div class="meta-item">
                        <strong>Assigned To</strong>
                        <?php echo htmlspecialchars($ticket['assigned_to'] ?? 'Unassigned'); ?>
                    </div>
                </div>

                <div class="ticket-description">
                    <h3>Employee's Request</h3>
                    <p><?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>
                </div>

                <div class="ticket-responses">
                    <h3>Response History</h3>
                    <?php if (empty($responses)): ?>
                        <p>No responses yet.</p>
                    <?php else: ?>
                        <?php foreach ($responses as $response): ?>
                            <div class="response-item <?php echo $response['is_internal'] ? 'internal-response' : ''; ?>">
                                <div class="response-header">
                                    <strong><?php echo htmlspecialchars($response['full_name']); ?></strong>
                                    <span><?php echo date('M d, Y g:ia', strtotime($response['created_at'])); ?></span>
                                    <?php if ($response['is_internal']): ?>
                                        <span class="badge badge-warning">Internal Note</span>
                                    <?php endif; ?>
                                </div>
                                <p><?php echo nl2br(htmlspecialchars($response['response_text'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="hr-response-form">
                    <h3>Send Response</h3>
                    <form method="POST" action="">
                        <div class="form-group">
                            <textarea name="response_text" class="form-control" rows="6" placeholder="Type your response to the employee..." required></textarea>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="is_internal"> Internal Note (not visible to employee)
                            </label>
                        </div>
                        <div class="form-group">
                            <h4>Send Notification</h4>
                            <label>
                                <input type="checkbox" name="send_email" checked> Send Email Notification
                            </label>
                            <label>
                                <input type="checkbox" name="send_sms"> Send SMS Notification
                            </label>
                        </div>
                        <button type="submit" name="send_response" class="btn btn-primary">Send Response</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
