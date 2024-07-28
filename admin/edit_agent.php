<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
include_once './create_notification.php';
redirectIfNotLoggedIn();

$database = new Database();
$db = $database->getConnection();

$agentId = $_GET['id'] ?? null;
if (!$agentId) {
    header("Location: manage_agents.php");
    exit();
}

$pageTitle = 'Edit Agent';

// Fetch existing agent data
$agentQuery = "SELECT username FROM agents WHERE id = :id";
$agentStmt = $db->prepare($agentQuery);
$agentStmt->bindParam(':id', $agentId);
$agentStmt->execute();
$agentData = $agentStmt->fetch(PDO::FETCH_ASSOC);

if (!$agentData) {
    $error = "Agent not found.";
} else {
    // Fetch all branches
    $branchQuery = "SELECT id, branch_name FROM branches";
    $branchStmt = $db->prepare($branchQuery);
    $branchStmt->execute();
    $allBranches = $branchStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch existing branch IDs for the agent
    $branchIdsQuery = "SELECT branch_id FROM agent_branches WHERE agent_id = :id";
    $branchIdsStmt = $db->prepare($branchIdsQuery);
    $branchIdsStmt->bindParam(':id', $agentId);
    $branchIdsStmt->execute();
    $selectedBranchIds = $branchIdsStmt->fetchAll(PDO::FETCH_COLUMN);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $branch_ids = $_POST['branch_ids'] ?? [];

        // Check if username already exists
        $checkQuery = "SELECT COUNT(*) FROM agents WHERE username = :username AND id != :id";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':username', $username);
        $checkStmt->bindParam(':id', $agentId);
        $checkStmt->execute();
        $usernameExists = $checkStmt->fetchColumn();

        if ($usernameExists > 0) {
            $error = "Username already exists. Please choose a different username.";
        } else {
            $query = "UPDATE agents SET username = :username" . (!empty($password) ? ", password = :password" : "") . " WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $username);
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hashing the password
                $stmt->bindParam(':password', $hashedPassword);
            }
            $stmt->bindParam(':id', $agentId);

            if ($stmt->execute()) {
                // Remove existing branches
                $deleteBranchQuery = "DELETE FROM agent_branches WHERE agent_id = :id";
                $deleteBranchStmt = $db->prepare($deleteBranchQuery);
                $deleteBranchStmt->bindParam(':id', $agentId);
                $deleteBranchStmt->execute();

                // Insert new branches into the junction table
                $branchQuery = "INSERT INTO agent_branches (agent_id, branch_id) VALUES (:agent_id, :branch_id)";
                $branchStmt = $db->prepare($branchQuery);
                foreach ($branch_ids as $branchId) {
                    $branchStmt->bindParam(':agent_id', $agentId);
                    $branchStmt->bindParam(':branch_id', $branchId);
                    $branchStmt->execute();
                }

                // Create notification
                $message = "Agent '{$username}' has been updated.";
                $userId = $_SESSION['admin_id'] ?? $_SESSION['agent_id'];
                $url = "manage_agents.php";
                try {
                    createNotification($db, $userId, $message, 'unread', $url);
                } catch (Exception $e) {
                    $error = "Notification error: " . $e->getMessage();
                }

                header("Location: manage_agents.php");
                exit();
            } else {
                $error = "Failed to update agent.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once '../includes/head.php'; ?>

<body class="bg-gray-100">
    <!-- Sidebar and Topbar -->
    <?php include "../includes/sidebar.php"; ?>
    <?php include "../includes/topbar.php"; ?>

    <main class="ml-64 p-6 mt-16">

        <h1 class="text-3xl font-bold mb-4">Edit Agent</h1>

        <form method="post" id="agent-form" class="bg-white shadow-md rounded-lg p-6">
            <?php if (isset($error)): ?>
                <div class="mb-4 text-red-600">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <div class="mb-4">
                <label for="username-input" class="block text-gray-700 font-semibold mb-2">Username</label>
                <input type="text" id="username-input" name="username"
                    class="w-full border border-gray-300 rounded-md p-2"
                    value="<?php echo htmlspecialchars($agentData['username']); ?>" required>
                <div id="username-status" class="mt-2 text-red-500"></div>
            </div>
            <div class="mb-4">
                <label for="password-input" class="block text-gray-700 font-semibold mb-2">Password</label>
                <input type="password" id="password-input" name="password"
                    class="w-full border border-gray-300 rounded-md p-2"
                    placeholder="Leave blank to keep current password">
            </div>
            <div class="mb-4">
                <label for="branch_ids" class="block text-gray-700 font-semibold mb-2">Branches</label>
                <select id="branch_ids" name="branch_ids[]" class="w-full border border-gray-300 rounded-md p-2"
                    multiple required>
                    <?php foreach ($allBranches as $branch): ?>
                        <option value="<?php echo $branch['id']; ?>" <?php echo in_array($branch['id'], $selectedBranchIds) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($branch['branch_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" id="submit-button"
                class="bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 transition duration-300 relative">
                <span class="submit-text">Update Agent </span>
            </button>
        </form>
    </main>
    <?php include_once '../includes/script.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('agent-form');
            const submitButton = document.getElementById('submit-button');
            const usernameInput = document.getElementById('username-input');
            const usernameStatus = document.getElementById('username-status');

            form.addEventListener('submit', function () {
                submitButton.innerHTML = `
                    <span id="spinner"
                        class="relative inset-0 flex items-center justify-center">
                        <svg aria-hidden="true" class="w-5 h-5 text-red-200 animate-spin fill-white"
                            viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor" />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill" />
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </span>`;
                submitButton.disabled = true;
            });

            usernameInput.addEventListener('input', function () {
                const username = usernameInput.value.trim();
                if (username.length < 3) {
                    usernameStatus.textContent = 'Username must be at least three letters';
                    isUsernameAvailable = false;
                    return;
                }

                usernameStatus.innerHTML = `
                <span class="text-gray-500 flex gap-2 items-center">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-sky-500"></span>
                    </span> Checking...
                </span>`;
                fetch('check_username.php?username=' + encodeURIComponent(username))
                    .then(response => response.json())
                    .then(data => {
                        if (data.available) {
                            usernameStatus.innerHTML = '<span class="text-green-500">Username available <svg class="inline-block w-4 h-4 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" d="M5 13l4 4L19 7"/></svg></span>';
                            isUsernameAvailable = true;
                        } else {
                            usernameStatus.innerHTML = '<span class="text-red-500">Username not available <svg class="inline-block w-4 h-4 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></span>';
                            isUsernameAvailable = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error checking username:', error);
                        usernameStatus.innerHTML = '<span class="text-red-500">Error checking username</span>';
                        isUsernameAvailable = false;
                    });
            });
        });
    </script>
</body>

</html>