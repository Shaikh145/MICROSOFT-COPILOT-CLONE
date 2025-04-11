<?php
    session_start();
    require_once 'db_config.php';
    
    // Check if user is logged in
    if(!isset($_SESSION['user_id'])) {
        echo "<script>window.location.href = 'index.php';</script>";
        exit;
    }
    
    // Get user data
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
    
    // Get user tasks
    $tasks = [];
    $stmt = $conn->prepare("SELECT id, title, description, status, created_at FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }
    }
    
    // Define predefined questions
    $predefinedQuestions = [
        "What's on my schedule today?",
        "How can I improve my productivity?",
        "Can you help me prioritize my tasks?",
        "What are some time management techniques?",
        "Create a to-do list for my project",
        "How do I stay focused while working?",
        "Summarize my current tasks",
        "What are effective meeting strategies?",
        "How can I reduce work stress?",
        "What's the best way to organize my tasks?",
        "Can you suggest a daily routine?",
        "How do I track my project progress?",
        "What productivity tools should I use?",
        "Help me plan my workweek",
        "How do I delegate tasks effectively?",
        "What are some goal-setting strategies?",
        "How do I maintain work-life balance?",
        "Can you suggest email management tips?",
        "What are effective brainstorming techniques?",
        "How do I stay motivated on long-term projects?"
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Microsoft Copilot Clone | Dashboard</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header Styles */
        header {
            background-color: #2c2c2c;
            color: white;
            padding: 12px 24px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo svg {
            height: 24px;
            margin-right: 10px;
        }

        .logo h1 {
            font-size: 18px;
            font-weight: 600;
        }

        .user-menu {
            display: flex;
            align-items: center;
        }

        .user-menu .user-info {
            margin-right: 20px;
            font-size: 14px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn svg {
            margin-right: 6px;
        }

        .btn-light {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .btn-light:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .btn-primary {
            background-color: #0078d4;
            color: white;
        }

        .btn-primary:hover {
            background-color: #106ebe;
        }

        /* Main Content Styles */
        main {
            flex: 1;
            padding: 24px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            gap: 24px;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 300px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            height: calc(100vh - 120px);
            position: sticky;
            top: 80px;
            overflow-y: auto;
        }

        .sidebar-section {
            margin-bottom: 24px;
        }

        .sidebar-section h2 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f0f0f0;
            color: #333;
        }

        .question-list {
            list-style-type: none;
        }

        .question-item {
            margin-bottom: 12px;
            padding: 10px 12px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #f0f0f0;
        }

        .question-item:hover {
            background-color: #f5f9ff;
            border-color: #d0e1fd;
        }

        /* Main Area Styles */
        .content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .chat-container {
            flex: 1;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            height: calc(100vh - 120px);
        }

        .chat-messages {
            flex: 1;
            padding: 24px;
            overflow-y: auto;
        }

        .message {
            margin-bottom: 24px;
            max-width: 80%;
        }

        .message.user {
            margin-left: auto;
        }

        .message-content {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            line-height: 1.5;
        }

        .message.assistant .message-content {
            background-color: #f0f2f5;
            color: #333;
            border-radius: 0 8px 8px 8px;
        }

        .message.user .message-content {
            background-color: #e3f2fd;
            color: #333;
            border-radius: 8px 0 8px 8px;
        }

        .message-header {
            display: flex;
            align-items: center;
            margin-bottom: 6px;
        }

        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: 8px;
            background-color: #0078d4;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .message-avatar.user {
            background-color: #0078d4;
        }

        .message-avatar.assistant {
            background-color: #333;
        }

        .message-sender {
            font-weight: 500;
            font-size: 14px;
        }

        .message-time {
            font-size: 12px;
            color: #777;
            margin-left: 10px;
        }

        .chat-input-container {
            padding: 16px;
            border-top: 1px solid #f0f0f0;
        }

        .chat-input-wrapper {
            position: relative;
            display: flex;
            align-items: flex-end;
        }

        .chat-input {
            flex: 1;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 14px;
            line-height: 1.5;
            resize: none;
            min-height: 48px;
            max-height: 150px;
            overflow-y: auto;
            transition: all 0.3s;
        }

        .chat-input:focus {
            outline: none;
            border-color: #0078d4;
            box-shadow: 0 0 0 2px rgba(0, 120, 212, 0.2);
        }

        .chat-input-actions {
            display: flex;
            align-items: center;
            margin-left: 12px;
        }

        .chat-action-btn {
            background: transparent;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            margin-left: 8px;
        }

        .chat-action-btn:hover {
            background-color: #f5f5f5;
        }

        .chat-action-btn svg {
            width: 20px;
            height: 20px;
            color: #666;
        }

        .chat-action-btn.send {
            background-color: #0078d4;
        }

        .chat-action-btn.send svg {
            color: white;
        }

        .chat-action-btn.send:hover {
            background-color: #106ebe;
        }

        /* Task Management Styles */
        .task-management {
            margin-top: 24px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
        }

        .task-management-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f0f0;
        }

        .task-management-header h2 {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .task-list {
            list-style-type: none;
        }

        .task-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f5f5f5;
            display: flex;
            align-items: center;
            transition: all 0.2s;
        }

        .task-item:hover {
            background-color: #f9f9f9;
        }

        .task-checkbox {
            margin-right: 12px;
        }

        .task-checkbox input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .task-content {
            flex: 1;
        }

        .task-title {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .task-description {
            font-size: 13px;
            color: #666;
        }

        .task-actions {
            display: flex;
        }

        .task-action-btn {
            background: transparent;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            margin-left: 4px;
        }

        .task-action-btn:hover {
            background-color: #f0f0f0;
        }

        .task-action-btn svg {
            width: 16px;
            height: 16px;
            color: #666;
        }

        .task-add-form {
            display: flex;
            margin-top: 16px;
            align-items: flex-start;
        }

        .task-add-input {
            flex: 1;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 10px 12px;
            font-size: 14px;
            margin-right: 8px;
        }

        .task-add-input:focus {
            outline: none;
            border-color: #0078d4;
        }

        .task-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            margin-left: 10px;
        }

        .task-status.pending {
            background-color: #fff0c2;
            color: #856404;
        }

        .task-status.in_progress {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .task-status.completed {
            background-color: #d4edda;
            color: #155724;
        }

        .completed-task {
            text-decoration: line-through;
            opacity: 0.7;
        }

        /* Modal Styles */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }

        .modal-backdrop.show {
            opacity: 1;
            visibility: visible;
        }

        .modal {
            background-color: white;
            border-radius: 8px;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            transform: translateY(-20px);
            transition: all 0.3s;
        }

        .modal-backdrop.show .modal {
            transform: translateY(0);
        }

        .modal-header {
            padding: 16px 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .modal-close {
            background: transparent;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background-color: #f5f5f5;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            padding: 16px 20px;
            border-top: 1px solid #f0f0f0;
            display: flex;
            justify-content: flex-end;
        }

        .modal-footer .btn {
            margin-left: 10px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #0078d4;
            box-shadow: 0 0 0 2px rgba(0, 120, 212, 0.1);
        }

        /* Speech Recognition Styles */
        .listening {
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(0, 120, 212, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(0, 120, 212, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(0, 120, 212, 0);
            }
        }

        .instructions {
            text-align: center;
            padding: 16px;
            background-color: #f9f9f9;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 14px;
            color: #666;
        }

        .instructions strong {
            color: #333;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            main {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                margin-bottom: 24px;
                position: static;
            }

            .chat-container, .task-management {
                height: auto;
            }

            .chat-messages {
                height: 400px;
            }
        }

        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                align-items: flex-start;
            }

            .user-menu {
                margin-top: 12px;
            }
        }

        /* Helper classes */
        .d-none {
            display: none !important;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 23 23" width="24" height="24">
                    <rect x="1" y="1" width="10" height="10" fill="#f25022"/>
                    <rect x="12" y="1" width="10" height="10" fill="#7fba00"/>
                    <rect x="1" y="12" width="10" height="10" fill="#00a4ef"/>
                    <rect x="12" y="12" width="10" height="10" fill="#ffb900"/>
                </svg>
                <h1>Microsoft Copilot Clone</h1>
            </div>
            <div class="user-menu">
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars($user_name); ?></span>
                </div>
                <button id="logout-btn" class="btn btn-light">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 17L21 12L16 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Logout
                </button>
            </div>
        </div>
    </header>

    <main>
        <aside class="sidebar">
            <div class="sidebar-section">
                <h2>Suggested Questions</h2>
                <ul class="question-list">
                    <?php foreach(array_slice($predefinedQuestions, 0, 10) as $question): ?>
                        <li class="question-item" data-question="<?php echo htmlspecialchars($question); ?>">
                            <?php echo htmlspecialchars($question); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="sidebar-section">
                <h2>More Questions</h2>
                <ul class="question-list">
                    <?php foreach(array_slice($predefinedQuestions, 10) as $question): ?>
                        <li class="question-item" data-question="<?php echo htmlspecialchars($question); ?>">
                            <?php echo htmlspecialchars($question); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>
        
        <div class="content-area">
            <div class="chat-container">
                <div class="chat-messages" id="chat-messages">
                    <div class="message assistant">
                        <div class="message-header">
                            <div class="message-avatar assistant">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13Z" fill="currentColor"/>
                                    <path d="M18 13C18.5523 13 19 12.5523 19 12C19 11.4477 18.5523 11 18 11C17.4477 11 17 11.4477 17 12C17 12.5523 17.4477 13 18 13Z" fill="currentColor"/>
                                    <path d="M6 13C6.55228 13 7 12.5523 7 12C7 11.4477 6.55228 11 6 11C5.44772 11 5 11.4477 5 12C5 12.5523 5.44772 13 6 13Z" fill="currentColor"/>
                                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="message-sender">Copilot Assistant</div>
                            <div class="message-time">Just now</div>
                        </div>
                        <div class="message-content">
                            Hello, <?php echo htmlspecialchars($user_name); ?>! I'm your productivity assistant. How can I help you today? You can ask me questions, manage your tasks, or get suggestions for improving your workflow.
                        </div>
                    </div>
                </div>
                <div class="chat-input-container">
                    <div class="instructions">
                        <strong>Tip:</strong> You can either type your question or <strong>click the microphone icon</strong> to speak your query!
                    </div>
                    <div class="chat-input-wrapper">
                        <textarea id="chat-input" class="chat-input" placeholder="Type your question or task here..."></textarea>
                        <div class="chat-input-actions">
                            <button id="voice-btn" class="chat-action-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 1C11.2044 1 10.4413 1.31607 9.87868 1.87868C9.31607 2.44129 9 3.20435 9 4V12C9 12.7956 9.31607 13.5587 9.87868 14.1213C10.4413 14.6839 11.2044 15 12 15C12.7956 15 13.5587 14.6839 14.1213 14.1213C14.6839 13.5587 15 12.7956 15 12V4C15 3.20435 14.6839 2.44129 14.1213 1.87868C13.5587 1.31607 12.7956 1 12 1Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M19 10V12C19 13.8565 18.2625 15.637 16.9497 16.9497C15.637 18.2625 13.8565 19 12 19C10.1435 19 8.36301 18.2625 7.05025 16.9497C5.7375 15.637 5 13.8565 5 12V10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 19V23" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8 23H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button id="send-btn" class="chat-action-btn send">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M22 2L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="task-management">
                <div class="task-management-header">
                    <h2>Task Management</h2>
                    <button id="add-task-btn" class="btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 5V19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Add Task
                    </button>
                </div>
                
                <ul class="task-list" id="task-list">
                    <?php if (count($tasks) > 0): ?>
                        <?php foreach($tasks as $task): ?>
                            <li class="task-item <?php echo $task['status'] === 'completed' ? 'completed-task' : ''; ?>" data-id="<?php echo $task['id']; ?>">
                                <div class="task-checkbox">
                                    <input type="checkbox" <?php echo $task['status'] === 'completed' ? 'checked' : ''; ?> class="task-complete-checkbox">
                                </div>
                                <div class="task-content">
                                    <div class="task-title"><?php echo htmlspecialchars($task['title']); ?></div>
                                    <?php if (!empty($task['description'])): ?>
                                        <div class="task-description"><?php echo htmlspecialchars($task['description']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="task-status <?php echo $task['status']; ?>">
                                    <?php 
                                        if ($task['status'] === 'pending') echo 'Pending';
                                        else if ($task['status'] === 'in_progress') echo 'In Progress';
                                        else echo 'Completed';
                                    ?>
                                </div>
                                <div class="task-actions">
                                    <button class="task-action-btn edit-task-btn">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M18.5 2.5C18.8978 2.10217 19.4374 1.87868 20 1.87868C20.5626 1.87868 21.1022 2.10217 21.5 2.5C21.8978 2.89782 22.1213 3.43739 22.1213 4C22.1213 4.56261 21.8978 5.10218 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                    <button class="task-action-btn delete-task-btn">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li id="no-tasks-message">No tasks available. Add some tasks to get started!</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </main>

    <!-- Add/Edit Task Modal -->
    <div id="task-modal" class="modal-backdrop">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title" id="modal-title">Add New Task</h3>
                <button class="modal-close" id="close-modal">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <form id="task-form">
                    <input type="hidden" id="task-id">
                    <div class="form-group">
                        <label for="task-title">Title</label>
                        <input type="text" id="task-title" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="task-description">Description (optional)</label>
                        <textarea id="task-description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="task-status">Status</label>
                        <select id="task-status" name="status" class="form-control">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn" id="cancel-task">Cancel</button>
                <button class="btn btn-primary" id="save-task">Save Task</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const chatMessages = document.getElementById('chat-messages');
            const chatInput = document.getElementById('chat-input');
            const sendBtn = document.getElementById('send-btn');
            const voiceBtn = document.getElementById('voice-btn');
            const taskList = document.getElementById('task-list');
            const addTaskBtn = document.getElementById('add-task-btn');
            const taskModal = document.getElementById('task-modal');
            const closeModal = document.getElementById('close-modal');
            const cancelTask = document.getElementById('cancel-task');
            const saveTask = document.getElementById('save-task');
            const logoutBtn = document.getElementById('logout-btn');
            const questionItems = document.querySelectorAll('.question-item');

            // Variables
            let isListening = false;
            let recognition;
            let currentTaskId = null;
            let useVoiceResponse = false;
            
            // Initialize speech recognition
            if ('SpeechRecognition' in window || 'webkitSpeechRecognition' in window) {
                recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
                recognition.continuous = false;
                recognition.interimResults = false;
                recognition.lang = 'en-US';

                recognition.onresult = function(event) {
                    const transcript = event.results[0][0].transcript;
                    chatInput.value = transcript;
                    useVoiceResponse = true;
                    sendMessage();
                };

                recognition.onend = function() {
                    isListening = false;
                    voiceBtn.classList.remove('listening');
                };

                recognition.onerror = function(event) {
                    console.error('Speech recognition error', event.error);
                    isListening = false;
                    voiceBtn.classList.remove('listening');
                    alert('Speech recognition error: ' + event.error);
                };
            } else {
                voiceBtn.style.display = 'none';
                alert('Speech recognition is not supported in your browser.');
            }
            
            // Helper Functions
            function formatTime(date) {
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }

            function scrollToBottom() {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            function addMessage(message, isUser = false) {
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${isUser ? 'user' : 'assistant'}`;
                
                const messageHeader = document.createElement('div');
                messageHeader.className = 'message-header';
                
                const messageAvatar = document.createElement('div');
                messageAvatar.className = `message-avatar ${isUser ? 'user' : 'assistant'}`;
                
                const avatarIcon = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                avatarIcon.setAttribute('width', '16');
                avatarIcon.setAttribute('height', '16');
                avatarIcon.setAttribute('viewBox', '0 0 24 24');
                avatarIcon.setAttribute('fill', 'none');
                
                if (isUser) {
                    avatarIcon.innerHTML = `
                        <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    `;
                } else {
                    avatarIcon.innerHTML = `
                        <path d="M12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13Z" fill="currentColor"/>
                        <path d="M18 13C18.5523 13 19 12.5523 19 12C19 11.4477 18.5523 11 18 11C17.4477 11 17 11.4477 17 12C17 12.5523 17.4477 13 18 13Z" fill="currentColor"/>
                        <path d="M6 13C6.55228 13 7 12.5523 7 12C7 11.4477 6.55228 11 6 11C5.44772 11 5 11.4477 5 12C5 12.5523 5.44772 13 6 13Z" fill="currentColor"/>
                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2"/>
                    `;
                }
                
                messageAvatar.appendChild(avatarIcon);
                messageHeader.appendChild(messageAvatar);
                
                const messageSender = document.createElement('div');
                messageSender.className = 'message-sender';
                messageSender.textContent = isUser ? 'You' : 'Copilot Assistant';
                messageHeader.appendChild(messageSender);
                
                const messageTime = document.createElement('div');
                messageTime.className = 'message-time';
                messageTime.textContent = formatTime(new Date());
                messageHeader.appendChild(messageTime);
                
                messageDiv.appendChild(messageHeader);
                
                const messageContent = document.createElement('div');
                messageContent.className = 'message-content';
                messageContent.textContent = message;
                messageDiv.appendChild(messageContent);
                
                chatMessages.appendChild(messageDiv);
                scrollToBottom();
            }

            function sendMessage() {
                const message = chatInput.value.trim();
                if (message === '') return;
                
                // Add user message to chat
                addMessage(message, true);
                
                // Process the message
                processUserInput(message);
                
                // Clear input
                chatInput.value = '';
                chatInput.focus();
            }

            function processUserInput(message) {
                // Simulate thinking time
                setTimeout(() => {
                    const response = generateResponse(message);
                    addMessage(response);
                    
                    // If voice response is enabled, use text-to-speech
                    if (useVoiceResponse && 'speechSynthesis' in window) {
                        const speech = new SpeechSynthesisUtterance(response);
                        speech.lang = 'en-US';
                        window.speechSynthesis.speak(speech);
                        useVoiceResponse = false;
                    }
                }, 1000);
            }

            function generateResponse(message) {
                // Clean up the message
                message = message.toLowerCase().trim();
                
                // Common responses for the predefined questions
                if (message.includes("schedule") || message.includes("calendar")) {
                    return "Your schedule today includes a team meeting at 10 AM, a client call at 2 PM, and a project review at 4 PM. You have 30 minutes of free time between the client call and project review that you could use for focused work.";
                } else if (message.includes("improve productivity") || message.includes("more productive")) {
                    return "To improve your productivity, try implementing the Pomodoro Technique: work for 25 minutes, then take a 5-minute break. Also, consider batching similar tasks together, turning off notifications during focused work, and using task prioritization methods like the Eisenhower Matrix.";
                } else if (message.includes("prioritize") || message.includes("tasks")) {
                    return "Looking at your current tasks, I recommend focusing on the 'Project Proposal' first as it has the closest deadline. Then move on to 'Client Presentation' as it's high-impact, and finally address the 'Weekly Report' which is routine but necessary.";
                } else if (message.includes("time management") || message.includes("manage time")) {
                    return "Effective time management techniques include: 1) Time blocking - schedule specific times for different types of work; 2) The 2-minute rule - if a task takes less than 2 minutes, do it immediately; 3) The 1-3-5 rule - plan to accomplish 1 big thing, 3 medium things, and 5 small things each day.";
                } else if (message.includes("to-do list") || message.includes("todo list")) {
                    return "I've created a to-do list for your project: 1) Finalize project scope document, 2) Schedule kickoff meeting with stakeholders, 3) Create initial wireframes, 4) Develop resource allocation plan, 5) Set up project tracking system. Would you like me to add these to your tasks?";
                } else if (message.includes("focus") || message.includes("concentration")) {
                    return "To improve focus while working, try: 1) Using noise-cancelling headphones or ambient sounds, 2) Implementing the 'deep work' philosophy by blocking off distraction-free time, 3) Using the 'Do Not Disturb' feature on your devices, 4) Taking short breaks for movement every hour to refresh your mind.";
                } else if (message.includes("summarize") || message.includes("summary")) {
                    return "You currently have 8 active tasks: 3 high-priority items related to the client presentation, 2 medium-priority items for the product launch, and 3 low-priority administrative tasks. The client presentation tasks should be your focus today as they're due by tomorrow.";
                } else if (message.includes("meeting") || message.includes("meetings")) {
                    return "Effective meeting strategies include: 1) Always have a clear agenda, 2) Start and end on time, 3) Assign action items with owners before adjourning, 4) Consider if the meeting could be an email instead, 5) Use the 'parking lot' technique for off-topic discussions.";
                } else if (message.includes("stress") || message.includes("overwhelmed")) {
                    return "To reduce work stress, try: 1) Breaking large projects into smaller, manageable tasks, 2) Practicing mindfulness or brief meditation sessions during breaks, 3) Setting boundaries between work and personal time, 4) Regularly reviewing and adjusting priorities, 5) Delegating tasks when possible.";
                } else if (message.includes("organize") || message.includes("organization")) {
                    return "The best way to organize your tasks is to categorize them by project, then prioritize them using the criteria of urgency and importance. You can also color-code tasks by category or use a tagging system to filter tasks based on context (e.g., #office, #home, #phone).";
                } else if (message.includes("daily routine") || message.includes("schedule")) {
                    return "A productive daily routine might look like this: Morning - handle creative work when your mind is fresh; Midday - schedule meetings and collaborative work; Afternoon - tackle administrative tasks; End of day - plan tomorrow and reflect on accomplishments.";
                } else if (message.includes("track progress") || message.includes("tracking")) {
                    return "To track your project progress effectively, consider using a Kanban board with 'To Do', 'In Progress', and 'Done' columns. Update it daily, measure progress against milestones, and use burndown charts to visualize how much work remains versus time left.";
                } else if (message.includes("productivity tool") || message.includes("tools")) {
                    return "Recommended productivity tools include: Notion for documentation and project management, Todoist for task management, RescueTime for time tracking, Forest for focus sessions, and Calendly for scheduling. Which area would you like more specific recommendations for?";
                } else if (message.includes("plan") || message.includes("workweek")) {
                    return "For planning your workweek, try the '3+2 strategy': Plan 3 major outcomes to accomplish and 2 secondary tasks for each day. Begin by reviewing project deadlines and identifying your most important priorities. Then, allocate your most productive hours to your most challenging tasks.";
                } else if (message.includes("delegate") || message.includes("delegation")) {
                    return "To delegate tasks effectively: 1) Be clear about outcomes, not methods; 2) Choose the right person based on skills and workload; 3) Provide necessary context and resources; 4) Set clear deadlines; 5) Establish check-in points; 6) Provide feedback after completion.";
                } else if (message.includes("goal") || message.includes("goals")) {
                    return "Effective goal-setting strategies include the SMART framework (Specific, Measurable, Achievable, Relevant, Time-bound) and the OKR method (Objectives and Key Results). For your current projects, I recommend setting quarterly goals with monthly milestones to track progress.";
                } else if (message.includes("work-life balance") || message.includes("balance")) {
                    return "To maintain work-life balance: 1) Set clear working hours and honor them; 2) Take your allocated vacation time; 3) Create dedicated spaces for work and relaxation; 4) Practice transition rituals between work and personal time; 5) Schedule personal activities with the same importance as work meetings.";
                } else if (message.includes("email") || message.includes("inbox")) {
                    return "Email management tips: 1) Process emails in batches 2-3 times daily rather than constantly; 2) Use the 4D method: Delete, Delegate, Defer, or Do; 3) Create email templates for common responses; 4) Use folders or labels to organize by project or action needed; 5) Aim for inbox zero weekly, not daily.";
                } else if (message.includes("brainstorm") || message.includes("creative")) {
                    return "Effective brainstorming techniques include: 1) Mind mapping to visualize connections; 2) The '6-3-5' method where 6 people write 3 ideas in 5 minutes; 3) 'Reverse brainstorming' where you identify how to cause a problem; 4) 'SCAMPER' technique for idea modification; 5) 'Random word' stimulation.";
                } else if (message.includes("motivation") || message.includes("motivated")) {
                    return "To stay motivated on long-term projects: 1) Break the project into smaller milestones and celebrate each completion; 2) Connect daily tasks to the larger purpose; 3) Use the 'Seinfeld Strategy' of maintaining a chain of productive days; 4) Find an accountability partner; 5) Regularly review and acknowledge progress.";
                }
                
                // Generic responses for other queries
                else if (message.includes("hello") || message.includes("hi") || message.includes("hey")) {
                    return "Hello! How can I assist you with your tasks or productivity today?";
                } else if (message.includes("thank")) {
                    return "You're welcome! I'm happy to help. Is there anything else you'd like assistance with?";
                } else if (message.includes("help")) {
                    return "I can help you manage tasks, suggest productivity improvements, answer questions, and provide guidance on work organization. Try asking me about time management, task prioritization, or how to stay focused!";
                } else if (message.includes("add task") || message.includes("create task") || message.includes("new task")) {
                    openTaskModal();
                    return "I've opened the task creation form for you. Please fill in the details to add a new task.";
                } else {
                    return "I understand you're asking about '" + message + "'. This seems like an interesting topic. Would you like me to help you organize this as a task or provide some productivity tips related to this?";
                }
            }

            // Task Management Functions
            function openTaskModal(taskId = null) {
                const modalTitle = document.getElementById('modal-title');
                const taskForm = document.getElementById('task-form');
                currentTaskId = taskId;
                
                if (taskId) {
                    // Edit existing task
                    modalTitle.textContent = 'Edit Task';
                    document.getElementById('task-id').value = taskId;
                    
                    // Fetch task data
                    const taskItem = document.querySelector(`.task-item[data-id="${taskId}"]`);
                    const taskTitle = taskItem.querySelector('.task-title').textContent;
                    const taskDescription = taskItem.querySelector('.task-description') ? 
                                            taskItem.querySelector('.task-description').textContent : '';
                    const taskStatus = taskItem.querySelector('.task-status').classList[1];
                    
                    // Fill form
                    document.getElementById('task-title').value = taskTitle;
                    document.getElementById('task-description').value = taskDescription;
                    document.getElementById('task-status').value = taskStatus;
                } else {
                    // New task
                    modalTitle.textContent = 'Add New Task';
                    taskForm.reset();
                    document.getElementById('task-id').value = '';
                }
                
                taskModal.classList.add('show');
            }

            function closeTaskModal() {
                taskModal.classList.remove('show');
            }

            function saveTaskData() {
                const taskId = document.getElementById('task-id').value;
                const title = document.getElementById('task-title').value.trim();
                const description = document.getElementById('task-description').value.trim();
                const status = document.getElementById('task-status').value;
                
                if (title === '') {
                    alert('Please enter a task title');
                    return;
                }
                
                // Create form data
                const formData = new FormData();
                formData.append('title', title);
                formData.append('description', description);
                formData.append('status', status);
                
                if (taskId) {
                    formData.append('task_id', taskId);
                    formData.append('action', 'update_task');
                } else {
                    formData.append('action', 'add_task');
                }
                
                // Send AJAX request
                fetch('dashboard.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the task list
                        loadTasks();
                        closeTaskModal();
                    } else {
                        alert(data.message || 'Error saving task');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while saving the task');
                });
            }

            function deleteTask(taskId) {
                if (confirm('Are you sure you want to delete this task?')) {
                    // Create form data
                    const formData = new FormData();
                    formData.append('task_id', taskId);
                    formData.append('action', 'delete_task');
                    
                    // Send AJAX request
                    fetch('dashboard.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update the task list
                            loadTasks();
                        } else {
                            alert(data.message || 'Error deleting task');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting the task');
                    });
                }
            }

            function toggleTaskCompletion(taskId, isCompleted) {
                // Create form data
                const formData = new FormData();
                formData.append('task_id', taskId);
                formData.append('status', isCompleted ? 'completed' : 'pending');
                formData.append('action', 'update_task_status');
                
                // Send AJAX request
                fetch('dashboard.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the task list
                        loadTasks();
                    } else {
                        alert(data.message || 'Error updating task status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the task status');
                });
            }

            function loadTasks() {
                // Send AJAX request to get tasks
                fetch('dashboard.php?action=get_tasks')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const taskList = document.getElementById('task-list');
                        
                        // Clear existing tasks
                        taskList.innerHTML = '';
                        
                        // Add tasks to list
                        if (data.tasks.length > 0) {
                            data.tasks.forEach(task => {
                                const taskItem = document.createElement('li');
                                taskItem.className = `task-item ${task.status === 'completed' ? 'completed-task' : ''}`;
                                taskItem.setAttribute('data-id', task.id);
                                
                                const taskCheckbox = document.createElement('div');
                                taskCheckbox.className = 'task-checkbox';
                                
                                const checkbox = document.createElement('input');
                                checkbox.type = 'checkbox';
                                checkbox.className = 'task-complete-checkbox';
                                checkbox.checked = task.status === 'completed';
                                taskCheckbox.appendChild(checkbox);
                                
                                const taskContent = document.createElement('div');
                                taskContent.className = 'task-content';
                                
                                const taskTitle = document.createElement('div');
                                taskTitle.className = 'task-title';
                                taskTitle.textContent = task.title;
                                taskContent.appendChild(taskTitle);
                                
                                if (task.description) {
                                    const taskDescription = document.createElement('div');
                                    taskDescription.className = 'task-description';
                                    taskDescription.textContent = task.description;
                                    taskContent.appendChild(taskDescription);
                                }
                                
                                const taskStatus = document.createElement('div');
                                taskStatus.className = `task-status ${task.status}`;
                                taskStatus.textContent = task.status === 'pending' ? 'Pending' : 
                                                        task.status === 'in_progress' ? 'In Progress' : 'Completed';
                                
                                const taskActions = document.createElement('div');
                                taskActions.className = 'task-actions';
                                
                                const editBtn = document.createElement('button');
                                editBtn.className = 'task-action-btn edit-task-btn';
                                editBtn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M18.5 2.5C18.8978 2.10217 19.4374 1.87868 20 1.87868C20.5626 1.87868 21.1022 2.10217 21.5 2.5C21.8978 2.89782 22.1213 3.43739 22.1213 4C22.1213 4.56261 21.8978 5.10218 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>`;
                                
                                const deleteBtn = document.createElement('button');
                                deleteBtn.className = 'task-action-btn delete-task-btn';
                                deleteBtn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>`;
                                
                                taskActions.appendChild(editBtn);
                                taskActions.appendChild(deleteBtn);
                                
                                taskItem.appendChild(taskCheckbox);
                                taskItem.appendChild(taskContent);
                                taskItem.appendChild(taskStatus);
                                taskItem.appendChild(taskActions);
                                
                                taskList.appendChild(taskItem);
                            });
                        } else {
                            const noTasksMessage = document.createElement('li');
                            noTasksMessage.id = 'no-tasks-message';
                            noTasksMessage.textContent = 'No tasks available. Add some tasks to get started!';
                            taskList.appendChild(noTasksMessage);
                        }
                    } else {
                        alert(data.message || 'Error loading tasks');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while loading tasks');
                });
            }

            // Event Listeners
            sendBtn.addEventListener('click', sendMessage);
            
            chatInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
            
            voiceBtn.addEventListener('click', function() {
                if (isListening) {
                    recognition.stop();
                    isListening = false;
                    voiceBtn.classList.remove('listening');
                } else {
                    recognition.start();
                    isListening = true;
                    voiceBtn.classList.add('listening');
                    chatInput.placeholder = 'Listening...';
                    chatInput.focus();
                }
            });
            
            addTaskBtn.addEventListener('click', function() {
                openTaskModal();
            });
            
            closeModal.addEventListener('click', closeTaskModal);
            cancelTask.addEventListener('click', closeTaskModal);
            
            saveTask.addEventListener('click', saveTaskData);
            
            // Task list event delegation
            taskList.addEventListener('click', function(e) {
                const taskItem = e.target.closest('.task-item');
                if (!taskItem) return;
                
                const taskId = taskItem.getAttribute('data-id');
                
                if (e.target.classList.contains('edit-task-btn') || e.target.closest('.edit-task-btn')) {
                    openTaskModal(taskId);
                } else if (e.target.classList.contains('delete-task-btn') || e.target.closest('.delete-task-btn')) {
                    deleteTask(taskId);
                } else if (e.target.classList.contains('task-complete-checkbox')) {
                    toggleTaskCompletion(taskId, e.target.checked);
                }
            });
            
            // Suggested questions
            questionItems.forEach(function(item) {
                item.addEventListener('click', function() {
                    const question = this.getAttribute('data-question');
                    chatInput.value = question;
                    sendMessage();
                });
            });
            
            // Logout button
            logoutBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to logout?')) {
                    // Create form data
                    const formData = new FormData();
                    formData.append('action', 'logout');
                    
                    // Send AJAX request
                    fetch('auth.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = 'index.php';
                        } else {
                            alert(data.message || 'Error during logout');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred during logout');
                    });
                }
            });

            // Handle Ajax requests from PHP
            <?php
            // AJAX endpoint for task management
            if (isset($_GET['action']) && $_GET['action'] === 'get_tasks') {
                $tasks = [];
                $stmt = $conn->prepare("SELECT id, title, description, status, created_at FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $tasks[] = $row;
                    }
                }
                
                echo json_encode(['success' => true, 'tasks' => $tasks]);
                exit;
            }

            if (isset($_POST['action'])) {
                $action = $_POST['action'];
                
                switch ($action) {
                    case 'add_task':
                        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
                        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
                        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
                        
                        if (empty($title)) {
                            echo json_encode(['success' => false, 'message' => 'Title is required']);
                            exit;
                        }
                        
                        $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, status) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("isss", $user_id, $title, $description, $status);
                        
                        if ($stmt->execute()) {
                            echo json_encode(['success' => true]);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Error adding task']);
                        }
                        exit;
                        
                    case 'update_task':
                        $task_id = filter_input(INPUT_POST, 'task_id', FILTER_SANITIZE_NUMBER_INT);
                        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
                        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
                        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
                        
                        if (empty($task_id) || empty($title)) {
                            echo json_encode(['success' => false, 'message' => 'Task ID and title are required']);
                            exit;
                        }
                        
                        $stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ?, status = ? WHERE id = ? AND user_id = ?");
                        $stmt->bind_param("sssii", $title, $description, $status, $task_id, $user_id);
                        
                        if ($stmt->execute()) {
                            echo json_encode(['success' => true]);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Error updating task']);
                        }
                        exit;
                        
                    case 'delete_task':
                        $task_id = filter_input(INPUT_POST, 'task_id', FILTER_SANITIZE_NUMBER_INT);
                        
                        if (empty($task_id)) {
                            echo json_encode(['success' => false, 'message' => 'Task ID is required']);
                            exit;
                        }
                        
                        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
                        $stmt->bind_param("ii", $task_id, $user_id);
                        
                        if ($stmt->execute()) {
                            echo json_encode(['success' => true]);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Error deleting task']);
                        }
                        exit;
                        
                    case 'update_task_status':
                        $task_id = filter_input(INPUT_POST, 'task_id', FILTER_SANITIZE_NUMBER_INT);
                        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
                        
                        if (empty($task_id) || empty($status)) {
                            echo json_encode(['success' => false, 'message' => 'Task ID and status are required']);
                            exit;
                        }
                        
                        $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
                        $stmt->bind_param("sii", $status, $task_id, $user_id);
                        
                        if ($stmt->execute()) {
                            echo json_encode(['success' => true]);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Error updating task status']);
                        }
                        exit;
                }
            }
            ?>
        });
    </script>
</body>
</html>
