<?php
header('Content-Type: application/json');
session_start();

require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$db = new Database();
$conn = $db->connect();

$employee_id = $_SESSION['employee_id'] ?? null; // ensure correct session var

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🔹 Decode the JSON input safely
    $input = json_decode(file_get_contents('php://input'), true);
    $message = trim($input['message'] ?? '');

    if ($message !== '') {
        // 🔹 Save employee message
        $stmt = $conn->prepare("
            INSERT INTO chat_messages (employee_id, message, is_bot, created_at)
            VALUES (:employee_id, :message, 0, NOW())
        ");
        $stmt->execute([
            'employee_id' => $employee_id,
            'message' => $message
        ]);

        // 🔹 Get AI/bot response from chatbot_responses
        $bot_response = getBotResponse($conn, $message);

        // 🔹 Save bot reply
        $stmt = $conn->prepare("
            INSERT INTO chat_messages (employee_id, message, is_bot, created_at)
            VALUES (:employee_id, :message, 1, NOW())
        ");
        $stmt->execute([
            'employee_id' => $employee_id,
            'message' => $bot_response
        ]);

        echo json_encode([
            'success' => true,
            'response' => $bot_response,
            'label' => '✅ Bot replied successfully'
        ]);
    } else {
        echo json_encode(['error' => '⚠️ Empty message.']);
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 🔹 Fetch last 20 messages
    $messages = getChatHistory($conn, $employee_id, 20);
    echo json_encode([
        'success' => true,
        'messages' => $messages,
        'label' => '📜 Loaded chat history'
    ]);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
