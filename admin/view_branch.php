<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedIn();
$pageTitle = 'View Branch';

$database = new Database();
$db = $database->getConnection();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_branches.php");
    exit();
}

$branchId = intval($_GET['id']);

// Fetch branch details
$query = "SELECT * FROM branches WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $branchId, PDO::PARAM_INT);
$stmt->execute();
$branch = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$branch) {
    header("Location: manage_branches.php");
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
        <h1 class="text-3xl font-bold mb-6">Branch Details</h1>

        <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
            <div class="mb-4">
                <a href="manage_branches.php" class="inline-block bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 transition duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Branch List
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-50 p-4 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Branch Information</h2>
                    <p><strong class="font-medium">Branch Name:</strong> <?php echo htmlspecialchars($branch['branch_name']); ?></p>
                    <p><strong class="font-medium">Branch Type:</strong> <?php echo htmlspecialchars(ucfirst($branch['branch_type'])); ?></p>
                    <p><strong class="font-medium">Status:</strong>
                        <span class="<?php echo htmlspecialchars($branch['status']) === 'active' ? 'text-green-600' : 'text-red-600'; ?>">
                            <?php echo htmlspecialchars(ucfirst($branch['status'])); ?>
                        </span>
                    </p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Contact Information</h2>
                    <p><strong class="font-medium">Contact Person:</strong> <?php echo htmlspecialchars($branch['contact_person']); ?></p>
                    <p><strong class="font-medium">Phone Number:</strong> <?php echo htmlspecialchars($branch['phone_number']); ?></p>
                    <p><strong class="font-medium">Email Address:</strong> <?php echo htmlspecialchars($branch['email_address']); ?></p>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg shadow-md mb-6">
                <h2 class="text-xl font-semibold mb-4">Address</h2>
                <p><strong class="font-medium">Address:</strong> <?php echo htmlspecialchars($branch['address']); ?></p>
                <p><strong class="font-medium">City:</strong> <?php echo htmlspecialchars($branch['city']); ?></p>
                <p><strong class="font-medium">State/Province:</strong> <?php echo htmlspecialchars($branch['state_province']); ?></p>
                <p><strong class="font-medium">Zip/Postal Code:</strong> <?php echo htmlspecialchars($branch['zip_postal_code']); ?></p>
                <p><strong class="font-medium">Country:</strong> <?php echo htmlspecialchars($branch['country']); ?></p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Branch Manager</h2>
                <p><strong class="font-medium">Manager Name:</strong> <?php echo htmlspecialchars($branch['branch_manager']); ?></p>
            </div>
        </div>
    </main>
</body>

</html>