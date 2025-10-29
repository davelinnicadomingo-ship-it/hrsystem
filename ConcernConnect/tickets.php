<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$db = new Database();
$conn = $db->connect();

$user = getEmployeeData($conn, $_SESSION['employee_id']);
$stats = getTicketStats($conn, $_SESSION['user_id']);

// Initialize variables to avoid undefined warnings
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

// Handle AJAX requests
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    $query = "SELECT * FROM tickets WHERE employee_id = :employee_id";
    $params = ['employee_id' => $_SESSION['employee_id']];
    
    if ($filter !== 'all') {
        $query .= " AND LOWER(status) = :status";
        $params['status'] = strtolower($filter);
    }
    
    if ($search) {
        $query .= " AND (LOWER(title) LIKE :search OR LOWER(ticket_number) LIKE :search)";
        $params['search'] = '%' . strtolower($search) . '%';
    }
    
    $query .= " ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($tickets);
    exit;
}

// Initial page load - fetch all tickets
$query = "SELECT * FROM tickets WHERE employee_id = :employee_id ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute(['employee_id' => $_SESSION['employee_id']]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tickets - HR System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<style>
/* Base Layout */
body {
  font-family: "Segoe UI", Roboto, Arial, sans-serif;
  background: linear-gradient(to left, #fcf0ff, #ffffff);
  color: #333;
  margin: 0;
  padding: 0;
}

/* Main Content */
.main-content {
  padding: 2rem 3rem;
  background: #fafafa;
  border-radius: 30px 0 0 30px;
}

/* Stats Section */
.ticket-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1rem;
  margin: 2rem 0;
}

.stat-card {
  background: #ffffff;
  padding: 1.5rem;
  border-radius: 16px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  text-align: center;
  transition: transform 0.2s;
}
.stat-card:hover {
  transform: translateY(-4px);
}

.stat-label {
  font-size: 0.9rem;
  color: #777;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
.stat-value {
  font-size: 1.8rem;
  font-weight: 600;
  margin-top: 0.5rem;
}

/* Filters */
.ticket-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 2rem;
  align-items: center;
}

.search-input, .filter-select {
  padding: 0.75rem 1rem;
  border-radius: 12px;
  border: 1px solid #ccc;
  background: #fff;
  flex: 1;
  max-width: 250px;
}

.search-input:focus, .filter-select:focus {
  outline: none;
  border-color: #7c4dff;
  box-shadow: 0 0 0 2px rgba(124, 77, 255, 0.1);
}

/* Ticket List */
.tickets-list {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}

.ticket-item {
  background: #ffffff;
  border-radius: 18px;
  padding: 1.5rem 2rem;
  box-shadow: 0 3px 8px rgba(0,0,0,0.05);
  display: flex;
  justify-content: space-between;
  align-items: start;
  transition: all 0.2s;
}
.ticket-item:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}

.ticket-info h3 {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 0.3rem;
}

.ticket-meta {
  font-size: 0.85rem;
  color: #666;
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem;
  margin-top: 0.5rem;
}

.ticket-status {
  text-align: right;
}

.ticket-status .badge {
  font-size: 0.8rem;
  padding: 0.5em 0.8em;
  border-radius: 10px;
}

/* Responsive */
@media (max-width: 992px) {
  .page-container {
    grid-template-columns: 1fr;
  }
  .main-content {
    border-radius: 0;
    padding: 1.5rem;
  }
  .ticket-item {
    flex-direction: column;
    align-items: stretch;
  }
  .ticket-status {
    text-align: left;
    margin-top: 1rem;
  }
}
</style>
<body>
    <?php include_once 'includes/sidebar.php'; ?>

    <div class="main-content">
    <!-- Top Header -->
    <?php include_once 'includes/employee_header.php'; ?>
    
        <div class="ticket-header m-4">
            <a href="create_ticket.php" class="btn btn-primary">Create New Ticket</a>
        </div>
        
        <div class="ticket-stats">
            <div class="stat-card">
                <div class="stat-label">TOTAL TICKETS</div>
                <div class="stat-value"><?php echo $stats['total']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">PENDING</div>
                <div class="stat-value"><?php echo $stats['pending']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">IN PROGRESS</div>
                <div class="stat-value"><?php echo $stats['in_progress']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">RESOLVED</div>
                <div class="stat-value"><?php echo $stats['resolved']; ?></div>
            </div>
        </div>
        
        <div class="ticket-filters">
            <input type="text" id="search-tickets" placeholder="Search tickets..." value="<?php echo htmlspecialchars($search); ?>" class="search-input">
            <select id="filter-status" class="filter-select">
                <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                <option value="pending" <?php echo $filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="in progress" <?php echo $filter === 'in progress' ? 'selected' : ''; ?>>In Progress</option>
                <option value="resolved" <?php echo $filter === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                <option value="closed" <?php echo $filter === 'closed' ? 'selected' : ''; ?>>Closed</option>
            </select>
        </div>
        
        <div class="tickets-list">
            <?php if (empty($tickets)): ?>
                <p class="no-tickets">No tickets found.</p>
            <?php else: ?>
                <?php foreach ($tickets as $ticket): ?>
                    <div class="ticket-item">
                        <div class="ticket-info">
                            <h3>
                                <a href="ticket_detail.php?id=<?php echo $ticket['id']; ?>" style="color: #333; text-decoration: none;">
                                    <?php echo htmlspecialchars($ticket['title']); ?>
                                </a>
                            </h3>
                            <p><?php echo htmlspecialchars(substr($ticket['description'], 0, 100)); ?>...</p>
                            <div class="ticket-meta">
                                <span class="ticket-number"><?php echo htmlspecialchars($ticket['ticket_number']); ?></span>
                                <span class="badge <?php echo getPriorityClass($ticket['priority']); ?>"><?php echo htmlspecialchars($ticket['priority']); ?></span>
                                <span class="ticket-category"><?php echo htmlspecialchars($ticket['category_id']); ?></span>
                                <span class="ticket-date"><?php echo date('M d, Y', strtotime($ticket['created_at'])); ?></span>
                            </div>
                            <div class="ticket-assigned">Assigned to: <?php echo htmlspecialchars($ticket['assigned_to']); ?></div>
                        </div>
                        <div class="ticket-status">
                            <span class="badge <?php echo getStatusClass($ticket['status']); ?>"><?php echo strtoupper($ticket['status']); ?></span>
                            <a href="ticket_detail.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-secondary" style="margin-top: 10px;">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <button class="chatbot-toggle" id="chatbot-toggle">💬 Help</button>
    <div class="chatbot-window" id="chatbot-window">
        <div class="chatbot-header">
            <span>🤖 HR Assistant</span>
            <button id="close-chatbot">✕</button>
        </div>
        <div class="chatbot-messages" id="chatbot-messages"></div>
        <div class="chatbot-input">
            <input type="text" id="chatbot-input" placeholder="Type your question...">
            <button id="send-message">Send</button>
        </div>
    </div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/dashboard.js?v=<?php echo time(); ?>"></script>
<script src="assets/js/chatbot.js?v=<?php echo time(); ?>"></script>
<script src="assets/js/tickets.js?v=<?php echo time(); ?>"></script>


</body>
</html>
