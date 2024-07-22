<?php
include "../includes/header.php";
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';



if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM admins WHERE username = :username";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // Directly compare the plain text passwords
        if ($password === $row['password']) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['username'] = $row['username']; // Store username for the topbar

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid Password!";
        }
    } else {
        $error = "No User Found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../public/css/styles.css">
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded shadow-md w-96">
            <h2 class="text-2xl font-bold text-center text-red-600 mb-6">Admin Login</h2>
            <?php if (isset($error)) : ?>
                <div class="bg-red-100 text-red-700 p-2 mb-4 rounded"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700">Username</label>
                    <input type="text" name="username" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-red-600" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700">Password</label>
                    <input type="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-red-600" required>
                </div>
                <button type="submit" class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 transition duration-300">Login</button>
            </form>
        </div>
    </div>
</body>

</html>
\
<?php include "../includes/footer.php"; ?>