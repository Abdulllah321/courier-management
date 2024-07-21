<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedIn();
$pageTitle = 'Add New Agent';

$database = new Database();
$db = $database->getConnection();

// Fetch branches for the branch selection dropdown
$branchQuery = "SELECT id, branch_name FROM branches";
$branchStmt = $db->prepare($branchQuery);
$branchStmt->execute();
$branches = $branchStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $branch_id = $_POST['branch_id'];

    // Insert new agent
    $query = "INSERT INTO agents (username, branch_id) VALUES (:username, :branch_id)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':branch_id', $branch_id);
    if ($stmt->execute()) {
        header("Location: manage_agents.php");
        exit();
    } else {
        $error = "Failed to add new agent.";
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
        <h1 class="text-3xl font-bold mb-4">Add New Agent</h1>

        <form method="post" class="bg-white shadow-md rounded-lg p-6">
            <?php if (isset($error)) : ?>
                <div class="mb-4 text-red-600"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <div class="mb-4">
                <label for="username" class="block text-gray-700 font-semibold mb-2">Username</label>
                <input type="text" id="username" name="username" class="w-full border border-gray-300 rounded-md p-2" required>
            </div>
            <div class="mb-4">
                <label for="branch_id" class="block text-gray-700 font-semibold mb-2">Branch</label>
                <select id="branch_id" name="branch_id" class="w-full border border-gray-300 rounded-md p-2" required>
                    <option value="">Select Branch</option>
                    <?php foreach ($branches as $branch) : ?>
                        <option value="<?php echo $branch['id']; ?>"><?php echo $branch['branch_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 transition duration-300">Add Agent</button>
        </form>
    </main>
    <?php include_once '../includes/script.php'; ?>
</body>

</html>