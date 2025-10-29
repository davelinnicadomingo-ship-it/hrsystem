<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$db = new Database();
$conn = $db->connect();

$user = getEmployeeData($conn, $_SESSION['employee_id']);
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone_number'] ?? '';

    $stmt = $conn->prepare("UPDATE employees SET email = :email, phone_number = :phone WHERE employee_id = :id");
    $stmt->execute([
        'email' => $email,
        'phone' => $phone,
        'id' => $_SESSION['employee_id']
    ]);

    $success = 'Profile updated successfully!';
    $user = getEmployeeData($conn, $_SESSION['employee_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - HR System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
    <style>
.personal-info-section .card {
  background-color: #fafafa;
  transition: all 0.2s ease-in-out;
}

.personal-info-section .card:hover {
  transform: translateY(-2px);
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.personal-info-section h4 {
  color: #333;
}

.personal-info-section p {
  font-size: 0.95rem;
}
.profile-card {
  width: 420px; /* increase width (try 400–450px for best fit) */
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  padding: 24px;
  margin: 0 20px;
}
body {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Ubuntu, sans-serif;
  background: linear-gradient(to left, #fcf0ffff, #ffffff);
  color: #333;
}

/* Page grid */
.page-container {
  display: grid;
  grid-template-columns: 260px auto;
  min-height: 100vh;
}

/* Main content */
.main-content {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

/* Dashboard main grid */
.dashboard-container {
  display: grid;
  grid-template-columns: 2fr 1.2fr;
  gap: 30px;
  align-items: start;
}


/* Shared cards */
.shift-box, .tickets-box, .chat-section {
  background: #fff;
  border-radius: 30px;
  padding: 25px;
  box-shadow: 0 3px 8px rgba(0,0,0,0.08);
}

@media (max-width: 992px) {
  .page-container {
    grid-template-columns: 1fr;
  }

  .dashboard-container {
    grid-template-columns: 1fr;
  }

  .main-content {
    padding: 1.5rem;
  }
}

/* ====== Profile Page Styling ====== */

.card {
  background: #ffffff;
  border-radius: 1.2rem;
  transition: transform 0.2s ease, box-shadow 0.3s ease;
}

.card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
}

/* Header Titles */
.card h3 {
  color: #1a1a1a;
  font-weight: 700;
  font-size: 1.4rem;
  border-bottom: 2px solid #f0f0f0;
  padding-bottom: 0.75rem;
  margin-bottom: 1.5rem;
}

/* Info boxes */
.info-box {
  background: #f9fafc;
  border: 1px solid #e3e6eb;
  border-radius: 1rem;
  transition: all 0.3s ease;
}

.info-box:hover {
  background: #f0f4ff;
  border-color: #b3c7ff;
}

/* Labels (like Full Name, Email, etc.) */
.info-box strong {
  color: #2c3e50;
  font-size: 1rem;
}

/* Paragraph (the displayed user info) */
.info-box p {
  margin: 0;
  font-size: 0.95rem;
  color: #555;
}

/* Edit buttons */
.edit-btn {
  font-size: 0.85rem;
  border-radius: 0.6rem;
  transition: all 0.2s ease;
}

.edit-btn i {
  font-size: 0.9rem;
}

.edit-btn:hover {
  background-color: #0d6efd;
  color: #fff;
  border-color: #0d6efd;
}

/* Locked (disabled) buttons */
.btn-outline-secondary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Input fields (when editing) */
.form-control {
  font-size: 0.95rem;
  border-radius: 0.6rem;
}

.form-control:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Save Changes button */
#saveChanges {
  font-weight: 600;
  padding: 0.6rem 1.2rem;
  border-radius: 0.7rem;
  transition: background-color 0.25s ease, transform 0.15s ease;
}

#saveChanges:hover {
  background-color: #0b5ed7;
  transform: translateY(-2px);
}

/* Responsive tweaks */
@media (max-width: 992px) {
  .card {
    margin-bottom: 1.5rem;
  }
}

</style>
<body>
    <?php include_once 'includes/sidebar.php'; ?>

    <div class="main-content">
    <!-- Top Header -->
    <?php include_once 'includes/employee_header.php'; ?>

        <div class="profile-container">
            <?php if (isset($_GET['upload']) && $_GET['upload'] === 'success'): ?>
                <div class="alert alert-success">Profile photo updated successfully!</div>
            <?php elseif ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="settings-section">
                <h3>Profile Information</h3>

                <!-- Profile Photo Preview -->
                <img id="previewImg" 
                     src="<?php echo htmlspecialchars($user['photo'] ?? 'assets/images/default.png'); ?>" 
                     alt="Profile Photo" 
                     style="width:100px; height:100px; border-radius:50%; object-fit:cover; margin-bottom:10px;">

                <!-- Upload Photo Form -->
                <form action="upload_photo.php" method="POST" enctype="multipart/form-data" style="margin-bottom:1rem;">
                    <label for="profilePhoto" class="form-label">Update Photo</label>
                    <input type="file" name="photo" id="profilePhoto" accept="image/*" class="form-control mb-2" required>
                    <button type="submit" class="btn btn-secondary btn-sm">Upload</button>
                </form>
                <div id="uploadError" class="text-danger small mb-3"></div>
           <div class="container my-5">
  <div class="row g-4">
    <!-- Personal Info -->
    <div class="col-lg-6">
      <div class="card shadow-lg border-0 rounded-4 p-4">
        <h3 class="fw-bold mb-4">Personal Information</h3>

        <!-- Full Name -->
        <div class="info-box mb-3 p-3 border rounded-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <strong>Full Name</strong>
            <button class="btn btn-sm btn-outline-primary edit-btn" data-field="full_name">
              <i class="bi bi-pencil"></i> Edit
            </button>
          </div>
          <p id="full_name_display"><?php echo htmlspecialchars($user['full_name']); ?></p>
          <input type="text" id="full_name_input" class="form-control d-none" value="<?php echo htmlspecialchars($user['full_name']); ?>">
        </div>

        <!-- Email -->
        <div class="info-box mb-3 p-3 border rounded-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <strong>Email</strong>
            <button class="btn btn-sm btn-outline-primary edit-btn" data-field="email">
              <i class="bi bi-pencil"></i> Edit
            </button>
          </div>
          <p id="email_display"><?php echo htmlspecialchars($user['email']); ?></p>
          <input type="email" id="email_input" class="form-control d-none" value="<?php echo htmlspecialchars($user['email']); ?>">
        </div>

        <!-- Phone -->
        <div class="info-box mb-3 p-3 border rounded-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <strong>Phone Number</strong>
            <button class="btn btn-sm btn-outline-primary edit-btn" data-field="phone_number">
              <i class="bi bi-pencil"></i> Edit
            </button>
          </div>
          <p id="phone_number_display"><?php echo htmlspecialchars($user['phone_number'] ?? '+63 912 345 6789'); ?></p>
          <input type="text" id="phone_number_input" class="form-control d-none" value="<?php echo htmlspecialchars($user['phone_number'] ?? '+63 912 345 6789'); ?>">
        </div>

        <!-- Date Joined -->
        <div class="info-box mb-3 p-3 border rounded-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <strong>Date Joined</strong>
            <button class="btn btn-sm btn-outline-secondary" disabled>
              <i class="bi bi-lock"></i> Locked
            </button>
          </div>
          <p><?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
        </div>

        <button id="saveChanges" class="btn btn-primary mt-3 d-none w-100">Save Changes</button>
      </div>
    </div>

    <!-- Account Details -->
    <div class="col-lg-6">
      <div class="card shadow-lg border-0 rounded-4 p-4">
        <h3 class="fw-bold mb-4">Account Details</h3>

        <div class="info-box mb-3 p-3 border rounded-4">
          <strong>Address</strong>
          <p>40 Victory Ave. Tatalon Quezon City</p>
          <p>629 J Nepomuceno, Quiapo, Manila</p>
        </div>

        <div class="info-box mb-3 p-3 border rounded-4">
          <strong>Language</strong>
          <p><?php echo htmlspecialchars($user['language'] ?? 'English'); ?></p>
        </div>

        <div class="info-box mb-3 p-3 border rounded-4">
          <strong>Time Zone</strong>
          <p><?php echo htmlspecialchars($user['timezone'] ?? 'GMT+8'); ?> (Philippines)</p>
        </div>

        <div class="info-box mb-3 p-3 border rounded-4">
          <strong>Nationality</strong>
          <p><?php echo htmlspecialchars($user['nationality'] ?? 'Filipino'); ?></p>
        </div>
      </div>
    </div>
  </div>

    <script>
document.getElementById('profilePhoto').addEventListener('change', function(event) {
    const fileInput = event.target;
    const file = fileInput.files[0];
    const errorDiv = document.getElementById('uploadError');
    const preview = document.getElementById('previewImg');
    errorDiv.innerText = "";

    if (!file) return;

    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(file.type)) {
        errorDiv.innerText = "Only JPG, PNG, or GIF allowed.";
        fileInput.value = "";
        preview.src = "";
        return;
    }

    if (file.size > 2 * 1024 * 1024) {
        errorDiv.innerText = "Image must be smaller than 2MB.";
        fileInput.value = "";
        preview.src = "";
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        preview.src = e.target.result;
    }
    reader.readAsDataURL(file);
});

document.querySelectorAll('.edit-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const field = this.dataset.field;
    const display = document.getElementById(field + '_display');
    const input = document.getElementById(field + '_input');

    // Toggle visibility
    display.classList.toggle('d-none');
    input.classList.toggle('d-none');

    // Show Save button if any edit is active
    document.getElementById('saveChanges').classList.remove('d-none');
  });
});

document.getElementById('saveChanges').addEventListener('click', () => {
  const data = {
    full_name: document.getElementById('full_name_input').value,
    email: document.getElementById('email_input').value,
    phone_number: document.getElementById('phone_number_input').value
  };

  fetch('update_profile.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(data)
  })
  .then(res => res.json())
  .then(result => {
    if (result.success) {
      alert('Profile updated successfully!');
      location.reload();
    } else {
      alert('Error updating profile.');
    }
  })
  .catch(() => alert('Request failed.'));
});
</script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="assets/js/dashboard.js"></script>
</body>
</html>
