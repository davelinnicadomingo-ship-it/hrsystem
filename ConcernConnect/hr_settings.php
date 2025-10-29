<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$db = new Database();
$conn = $db->connect();

$user = getEmployeeData($conn, $_SESSION['employee_id']);

if ($user['role'] !== 'hr_admin') {
    header('Location: dashboard.php');
    exit();
}

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
    <title>Settings - HR System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/hr_sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h1>HR SETTINGS</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($user['full_name']); ?></span>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>

        <div class="settings-container">
            <h2>HR Administrator Settings</h2>

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

                <!-- Update Profile Form -->
                <form method="POST" action="">
                    <div class="form-group mb-3">
                        <label>Employee ID</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['employee_id']); ?>" disabled>
                    </div>
                    <div class="form-group mb-3">
                        <label>Full Name</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" disabled>
                    </div>
                    <div class="form-group mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Phone Number</label>
                        <input type="text" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                </form>
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
</script>
</body>
</html>
