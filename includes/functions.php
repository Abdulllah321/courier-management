<?php

function isLoggedIn()
{
    return isset($_SESSION['admin_id']);
}

function isLoggedInAsAgent()
{
    return isset($_SESSION['agent_id']);
}

function redirectIfNotLoggedIn()
{
    if (!isLoggedIn()) {
        addSessionMessage('error', 'You must be logged in as an admin to access this page.');
        header("Location: login.php");
        exit;
    }
}

function redirectIfNotLoggedInAgent()
{
    if (!isLoggedInAsAgent()) {
        addSessionMessage('error', 'You must be logged in as an agent to access this page.');
        header("Location: login.php");
        exit;
    }
}

function redirectToDashboard()
{
    if (isLoggedIn()) {
        header("Location: ../admin/dashboard.php");
    } elseif (isLoggedInAsAgent()) {
        header("Location: ../agent/dashboard.php");
    } else {
        addSessionMessage('error', 'You are not authorized to access this page.');
        header("Location: login.php");
    }
    exit;
}

function checkAccess($allowedRoles)
{
    $userRole = isset($_SESSION['role']) ? $_SESSION['role'] : null;

    if (!in_array($userRole, $allowedRoles)) {
        addSessionMessage('error', 'You do not have permission to access this page.');
        header("Location: ../public/index.php");
        exit;
    }
}

function addSessionMessage($type, $message, $duration = 10)
{
    if (!isset($_SESSION['session_messages'])) {
        $_SESSION['session_messages'] = [];
    }
    // Create a unique ID for each message
    $id = uniqid();
    $_SESSION['session_messages'][] = ['id' => $id, 'type' => $type, 'message' => $message, 'duration' => $duration];
}

function displaySessionMessages()
{
    if (isset($_SESSION['session_messages']) && !empty($_SESSION['session_messages'])) {
        foreach ($_SESSION['session_messages'] as $msg) {
            $id = htmlspecialchars($msg['id']);
            $type = htmlspecialchars($msg['type']);
            $message = htmlspecialchars($msg['message']);
            $duration = htmlspecialchars($msg['duration']);

            // Determine Tailwind classes based on the type of message
            $typeClasses = [
                'success' => 'bg-green-100 border border-green-400 text-green-700',
                'error' => 'bg-red-100 border border-red-400 text-red-700',
                'warning' => 'bg-yellow-100 border border-yellow-400 text-yellow-700',
                'info' => 'bg-blue-100 border border-blue-400 text-blue-700'
            ];
            $class = $typeClasses[$type] ?? $typeClasses['info'];

            echo "<div id='msg-{$id}' class='session-message {$class} p-4 mb-4 rounded relative' data-duration='{$duration}'>
                    <span>{$message}</span>
                    <button type='button' class='close absolute top-0 right-0 mt-1 mr-1 text-black'>
                        <span>&times;</span>
                    </button>
                    <div class='progress-bar h-1 mt-2 bg-gray-200 rounded-full mx-auto left-1/2 -translate-x-1/2 !absolute bottom-2'>
                        <div class='progress bg-green-500 h-full rounded-full transition-all duration-300'></div>
                    </div>
                  </div>";
        }
        // Clear session messages
        $_SESSION['session_messages'] = [];
    }
}
?>
