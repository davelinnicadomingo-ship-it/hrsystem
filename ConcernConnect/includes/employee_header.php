<style>
    .header {
  background: #fff;
  border: 1px solid #d0d0d0;
  padding: 1rem 1.5rem;
  border-radius: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.header h4 {
  margin: 0;
  font-weight: 600;
  color: #333;
}

.header .dropdown img {
  border: 2px solid #ccc;
}

.dropdown-menu.show { 
  display: block !important; 
  z-index: 9999 !important;
}

/* === Employee Dropdown Styling === */
.dropdown .btn.dropdown-toggle {
  color: #333;
  font-weight: 500;
  border: none;
  background: transparent;
  padding: 6px 10px;
}

.dropdown .btn.dropdown-toggle:hover {
  color: #5f4ef7;
}

.dropdown .btn img {
  border-radius: 50%;
  border: 2px solid #eee;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.dropdown-menu {
  border-radius: 12px;
  border: none;
  box-shadow: 0 4px 16px rgba(0,0,0,0.1);
  min-width: 180px;
  background: linear-gradient(180deg, #ffffff 0%, #f9f8ff 100%);
}

.dropdown-item {
  font-size: 15px;
  color: #333;
  padding: 10px 15px;
  transition: background 0.2s ease, color 0.2s ease;
}

.dropdown-item:hover {
  background-color: #f2f2ff;
  color: #5f4ef7;
}

.dropdown-divider {
  margin: 0.5rem 0;
}

.dropdown-item.text-danger:hover {
  background-color: #ffeaea;
  color: #dc3545;
}
</style>
<?php
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Detect current page name
$currentPage = basename($_SERVER['PHP_SELF'], ".php");

// Map readable titles
$pageNames = [
    'dashboard'      => 'Dashboard',
    'profile'        => 'My Profile',
    'tickets'        => 'Tickets',
    'chatbot'        => 'Chat with Bot',
    'status_update'  => 'Status Update',
    'settings'       => 'Settings'
];

// Use a friendly fallback if not in list
$pageTitle = $pageNames[$currentPage] ?? ucfirst($currentPage);
?>

<div class="header mb-4 d-flex justify-content-between align-items-center">
  <h4 class="m-4">
    <?php if ($currentPage === 'dashboard'): ?>
      <?php echo $pageTitle; ?> |
      Hello <strong><?php echo $_SESSION['full_name']; ?></strong>,
      <span id="greeting">Good Day!</span>
    <?php else: ?>
      <?php echo $pageTitle; ?>
    <?php endif; ?>
  </h4>

  <div class="d-flex align-items-center">
    <button class="btn btn-link d-lg-none" id="sidebarToggle">
      <i class="bi bi-list"></i>
    </button>

    <div class="header-actions ms-auto d-flex align-items-center">
      <button class="btn btn-link me-3"><i class="bi bi-bell"></i></button>

      <div class="dropdown">
        <button class="btn btn-link dropdown-toggle d-flex align-items-left" type="button" data-bs-toggle="dropdown">
          <img src="<?php echo htmlspecialchars($photoPath); ?>" alt="Employee"
               class="rounded-circle me-2" width="32" height="32">
          <?php echo $_SESSION['full_name']; ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="profile.php">Profile</a></li>
          <li><a class="dropdown-item" href="settings.php">Settings</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="login.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<script>
// ===== DROPDOWN CLOSE ON OUTSIDE CLICK =====
document.addEventListener('click', (e) => {
  const dropdown = document.querySelector('.dropdown');
  if (dropdown && !dropdown.contains(e.target)) {
    const menu = dropdown.querySelector('.dropdown-menu');
    if (menu && menu.classList.contains('show')) {
      menu.classList.remove('show');
    }
  }
});

// ===== TIME-BASED GREETING (only on dashboard) =====
<?php if ($currentPage === 'dashboard'): ?>
function updateGreeting() {
  const now = new Date();
  const hour = now.getHours();
  let greetingText = "Good Day";

  if (hour >= 5 && hour < 12) greetingText = "Good Morning";
  else if (hour >= 12 && hour < 18) greetingText = "Good Afternoon";
  else greetingText = "Good Evening";

  document.getElementById('greeting').textContent = greetingText + "!";
}

updateGreeting();
<?php endif; ?>
</script>
