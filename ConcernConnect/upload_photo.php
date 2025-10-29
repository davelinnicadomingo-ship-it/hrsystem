<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
requireLogin();

$db = new Database();
$conn = $db->connect();
$employee_id = $_SESSION['employee_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $file = $_FILES['photo'];
    $error = $file['error'];

    if ($error === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        $upload_dir = 'assets/images/';

        // Create folder if missing
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $unique_name = uniqid('hr_') . '_' . basename($file['name']);
        $target_path = $upload_dir . $unique_name;

        if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                // Save relative path to employees.photo
                $stmt = $conn->prepare("UPDATE employees SET photo = :photo WHERE employee_id = :id");
                $stmt->execute(['photo' => $target_path, 'id' => $employee_id]);

                $_SESSION['photo'] = $target_path; // for sidebar refresh

                header("Location: hr_settings.php?upload=success");
                exit();
            } else {
                echo "Error moving uploaded file.";
            }
        } else {
            echo "Invalid file type or file too large.";
        }
    } else {
        echo "File upload error: $error";
    }
} else {
    header("Location: hr_settings.php");
    exit();
}
