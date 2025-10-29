<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$db = new Database();
$conn = $db->connect();

// ✅ Get logged-in employee data
$employee = getEmployeeData($conn, $_SESSION['employee_id']);

if (!$employee || $employee['role'] !== 'hr_admin') {
    header('Location: dashboard.php');
    exit();
}

// ✅ Ticket statistics
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tickets");
$stmt->execute();
$total_tickets = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tickets WHERE LOWER(status) = 'pending'");
$stmt->execute();
$pending_tickets = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tickets WHERE LOWER(status) = 'in progress'");
$stmt->execute();
$in_progress_tickets = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tickets WHERE LOWER(status) = 'resolved'");
$stmt->execute();
$resolved_tickets = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tickets WHERE LOWER(status) = 'closed'");
$stmt->execute();
$closed_tickets = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// ✅ Filters
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$category_filter = $_GET['category'] ?? '';

// ✅ Adjusted for employees table (was users)
$query = "
    SELECT t.*, e.full_name, e.employee_id, e.department
    FROM tickets t
    JOIN employees e ON t.employee_id = e.employee_id
    WHERE 1=1
";
$params = [];

if ($search) {
    $query .= " AND (LOWER(t.title) LIKE :search OR LOWER(t.ticket_number) LIKE :search OR LOWER(e.full_name) LIKE :search)";
    $params['search'] = '%' . strtolower($search) . '%';
}

if ($status_filter && $status_filter !== 'all') {
    $query .= " AND LOWER(t.status) = :status";
    $params['status'] = strtolower($status_filter);
}

if ($category_filter && $category_filter !== 'all') {
    $query .= " AND t.category_id = :category";
    $params['category_id'] = $category_filter;
}

$query .= " ORDER BY t.created_at DESC LIMIT 50";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Management Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/hr_sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <h1>HR MANAGEMENT DASHBOARD</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($employee['full_name']); ?></span>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
        
        <div class="hr-stats-grid">
            <div class="stat-card">
                <div class="stat-label">TOTAL TICKETS</div>
                <div class="stat-value"><?php echo $total_tickets; ?></div>
            </div>
            <div class="stat-card stat-warning">
                <div class="stat-label">PENDING</div>
                <div class="stat-value"><?php echo $pending_tickets; ?></div>
            </div>
            <div class="stat-card stat-info">
                <div class="stat-label">IN PROGRESS</div>
                <div class="stat-value"><?php echo $in_progress_tickets; ?></div>
            </div>
            <div class="stat-card stat-success">
                <div class="stat-label">RESOLVED</div>
                <div class="stat-value"><?php echo $resolved_tickets; ?></div>
            </div>
            <div class="stat-card stat-dark">
                <div class="stat-label">CLOSED</div>
                <div class="stat-value"><?php echo $closed_tickets; ?></div>
            </div>
        </div>
        
        <div class="hr-filters">
            <input type="text" id="search-input" placeholder="Search tickets..." value="<?php echo htmlspecialchars($search); ?>" class="search-input">
            <select id="status-filter" class="filter-select">
                <option value="all">All Status</option>
                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="in progress" <?php echo $status_filter === 'in progress' ? 'selected' : ''; ?>>In Progress</option>
                <option value="resolved" <?php echo $status_filter === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                <option value="closed" <?php echo $status_filter === 'closed' ? 'selected' : ''; ?>>Closed</option>
            </select>
            <select id="category-filter" class="filter-select">
                <option value="all">All Categories</option>
                <option value="Equipment Request" <?php echo $category_filter === 'Equipment Request' ? 'selected' : ''; ?>>Equipment Request</option>
                <option value="Leave Request" <?php echo $category_filter === 'Leave Request' ? 'selected' : ''; ?>>Leave Request</option>
                <option value="Payroll Query" <?php echo $category_filter === 'Payroll Query' ? 'selected' : ''; ?>>Payroll Query</option>
                <option value="Benefits Inquiry" <?php echo $category_filter === 'Benefits Inquiry' ? 'selected' : ''; ?>>Benefits Inquiry</option>
                <option value="IT Support" <?php echo $category_filter === 'IT Support' ? 'selected' : ''; ?>>IT Support</option>
            </select>
        </div>
        
        <div class="hr-tickets-list">
            <?php if (empty($tickets)): ?>
                <p class="no-tickets">No tickets found.</p>
            <?php else: ?>
                <?php foreach ($tickets as $ticket): ?>
                    <div class="hr-ticket-item">
                        <div class="hr-ticket-header">
                            <h3><?php echo htmlspecialchars($ticket['title']); ?></h3>
                            <span class="badge <?php echo getStatusClass($ticket['status']); ?>">
                                <?php echo strtoupper($ticket['status']); ?>
                            </span>
                        </div>
                        <p><?php echo htmlspecialchars(substr($ticket['description'], 0, 150)); ?>...</p>
                        <div class="hr-ticket-meta">
                            <span class="ticket-number"><?php echo htmlspecialchars($ticket['ticket_number']); ?></span>
                            <span><?php echo htmlspecialchars($ticket['category_id']); ?></span>
                            <span class="badge <?php echo getPriorityClass($ticket['priority']); ?>">
                                <?php echo htmlspecialchars($ticket['priority']); ?>
                            </span>
                            <span>📅 <?php echo date('M d, Y', strtotime($ticket['created_at'])); ?></span>
                        </div>
                        <div class="hr-ticket-employee">
                            <strong>Employee:</strong>
                            <?php echo htmlspecialchars($ticket['full_name']); ?> 
                            (<?php echo htmlspecialchars($ticket['employee_id']); ?>) - 
                            <?php echo htmlspecialchars($ticket['department']); ?>
                        </div>
                        <div class="hr-ticket-actions">
                            <form method="POST" action="hr_ticket_manage.php" style="display: inline;">
                                <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                <select name="new_status" class="form-control-sm">
                                    <option value="">Change Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Resolved">Resolved</option>
                                    <option value="Closed">Closed</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm btn-primary">Update</button>
                            </form>
                            <select name="assign_to" class="form-control-sm" onchange="assignTicket(<?php echo $ticket['id']; ?>, this.value)">
                                <option value="">Assign To</option>
                                <option value="HR Administrator">HR Administrator</option>
                                <option value="HR Manager">HR Manager</option>
                                <option value="Payroll Team">Payroll Team</option>
                                <option value="IT Support">IT Support</option>
                            </select>
                            <a href="hr_ticket_detail.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-secondary">View & Respond</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="assets/js/hr_dashboard.js"></script>  
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
