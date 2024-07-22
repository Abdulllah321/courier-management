<?php
session_start();
include_once '../../config/database.php';
include_once '../../includes/functions.php';
include_once "../create_notification.php";
redirectIfNotLoggedIn();
$pageTitle = 'Add Branch';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();

    $branch_name = $_POST['branch_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state_province = $_POST['state_province'];
    $zip_postal_code = $_POST['zip_postal_code'];
    $country = $_POST['country'];
    $contact_person = $_POST['contact_person'];
    $phone_number = $_POST['phone_number'];
    $email_address = $_POST['email_address'];
    $branch_type = $_POST['branch_type'];
    $status = $_POST['status'];
    $branch_manager = $_POST['branch_manager'];

    // Fetch the latest branch ID
    $query = "SELECT MAX(id) as max_id FROM branches";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $newBranchId = $result['max_id'] + 1;

    // Insert the new branch
    $query = "INSERT INTO branches (branch_name, address, city, state_province, zip_postal_code, country, contact_person, phone_number, email_address, branch_type, status, branch_manager) VALUES (:branch_name, :address, :city, :state_province, :zip_postal_code, :country, :contact_person, :phone_number, :email_address, :branch_type, :status, :branch_manager)";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':branch_name', $branch_name);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':state_province', $state_province);
    $stmt->bindParam(':zip_postal_code', $zip_postal_code);
    $stmt->bindParam(':country', $country);
    $stmt->bindParam(':contact_person', $contact_person);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':email_address', $email_address);
    $stmt->bindParam(':branch_type', $branch_type);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':branch_manager', $branch_manager);

    if ($stmt->execute()) {
        $success = "Branch added successfully.";

        $message = "A new branch '{$branch_name}' has been added.";
        $userId = $_SESSION['admin_id'] ?? $_SESSION['agent_id'];
        $url = "view_branch.php?id={$newBranchId}";
        try {
            createNotification($db, $userId, $message, $status = 'unread', $url);
        } catch (Exception $e) {
            $error = "Notification error: " . $e->getMessage();
        }
    } else {
        $error = "Error: " . $stmt->errorInfo()[2];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include_once '../includes/head.php'; ?>

<body class="bg-gray-100 flex">
    <?php include '../includes/sidebar.php'; ?>
    <?php include "../includes/topbar.php"; ?>

    <!-- Main Content -->
    <main class="ml-64 p-8 w-full">
        <h1 class="text-3xl font-bold mb-4">Add Branch</h1>

        <?php if (!empty($success)) : ?>
            <div id="toast-success" class="fixed bottom-4 left-1/2 -translate-x-1/2 bg-green-500 text-white p-4 rounded shadow-lg transition duration-300 transform">
                <?php echo htmlspecialchars($success); ?>
            </div>
            <script>
                setTimeout(() => {
                    const toast = document.getElementById('toast-success');
                    if (toast) {
                        toast.classList.add('opacity-0', 'translate-y-2');
                        setTimeout(() => toast.remove(), 300);
                    }
                }, 3000);
            </script>
        <?php endif; ?>
        <?php if (!empty($error)) : ?>
            <div id="toast-error" class="fixed top-15 right-4 bg-red-500 text-white p-4 rounded shadow-lg transition duration-300 transform">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <script>
                setTimeout(() => {
                    const toast = document.getElementById('toast-error');
                    if (toast) {
                        toast.classList.add('opacity-0', 'translate-y-2');
                        setTimeout(() => toast.remove(), 300);
                    }
                }, 3000);
            </script>
        <?php endif; ?>

        <form method="post" action="" class="bg-white p-8 rounded shadow-md" id="branchForm">
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="branch_name" class="block text-gray-700">Branch Name:</label>
                    <input type="text" name="branch_name" id="branch_name" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                </div>
                <div>
                    <label for="address" class="block text-gray-700">Address:</label>
                    <input type="text" name="address" id="address" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                </div>
                <div>
                    <label for="city" class="block text-gray-700">City:</label>
                    <input type="text" name="city" id="city" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                </div>
                <div>
                    <label for="state_province" class="block text-gray-700">State/Province:</label>
                    <input type="text" name="state_province" id="state_province" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                </div>
                <div>
                    <label for="zip_postal_code" class="block text-gray-700">Zip/Postal Code:</label>
                    <input type="text" name="zip_postal_code" id="zip_postal_code" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                </div>
                <div>
                    <label for="country" class="block text-gray-700">Country:</label>
                    <input type="text" name="country" id="country" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                </div>
                <div>
                    <label for="contact_person" class="block text-gray-700">Contact Person:</label>
                    <input type="text" name="contact_person" id="contact_person" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                </div>
                <div>
                    <label for="phone_number" class="block text-gray-700">Phone Number:</label>
                    <input type="text" name="phone_number" id="phone_number" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                </div>
                <div>
                    <label for="email_address" class="block text-gray-700">Email Address:</label>
                    <input type="email" name="email_address" id="email_address" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                </div>
                <div>
                    <label for="branch_type" class="block text-gray-700">Branch Type:</label>
                    <select name="branch_type" id="branch_type" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                        <option value="" disabled selected>Please select Branch</option>
                        <option value="main">Main</option>
                        <option value="sub">Sub</option>
                        <option value="franchise">Franchise</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-gray-700">Status:</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                        <option value="" disabled selected>Please select Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div>
                    <label for="branch_manager" class="block text-gray-700">Branch Manager:</label>
                    <input type="text" name="branch_manager" id="branch_manager" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                </div>
                <div class="mt-4 flex gap-4">
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700" id="submitBtn">
                        <span class="submit-text">Add Branch</span>
                        <span class="spinner hidden">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                        </span>
                    </button>
                    <a href="manage_branches.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700 flex items-center justify-center">
                        cancel
                    </a>
                </div>
            </div>
        </form>
    </main>
    <?php include_once '../includes/script.php'; ?>

    <script>
        const branchForm = document.getElementById('branchForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = submitBtn.querySelector('.submit-text');
        const spinner = submitBtn.querySelector('.spinner');

        branchForm.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitText.classList.add('hidden');
            spinner.classList.remove('hidden');
        });
    </script>
</body>

</html>