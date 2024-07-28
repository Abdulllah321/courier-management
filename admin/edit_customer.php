<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedIn();
$pageTitle = "Customer Edit";

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Customer ID is required.";
    header("Location: manage_customers.php");
    exit();
}

$customerId = $_GET['id'];
$database = new Database();
$db = $database->getConnection();

// Fetch customer details
$query = "SELECT * FROM customers WHERE customer_id = :customer_id AND deleted = FALSE";
$stmt = $db->prepare($query);
$stmt->bindParam(':customer_id', $customerId);
$stmt->execute();
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    $_SESSION['error'] = "Customer not found.";
    header("Location: manage_customers.php");
    exit();
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
        <h1 class="text-3xl font-bold mb-4">Edit Customer</h1>

        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold mb-4">Customer Information</h2>

            <!-- Displaying Success/Error Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-500 text-white p-4 rounded-md mb-4">
                    <?php echo htmlspecialchars($_SESSION['success']);
                    unset($_SESSION['success']); ?>
                </div>
            <?php elseif (isset($_SESSION['error'])): ?>
                <div class="bg-red-500 text-white p-4 rounded-md mb-4">
                    <?php echo htmlspecialchars($_SESSION['error']);
                    unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="update_customer.php" method="POST">
                <input type="hidden" name="customer_id"
                    value="<?php echo htmlspecialchars($customer['customer_id']); ?>">

                <div class="mb-4">
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" id="first_name" name="first_name"
                        value="<?php echo htmlspecialchars($customer['first_name']); ?>"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                </div>

                <div class="mb-4">
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" id="last_name" name="last_name"
                        value="<?php echo htmlspecialchars($customer['last_name']); ?>"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email"
                        value="<?php echo htmlspecialchars($customer['email']); ?>"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                </div>

                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="tel" id="phone" name="phone"
                        value="<?php echo htmlspecialchars($customer['phone']); ?>"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                </div>

                <div class="mb-4">
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea id="address" name="address" rows="4"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required><?php echo htmlspecialchars($customer['address']); ?></textarea>
                </div>

                <button type="submit"
                    class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-blue-600">Update
                    Customer</button>
            </form>
        </div>
    </main>

    <?php include "../includes/script.php"; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const successMessage = document.getElementById('success-message');
            const errorMessage = document.getElementById('error-message');

            if (successMessage) {
                setTimeout(() => {
                    successMessage.remove();
                }, 1000); // Remove after 1 second
            }

            if (errorMessage) {
                setTimeout(() => {
                    errorMessage.remove();
                }, 1000); // Remove after 1 second
            }
        });
    </script>
</body>

</html>