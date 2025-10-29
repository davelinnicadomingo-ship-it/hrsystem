<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$db = new Database();
$conn = $db->connect();

// ✅ Use the correct employee fetch function
$employee = getEmployeeData($conn, $_SESSION['employee_id']);

// ✅ Ensure only HR admin can access
if ($employee['role'] !== 'hr_admin') {
    header('Location: dashboard.php');
    exit();
}

// ✅ Fetch all employees with ticket statistics (adjusted for your database)
$stmt = $conn->prepare("
    SELECT e.*, 
           COUNT(t.id) AS total_tickets,
           SUM(CASE WHEN LOWER(t.status) = 'pending' THEN 1 ELSE 0 END) AS pending_tickets,
           SUM(CASE WHEN LOWER(t.status) = 'resolved' THEN 1 ELSE 0 END) AS resolved_tickets
    FROM employees e
    LEFT JOIN tickets t ON e.employee_id = t.employee_id
    WHERE e.role = 'employee'
    GROUP BY e.employee_id
    ORDER BY e.full_name
");
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Monitoring - HR System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/hr_sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <h1>EMPLOYEE MONITORING</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($employee['full_name']); ?></span>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
        
        <div class="employees-grid">
            <?php if (empty($employees)): ?>
                <p class="no-employees">No employees found.</p>
            <?php else: ?>
                <?php foreach ($employees as $emp): ?>
                    <div class="employee-card">
                        <div class="employee-header">
                            <img src="assets/images/avatar.png" alt="Avatar" class="employee-avatar">
                            <div class="employee-info">
                                <h3><?php echo htmlspecialchars($emp['full_name']); ?></h3>
                                <p>ID: <?php echo htmlspecialchars($emp['employee_id']); ?></p>
                                <p>Dept: <?php echo htmlspecialchars($emp['department']); ?></p>
                            </div>
                        </div>
                        <div class="employee-stats">
                            <div class="stat-mini">
                                <span class="stat-label">Total Tickets</span>
                                <span class="stat-number"><?php echo $emp['total_tickets']; ?></span>
                            </div>
                            <div class="stat-mini">
                                <span class="stat-label">Pending</span>
                                <span class="stat-number stat-warning"><?php echo $emp['pending_tickets']; ?></span>
                            </div>
                            <div class="stat-mini">
                                <span class="stat-label">Resolved</span>
                                <span class="stat-number stat-success"><?php echo $emp['resolved_tickets']; ?></span>
                            </div>
                        </div>
                        <div class="employee-contact">
                            <p>📧 <?php echo htmlspecialchars($emp['email']); ?></p>
                            <p>📱 <?php echo htmlspecialchars($emp['phone_number'] ?? 'N/A'); ?></p>
                        </div>
                        <div class="employee-actions">
                            <a href="hr_employee_tickets.php?employee_id=<?php echo urlencode($emp['employee_id']); ?>" class="btn btn-sm btn-primary">View Tickets</a>
                            <button onclick="sendNotification('<?php echo $emp['employee_id']; ?>')" class="btn btn-sm btn-secondary">Send Notification</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
    function sendNotification(employeeId) {
        alert('Notification feature will be implemented with email/SMS integration for Employee ID: ' + employeeId);
    }
    </script>
</body>
</html>
