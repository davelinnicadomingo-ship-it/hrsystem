<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$db = new Database();
$conn = $db->connect();

$user = getEmployeeData($conn, $_SESSION['employee_id']);
$stats = getTicketStats($conn, $_SESSION['employee_id']);

// make $status_counts available to match your previous naming in design
$status_counts = $stats;

// --- tickets query you supplied ---
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Status Update</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    /* ===== General Layout ===== */

.main-container {
  width: 100%;
  max-width: 1100px; /* controls central width */
  margin: 2rem auto;
  background: white;
  border-radius: 15px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
  padding: 2rem;
}

    .status-update-container {
      max-width: 1000px;
      margin: 3rem auto;
      background: #fff;
      border-radius: 20px;
      padding: 2.5rem;
      box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    .status-update-container h2 {
      text-align: center;
      font-weight: 700;
      margin-bottom: 2rem;
      color: #1e293b;
      letter-spacing: 0.5px;
    }

    /* ===== Status Cards ===== */
    .status-report-large {
      display: flex;
      justify-content: center;
      gap: 1.5rem;
      margin-bottom: 3rem;
      flex-wrap: wrap;
    }

    .status-item-large {
      flex: 1 1 250px;
      text-align: center;
      padding: 1.4rem 1rem;
      border-radius: 15px;
      color: white;
      box-shadow: 0 6px 12px rgba(0,0,0,0.12);
      transition: transform 0.3s ease;
      min-width: 180px;
    }
    .status-item-large:hover { transform: translateY(-4px); }

    .status-count-large {
      font-size: 2.4rem;
      font-weight: 700;
      margin-bottom: 0.3rem;
    }
    .status-label-large {
      font-size: 0.95rem;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    /* ===== Tickets (timeline style) ===== */
    .tickets-area {
      margin-top: 1.5rem;
      padding-left: 1rem;
      border-left: 3px solid #e5e7eb;
    }

    .ticket-activity {
      display: grid;
      gap: 1rem;
      margin-top: 1rem;
    }

    .ticket-card {
      position: relative;
      background: #f9fafb;
      padding: 1rem 1.25rem;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
      display: flex;
      justify-content: space-between;
      gap: 1rem;
      align-items: flex-start;
    }

    .ticket-left {
      flex: 1;
    }

    .ticket-title {
      font-weight: 600;
      color: #111827;
      margin-bottom: 0.25rem;
      font-size: 1rem;
    }

    .ticket-desc {
      color: #4b5563;
      margin: 0;
      font-size: 0.95rem;
    }

    .ticket-meta {
      margin-top: 0.5rem;
      font-size: 0.85rem;
      color: #6b7280;
      display:flex;
      gap: 0.75rem;
      flex-wrap:wrap;
      align-items:center;
    }

    /* timeline marker (left) */
    .timeline-marker {
      position: absolute;
      left: -1.8rem;
      top: 18px;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      border: 3px solid white;
      box-shadow: 0 0 0 4px #e5e7eb;
    }

    .timeline-marker.pending { 
      background: #ef4444; 
    }

    .timeline-marker.in-progress { 
      background: #f59e0b; 
    }

    .timeline-marker.resolved { 
      background: #10b981; 
    }

    /* right side - status and actions */
    .ticket-right {
      display:flex;
      flex-direction:column;
      align-items:flex-end;
      gap:8px;
      min-width:170px;
    }

    .status-badge {
      display:inline-block;
      padding: 6px 10px;
      border-radius: 12px;
      font-size: 0.8rem;
      color:white;
      font-weight:600;
      text-transform:uppercase;
    }

    .status-badge.pending { 
      background:#ef4444;
    }

    .status-badge['in-progress'] { 
      background:#f59e0b;
    } /* fallback */

    .status-badge.in-progress { 
      background:#f59e0b; 
    }

    .status-badge.resolved { 
      background:#10b981; 
    }

    .status-select {
      border-radius: 8px;
      border: 1px solid #ddd;
      padding: 4px 8px;
      font-size: 0.9rem;
      background: #fff;
    }

    .small-meta {
      font-size:0.82rem;
      color:#6b7280;
    }

    /* responsive */
    @media (max-width: 768px) {
      .ticket-card { flex-direction: column; align-items: stretch; }
      .ticket-right { align-items:flex-start; }
      .timeline-marker { left: -1.2rem; top: 14px; }
      .status-item-large { min-width: 100px; padding: 1rem; }
    }

    /* success indicator */
    .update-toast {
      position: fixed;
      top: 18px;
      right: 18px;
      z-index: 2000;
      padding: 10px 14px;
      border-radius: 10px;
      background: #10b981;
      color: #fff;
      display: none;
      box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }
  </style>
</head>
<body>
  <?php include_once 'includes/sidebar.php'; ?>

<div class="main-content">
    <?php include_once 'includes/employee_header.php'; ?>

      <div class="main-container">
    <div class="status-update-container">
      <h2>Progress Tracking</h2>

      <div class="status-report-large">
        <div class="status-item-large" style="background: #3B82F6;">
          <div class="status-count-large"><?php echo (int)($status_counts['days'] ?? 0); ?></div>
          <div class="status-label-large">Days Active</div>
        </div>
        <div class="status-item-large" style="background: #10B981;">
          <div class="status-count-large"><?php echo (int)($status_counts['completed'] ?? 0); ?></div>
          <div class="status-label-large">Completed Tasks</div>
        </div>
        <div class="status-item-large" style="background: #F59E0B;">
          <div class="status-count-large"><?php echo (int)($status_counts['in_progress'] ?? 0); ?></div>
          <div class="status-label-large">In Progress</div>
        </div>
      </div>

      <!-- Tickets area styled like timeline -->
      <div class="tickets-area">
        <h3 style="text-align:center; margin-bottom:1rem; font-weight:600; color:#1f2937;">Recent Activity</h3>

        <div class="ticket-activity">
          <?php if (empty($tickets)): ?>
            <div class="ticket-card">
              <div class="ticket-left">
                <p class="ticket-desc">No recent tickets found.</p>
              </div>
            </div>
          <?php else: ?>
            <?php foreach ($tickets as $t): 
              // normalize status values for comparison
              $status_raw = strtolower(trim($t['status'] ?? 'pending'));
              $status_key = $status_raw === 'in progress' ? 'in-progress' : ($status_raw === 'resolved' ? 'resolved' : 'pending');
              $marker_class = $status_key;
              // nice short description
              $short_desc = htmlspecialchars(substr($t['description'] ?? '', 0, 140));
            ?>
              <div class="ticket-card" data-ticket-id="<?php echo (int)$t['id']; ?>">
                <div class="timeline-marker <?php echo $marker_class; ?>"></div>

                <div class="ticket-left">
                  <div class="ticket-title">
                    <a href="ticket_detail.php?id=<?php echo $t['id']; ?>" style="color:inherit; text-decoration:none;">
                      <?php echo htmlspecialchars($t['title']); ?>
                    </a>
                  </div>
                  <p class="ticket-desc"><?php echo $short_desc; ?><?php echo strlen($t['description'] ?? '') > 140 ? '...' : ''; ?></p>

                  <div class="ticket-meta">
                    <span class="small-meta"><i class="bi bi-calendar-event"></i> <?php echo date('M d, Y g:ia', strtotime($t['created_at'])); ?></span>
                    <span class="small-meta"><i class="bi bi-hash"></i> <?php echo htmlspecialchars($t['ticket_number']); ?></span>
                    <span class="small-meta"><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($t['assigned_to'] ?: 'Unassigned'); ?></span>
                  </div>
                </div>

                <div class="ticket-right">
                  <!-- visual badge (updated on change) -->
                  <div class="status-badge <?php echo $status_key; ?>" id="badge-<?php echo $t['id']; ?>">
                    <?php echo strtoupper($t['status']); ?>
                  </div>

                  <!-- inline status dropdown -->
                  <select class="status-select" data-id="<?php echo (int)$t['id']; ?>">
                    <option value="Pending" <?php echo $status_raw === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="In Progress" <?php echo $status_raw === 'in progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="Resolved" <?php echo $status_raw === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                  </select>

                  <a href="ticket_detail.php?id=<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-secondary mt-2">View</a>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>

  <div class="update-toast" id="update-toast">Status updated</div>

  <script>
    $(function(){
      // helper to map dropdown value -> marker class + badge class
      function statusKeyFor(value) {
        let s = String(value).toLowerCase().trim();
        if (s === 'in progress' || s === 'in-progress') return 'in-progress';
        if (s === 'resolved') return 'resolved';
        return 'pending';
      }

      function badgeTextFor(value) {
        return String(value).toUpperCase();
      }

      // handle change
      $('.status-select').on('change', function(){
        const select = $(this);
        const ticketId = select.data('id');
        const newStatus = select.val();

        $.ajax({
          url: 'update_ticket_status.php',
          type: 'POST',
          data: { id: ticketId, status: newStatus },
          success: function(resp) {
            // update badge text and classes
            const key = statusKeyFor(newStatus);
            const badge = $('#badge-' + ticketId);
            badge.removeClass('pending in-progress resolved').addClass(key).text(badgeTextFor(newStatus));

            // update timeline marker color
            const card = select.closest('.ticket-card');
            const marker = card.find('.timeline-marker');
            marker.removeClass('pending in-progress resolved').addClass(key);

            // show toast
            $('#update-toast').fadeIn(150).delay(900).fadeOut(250);
          },
          error: function() {
            alert('Error updating status. Please try again.');
          }
        });
      });
    });
  </script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
