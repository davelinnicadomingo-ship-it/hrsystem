<style>
    .admin-profile {
        text-align: center;
        padding: 1.5rem 1rem;
    }

    .admin-avatar-img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 0.75rem;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        border: 3px solid #fff;
    }

    .admin-name {
        color: #fff;
        font-weight: 600;
        font-size: 0.95rem;
        margin: 0;
    }
</style>

<?php
$db = new Database();
$conn = $db->connect();
$employee_id = $_SESSION['employee_id'];

$stmt = $conn->prepare("SELECT * FROM employees WHERE employee_id = :id");
$stmt->execute(['id' => $employee_id]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

$photoPath = (!empty($employee['photo']) && file_exists($employee['photo'])) 
    ? $employee['photo'] 
    : 'assets/images/default.png';
?>

<div class="sidebar">
    <div class="sidebar-header">
        <h2>Trusting Social AI Philippines</h2>
        <div class="admin-profile">
            <img src="<?php echo htmlspecialchars($photoPath); ?>" alt="Employee" class="admin-avatar-img">
            <h6 class="admin-name"><?php echo htmlspecialchars($employee['full_name']); ?></h6>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="hr_dashboard.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'hr_dashboard.php' ? 'active' : ''; ?>">
            <span class="nav-icon"><i class="bi bi-house-door"></i></span> Dashboard
        </a>
        <a href="hr_employees.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'hr_employees.php' ? 'active' : ''; ?>">
            <span class="nav-icon"><i class="bi bi-people"></i></span> Employee Monitoring
        </a>
        <a href="hr_ticket_detail.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'hr_ticket_detail.php' ? '' : ''; ?>">
            <span class="nav-icon"><i class="bi bi-ticket"></i></span> Tickets Management
        </a>
        <a href="hr_notifications.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'hr_notifications.php' ? 'active' : ''; ?>">
            <span class="nav-icon"><i class="bi bi-bell"></i></span> Notifications
        </a>
    
    <div class="sidebar-footer">
        <ul class="nav flex-column">
            <a href="hr_settings.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'hr_settings.php' ? 'active' : ''; ?>">
            <span class="nav-icon"><i class="bi bi-gear"></i></span> Settings
        </a>
        <a href="logout.php" class="nav-item">
            <span class="nav-icon"><i class="bi bi-box-arrow-right"></i></span> Logout </a>
        </ul>
        </nav>
    </div>
</div>
