<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$db = new Database();
$conn = $db->connect();

$user = getEmployeeData($conn, $_SESSION['employee_id']);
$stats = getTicketStats($conn, $_SESSION['employee_id']);
$status_counts = getStatusCounts($conn, $_SESSION['employee_id']);
$recent_tickets = getRecentTickets($conn, $_SESSION['employee_id'], 3);
$chat_history = getChatHistory($conn, $_SESSION['employee_id'], 5);

$chatbot_reply = '';

if (!isset($_SESSION)) session_start();

if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['chat_message'])) {
    $message = strtolower(trim($_POST['chat_message']));

    $responses = [
        'leave' => "You can request leave under the HR Portal → Leave Requests section.",
        'payroll' => "Payroll is processed every 15th and 30th of the month.",
        'benefits' => "We offer healthcare, paid leave, and annual bonuses.",
        'hi' => "Hello! How can I help you today?",
        'hello' => "Hi there! What do you need help with?",
        'thank you' => "You're welcome! 😊 Anything else you'd like to know?",
        'bye' => "Goodbye! Have a great day!"
    ];

    if (array_key_exists($message, $responses)) {
        $reply = $responses[$message];
    } else {
        $reply = "Sorry, I'm not sure how to answer that. Please contact HR for assistance.";
    }

    $_SESSION['chat_history'][] = ['sender' => 'user', 'message' => $_POST['chat_message']];
    $_SESSION['chat_history'][] = ['sender' => 'bot', 'message' => $reply];

    if (isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode(['reply' => $reply, 'history' => $_SESSION['chat_history']]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - HR System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --accent: #4facfe;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --light-bg: #f8fafc;
            --card-bg: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
            --radius: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--light-bg);
            color: var(--text-primary);
            overflow-x: hidden;
            line-height: 1.6;
        }

        .page-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
            max-height: 100vh;
        }

        .dashboard-header {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dashboard-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }

        .card {
            background: var(--card-bg);
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
        }

        .card-header {
            margin-bottom: 1.5rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-subtitle {
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .company-card {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 1.75rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            margin-bottom: 2rem;
            border: none;
        }

        .company-card h3 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            opacity: 0.95;
        }

        .time-display {
            text-align: center;
            padding: 1rem 0;
        }

        .current-time {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0.75rem 0;
            letter-spacing: -0.02em;
            font-variant-numeric: tabular-nums;
        }

        .shift-info {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0.75rem 0;
        }

        .checkout-btn {
            background: rgba(255, 255, 255, 0.25);
            border: 2px solid rgba(255, 255, 255, 0.5);
            color: white;
            padding: 0.625rem 1.75rem;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.75rem;
            font-size: 0.9rem;
        }

        .checkout-btn:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: translateY(-2px);
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .status-item {
            text-align: center;
            padding: 1.25rem 1rem;
            border-radius: 12px;
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            border: 1px solid #bae6fd;
        }

        .status-item.completed {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border-color: #bbf7d0;
        }

        .status-item.in-progress {
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            border-color: #fde68a;
        }

        .status-count {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .status-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .chat-history {
            max-height: 300px;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .chat-history::-webkit-scrollbar {
            width: 6px;
        }

        .chat-history::-webkit-scrollbar-track {
            background: var(--light-bg);
            border-radius: 3px;
        }

        .chat-history::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 3px;
        }

        .chat-message {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 0.75rem;
            animation: fadeIn 0.3s ease;
        }

        .chat-message.bot {
            background: linear-gradient(135deg, #ede9fe, #ddd6fe);
            border-left: 3px solid var(--primary);
        }

        .chat-message.user {
            background: linear-gradient(135deg, #e0f2fe, #bae6fd);
            border-left: 3px solid var(--accent);
        }

        .chat-message strong {
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
        }

        /* Team Member Card - Centered Design */
.team-member-card {
    text-align: center;
    padding: 2rem 1.5rem;
}

.member-profile {
    display: flex;
    flex-direction: column;
    align-items: center;
}
/* Avatar Image - for uploaded photos */
.avatar-image {
    width: 96px;
    height: 96px;
    min-width: 96px;
    min-height: 96px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 1.5rem;
    box-shadow: 0 8px 24px rgba(124, 58, 237, 0.3);
    border: 3px solid white;
}


.team-member-card .avatar-circle {
    width: 96px;
    height: 96px;
    min-width: 96px;
    min-height: 96px;
    border-radius: 50%;
    background: linear-gradient(135deg, #7c3aed, #4f46e5);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 2rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 8px 24px rgba(124, 58, 237, 0.3);
    flex-shrink: 0;
}

.member-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 0.5rem 0;
}

.member-role {
    color: var(--text-secondary);
    font-size: 0.95rem;
    margin: 0 0 1rem 0;
}

.member-email {
    color: var(--text-secondary);
    font-size: 0.875rem;
    text-decoration: underline;
    margin-bottom: 1rem;
    display: inline-block;
    transition: color 0.3s ease;
}

.member-email:hover {
    color: var(--primary);
}

.member-joined {
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin: 0.5rem 0 0 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.member-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
}

.action-btn {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    border: none;
    background: var(--light-bg);
    color: var(--text-secondary);
    font-size: 1.25rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.action-btn:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

        .avatar-circle {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .member-info {
            flex: 1;
        }

        .member-info strong {
            display: block;
            font-size: 1.125rem;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .member-info div {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
        }

        .calendar-header {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .calendar-header select {
            flex: 1;
            padding: 0.625rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.875rem;
            background: white;
            cursor: pointer;
        }

        #calendar {
            background: var(--light-bg);
            padding: 1rem;
            border-radius: 8px;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
        }

        .event-item {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            background: var(--light-bg);
            border-radius: 12px;
            margin-bottom: 0.75rem;
            border-left: 3px solid var(--primary);
            transition: all 0.3s ease;
        }

        .event-item:hover {
            background: #f1f5f9;
            transform: translateX(5px);
        }

        .event-date {
            text-align: center;
            font-weight: 700;
            color: var(--primary);
            min-width: 60px;
            font-size: 0.875rem;
        }

        .event-details strong {
            display: block;
            margin-bottom: 0.25rem;
            color: var(--text-primary);
        }

        .event-details div {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        /* Logout Modal */
/* Logout Confirmation Modal - IMPROVED DESIGN */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 2000;
    align-items: center;
    justify-content: center;
}

.modal-overlay.active {
    display: flex;
    animation: fadeIn 0.3s ease;
}

.modal-content {
    background: white;
    padding: 2.5rem;
    border-radius: 24px;
    box-shadow: 0 25px 70px rgba(0, 0, 0, 0.3);
    max-width: 480px;
    width: 90%;
    animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
}

.modal-header {
    text-align: center;
    margin-bottom: 2rem;
}

.modal-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, #b14affff, #27d0ffff);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: white;
    box-shadow: 0 8px 24px rgba(245, 158, 11, 0.3);
    animation: iconPulse 2s ease-in-out infinite;
    aspect-ratio: 1 / 1; /* Force perfect square */
    flex-shrink: 0; /* Prevent shrinking */
}


@keyframes iconPulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 8px 24px rgba(245, 158, 11, 0.3);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 12px 32px rgba(245, 158, 11, 0.4);
    }
}

.modal-header h3 {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0 0 0.75rem 0;
    color: var(--text-primary);
    letter-spacing: -0.02em;
}

.modal-header p {
    color: var(--text-secondary);
    font-size: 1rem;
    margin: 0;
    line-height: 1.5;
}

.shift-summary {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    padding: 1.5rem;
    border-radius: 16px;
    margin: 2rem 0;
    border: 1px solid var(--border-color);
}

.shift-summary div {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    color: var(--text-secondary);
    font-size: 0.95rem;
}

.shift-summary div:not(:last-child) {
    border-bottom: 1px solid #e2e8f0;
}

.shift-summary div span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.shift-summary div span i {
    font-size: 1.1rem;
    color: var(--primary);
}

.shift-summary div strong {
    color: var(--text-primary);
    font-weight: 700;
    font-size: 1rem;
    font-variant-numeric: tabular-nums;
}

.modal-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.modal-btn {
    flex: 1;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1rem;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-cancel {
    background: white;
    color: var(--text-primary);
    border: 2px solid var(--border-color);
}

.btn-cancel:hover {
    background: var(--light-bg);
    border-color: #cbd5e1;
    transform: translateY(-2px);
}

.btn-confirm {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.btn-confirm:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(239, 68, 68, 0.4);
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@media (max-width: 480px) {
    .modal-content {
        padding: 2rem 1.5rem;
    }

    .modal-header h3 {
        font-size: 1.5rem;
    }

    .modal-icon {
        width: 64px;
        height: 64px;
        font-size: 2rem;
    }

    .modal-actions {
        flex-direction: column;
    }

    .modal-btn {
        width: 100%;
    }
}


        .chatbot-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 1.25rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chatbot-header span {
            font-weight: 600;
            font-size: 1.125rem;
        }

        .chatbot-header button {
            background: transparent;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background 0.3s ease;
        }

        .chatbot-header button:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .chatbot-messages {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
            max-height: 400px;
        }

        .chatbot-input {
            display: flex;
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
            gap: 0.75rem;
        }

        .chatbot-input input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 50px;
            font-size: 0.875rem;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .chatbot-input input:focus {
            border-color: var(--primary);
        }

        .chatbot-input button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .chatbot-input button:hover {
            background: var(--secondary);
            transform: scale(1.05);
        }

        .chatbot-quick {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
            background: var(--light-bg);
        }

        .chatbot-quick p {
            margin: 0 0 0.75rem 0;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .quick-btn {
            background: white;
            border: 1px solid var(--border-color);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            font-size: 0.8125rem;
            transition: all 0.3s ease;
        }

        .quick-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        @media (max-width: 992px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .status-grid {
                grid-template-columns: 1fr;
            }

            .main-content {
                padding: 1rem;
                max-height: none;
            }

            .chatbot-window, .modal-content {
                width: calc(100vw - 2rem);
                right: 1rem;
            }
        }

        @media (max-width: 768px) {
            .current-time {
                font-size: 2rem;
            }

            .dashboard-header h1 {
                font-size: 1.5rem;
            }

            .status-count {
                font-size: 1.75rem;
            }

            .chatbot-section {
                bottom: 1rem;
                right: 1rem;
            }
        }
    </style>
</head>
<body>
    <?php include_once 'includes/sidebar.php'; ?>

    <div class="page-container">
        <div class="main-content">
            <?php include_once 'includes/employee_header.php'; ?>

            <div class="dashboard-grid">
                <div class="dashboard-left">
                    <div class="company-card">
                        <h3><i class="bi bi-building"></i> Trusting Social AI Philippines</h3>
                        <div class="time-display">
                            <div class="current-time" id="current-time">14:48:32</div>
                            <div class="shift-info">
                                <i class="bi bi-clock-history"></i> <span id="duration-text">Shift Duration: 00h 00m 00s</span>
                            </div>
                            <button class="checkout-btn" onclick="showLogoutModal()">
                                <i class="bi bi-box-arrow-right"></i> Check Out
                            </button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="bi bi-graph-up"></i>
                                Status Report
                            </h3>
                            <p class="card-subtitle">Your activity overview</p>
                        </div>
                        <div class="status-grid">
                            <div class="status-item">
                                <div class="status-count"><?php echo $status_counts['days'] ?? 0; ?></div>
                                <div class="status-label">Days Active</div>
                            </div>
                            <div class="status-item completed">
                                <div class="status-count"><?php echo $status_counts['completed'] ?? 0; ?></div>
                                <div class="status-label">Completed</div>
                            </div>
                            <div class="status-item in-progress">
                                <div class="status-count"><?php echo $status_counts['in_progress'] ?? 0; ?></div>
                                <div class="status-label">In Progress</div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="bi bi-chat-dots"></i>
                                Previous Conversations
                            </h3>
                            <p class="card-subtitle">Recent chat with AI assistant</p>
                        </div>
                        <div class="chat-history" id="chat-history">
                            <?php if (!empty($chat_history)): ?>
                                <?php foreach ($chat_history as $msg): ?>
                                    <div class="chat-message <?php echo $msg['is_bot'] ? 'bot' : 'user'; ?>">
                                        <strong><?php echo $msg['is_bot'] ? 'AI Assistant' : 'You'; ?></strong>
                                        <?php echo htmlspecialchars($msg['message']); ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="chat-message bot">
                                    <strong>AI Assistant</strong>
                                    No previous conversations. Start chatting with the AI assistant!
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="dashboard-right">
                    <div class="card team-member-card">
    <div class="member-profile">
        <?php if (!empty($user['photo'])): ?>
            <!-- Show uploaded photo -->
            <img src="<?php echo htmlspecialchars($user['photo']); ?>" alt="<?php echo htmlspecialchars($user['full_name'] ?? 'Employee'); ?>" class="avatar-image">
        <?php else: ?>
            <!-- Show initials as fallback -->
            <div class="avatar-circle">
                <?php echo strtoupper(substr($user['full_name'] ?? 'AA', 0, 2)); ?>
            </div>
        <?php endif; ?>
        
        <h3 class="member-name"><?php echo htmlspecialchars($user['full_name'] ?? 'Employee Name'); ?></h3>
        <p class="member-role"><?php echo htmlspecialchars($user['department'] ?? 'Department'); ?></p>
        <a href="mailto:<?php echo htmlspecialchars($user['email'] ?? ''); ?>" class="member-email">
            <?php echo htmlspecialchars($user['email'] ?? 'email@example.com'); ?>
        </a>
        <p class="member-joined">
            <i class="bi bi-calendar-event"></i>
            Joined: <?php echo date('F Y', strtotime($user['created_at'] ?? 'now')); ?>
        </p>
    </div>
    
    <div class="member-actions">
        <button class="action-btn" onclick="window.location.href='mailto:<?php echo htmlspecialchars($user['email'] ?? ''); ?>'">
            <i class="bi bi-envelope-fill"></i>
        </button>
        <button class="action-btn" onclick="alert('Message feature coming soon!')">
            <i class="bi bi-chat-dots-fill"></i>
        </button>
    </div>
</div>


                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="bi bi-calendar3"></i>
                                Calendar
                            </h3>
                        </div>
                        <div class="calendar-header">
                            <select id="month-select">
                                <option>October</option>
                            </select>
                            <select id="year-select">
                                <option>2025</option>
                            </select>
                        </div>
                        <div id="calendar">
                            <i class="bi bi-calendar-week" style="font-size: 2rem; opacity: 0.3;"></i>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="bi bi-bell"></i>
                                Upcoming Events
                            </h3>
                            <p class="card-subtitle">Recent tickets and tasks</p>
                        </div>
                        <?php if (!empty($recent_tickets)): ?>
                            <?php foreach ($recent_tickets as $ticket): ?>
                                <div class="event-item">
                                    <div class="event-date">
                                        <?php echo date('M d', strtotime($ticket['created_at'])); ?><br>
                                        <small><?php echo date('g:ia', strtotime($ticket['created_at'])); ?></small>
                                    </div>
                                    <div class="event-details">
                                        <strong><?php echo htmlspecialchars($ticket['title']); ?></strong>
                                        <div><?php echo htmlspecialchars($ticket['category_id'] ?? 'General'); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="text-align: center; color: var(--text-secondary); padding: 2rem 0;">
                                <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                                No upcoming events
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
<div class="modal-overlay" id="logoutModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon">
                <i class="bi bi-box-arrow-right"></i>
            </div>
            <h3>Confirm Check Out</h3>
            <p>Are you sure you want to end your shift and log out?</p>
        </div>

        <div class="shift-summary">
            <div>
                <span><i class="bi bi-clock-history"></i> Login Time</span>
                <strong id="login-time-display">--:--:--</strong>
            </div>
            <div>
                <span><i class="bi bi-clock"></i> Current Time</span>
                <strong id="current-time-display">--:--:--</strong>
            </div>
            <div>
                <span><i class="bi bi-stopwatch"></i> Total Duration</span>
                <strong id="total-duration-display">--h --m --s</strong>
            </div>
        </div>

        <div class="modal-actions">
            <button class="modal-btn btn-cancel" onclick="hideLogoutModal()">
                <i class="bi bi-x-circle"></i> 
                <span>Cancel</span>
            </button>
            <button class="modal-btn btn-confirm" onclick="confirmLogout()">
                <i class="bi bi-check-circle"></i> 
                <span>Confirm Check Out</span>
            </button>
        </div>
    </div>
</div>


    <!-- Chatbot -->
    <div class="chatbot-section">
        <button class="chatbot-toggle" id="chatbot-toggle" type="button">
            <i class="bi bi-chat-dots-fill"></i>
            Help
        </button>

        <div class="chatbot-window" id="chatbot-window">
            <div class="chatbot-header">
                <span><i class="bi bi-robot"></i> AI Assistant</span>
                <button id="close-chatbot" type="button">&times;</button>
            </div>

            <div class="chatbot-messages" id="chatbot-messages">
                <?php if (!empty($_SESSION['chat_history'])): ?>
                    <?php foreach ($_SESSION['chat_history'] as $msg): ?>
                        <div class="chat-message <?= $msg['sender'] === 'bot' ? 'bot' : 'user' ?>">
                            <strong><?= $msg['sender'] === 'bot' ? 'AI Assistant' : 'You' ?></strong>
                            <?= htmlspecialchars($msg['message']) ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="chat-message bot">
                        <strong>AI Assistant</strong>
                        Hello! How can I assist you today?
                    </div>
                <?php endif; ?>
            </div>

            <div class="chatbot-input">
                <input type="text" id="chatbot-input" name="chat_message" placeholder="Type your question...">
                <button id="send-message" type="button">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>

            <div class="chatbot-quick">
                <p>Quick Actions</p>
                <button class="quick-btn" data-msg="leave">Leave Request</button>
                <button class="quick-btn" data-msg="payroll">Payroll Info</button>
                <button class="quick-btn" data-msg="benefits">Benefits</button>
            </div>
        </div>
    </div>

    <script>
        if (!sessionStorage.getItem('loginTime')) {
            sessionStorage.setItem('loginTime', new Date().getTime());
        }

        function updateTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('current-time').textContent = `${hours}:${minutes}:${seconds}`;
            updateShiftDuration();
        }

        function updateShiftDuration() {
            const loginTime = parseInt(sessionStorage.getItem('loginTime'));
            const now = new Date().getTime();
            const diff = now - loginTime;

            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            document.getElementById('duration-text').textContent = 
                `Shift Duration: ${String(hours).padStart(2, '0')}h ${String(minutes).padStart(2, '0')}m ${String(seconds).padStart(2, '0')}s`;
        }

        updateTime();
        setInterval(updateTime, 1000);

        function showLogoutModal() {
            const modal = document.getElementById('logoutModal');
            const loginTime = new Date(parseInt(sessionStorage.getItem('loginTime')));
            const now = new Date();
            const diff = now.getTime() - loginTime.getTime();

            const loginHours = String(loginTime.getHours()).padStart(2, '0');
            const loginMinutes = String(loginTime.getMinutes()).padStart(2, '0');
            const loginSeconds = String(loginTime.getSeconds()).padStart(2, '0');
            document.getElementById('login-time-display').textContent = `${loginHours}:${loginMinutes}:${loginSeconds}`;

            const currentHours = String(now.getHours()).padStart(2, '0');
            const currentMinutes = String(now.getMinutes()).padStart(2, '0');
            const currentSeconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('current-time-display').textContent = `${currentHours}:${currentMinutes}:${currentSeconds}`;

            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);
            document.getElementById('total-duration-display').textContent = 
                `${String(hours).padStart(2, '0')}h ${String(minutes).padStart(2, '0')}m ${String(seconds).padStart(2, '0')}s`;

            modal.classList.add('active');
        }

        function hideLogoutModal() {
            document.getElementById('logoutModal').classList.remove('active');
        }

        function confirmLogout() {
            sessionStorage.removeItem('loginTime');
            window.location.href = 'logout.php';
        }

        document.getElementById('logoutModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideLogoutModal();
            }
        });

        document.getElementById('chatbot-toggle').addEventListener('click', function() {
            document.getElementById('chatbot-window').classList.add('active');
        });

        document.getElementById('close-chatbot').addEventListener('click', function() {
            document.getElementById('chatbot-window').classList.remove('active');
        });

        document.getElementById('send-message').addEventListener('click', sendMessage);
        document.getElementById('chatbot-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') sendMessage();
        });

        function sendMessage() {
            const input = document.getElementById('chatbot-input');
            const message = input.value.trim();
            if (!message) return;

            const formData = new FormData();
            formData.append('chat_message', message);
            formData.append('ajax', '1');

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messagesDiv = document.getElementById('chatbot-messages');
                messagesDiv.innerHTML = '';
                data.history.forEach(msg => {
                    const div = document.createElement('div');
                    div.className = `chat-message ${msg.sender === 'bot' ? 'bot' : 'user'}`;
                    div.innerHTML = `<strong>${msg.sender === 'bot' ? 'AI Assistant' : 'You'}</strong>${msg.message}`;
                    messagesDiv.appendChild(div);
                });
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
                input.value = '';
            });
        }

        document.querySelectorAll('.quick-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('chatbot-input').value = this.dataset.msg;
                sendMessage();
            });
        });
    </script>

    <script src="assets/js/dashboard.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
