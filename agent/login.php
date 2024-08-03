<?php
session_start();
include "../includes/header.php";
include_once '../config/database.php';
include_once '../includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM agents WHERE username = :username";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $row['password'])) {
            $_SESSION['agent_id'] = $row['id'];
            $_SESSION['agent_username'] = $row['username']; // Store username for the session

            $_SESSION['agent_username'] = $row['username'];
            $_SESSION['role'] = "agent";
            addSessionMessage('success', 'Login successful. Welcome back!');
            header("Location: dashboard.php");
            exit;
        } else {
            addSessionMessage('error', 'Invalid username or password.');
        }
    } else {
        addSessionMessage('error', 'No user found with that username.');
    }
}
?>

<?php displaySessionMessages(); ?>
<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow-md w-96">
        <h2 class="text-2xl font-bold text-center text-red-600 mb-6">Agent Login</h2>
        <form method="post" action="">
            <div class="mb-4">
                <label for="username" class="block text-gray-700">Username</label>
                <input type="text" name="username"
                    class="w-full px-3 py-2 border border-gray-300 rounded ring-1 outline-none ring-transparent transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-600" autocomplete="username"
                    required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700">Password</label>
                <input type="password" name="password"
                    class="w-full px-3 py-2 border border-gray-300 rounded ring-1 outline-none ring-transparent transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-600" autocomplete="current-password"
                    required>
            </div>
            <button type="submit"
                class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 transition duration-300">
                Login
            </button>
        </form>
    </div>
</div>
<?php include "../includes/script.php" ?>


<?php include "../includes/footer.php"; ?>