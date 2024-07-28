<?php
session_start();
require '../config/database.php';
include "../includes/functions.php";

redirectIfNotLoggedIn();

$database = new Database();
$conn = $database->getConnection();

$mode = isset($_GET['id']) ? 'edit' : 'create';
$courier = null;

if ($mode === 'edit') {
    $courier_id = $_GET['id'];
    // Fetch courier details from the database using $courier_id
    $stmt = $conn->prepare("SELECT * FROM couriers WHERE courier_id = ?");
    $stmt->execute([$courier_id]);
    $courier = $stmt->fetch(PDO::FETCH_ASSOC);
}

$pageTitle = $mode === 'create' ? 'Add New Courier' : 'Edit Courier';

?>

<!DOCTYPE html>
<html lang="en">

<?php include_once '../includes/head.php'; ?>

<body class="bg-gray-100 min-h-screen">
    <!-- Sidebar and Topbar -->
    <?php include "../includes/sidebar.php"; ?>
    <?php include "../includes/topbar.php"; ?>

    <main class="ml-64 p-6 mt-16">
        <form id="courierDetailsForm" method="POST" action="courier_action.php">
            <h1 class="text-2xl font-bold mb-4">
                <?php echo $mode === 'create' ? 'Create Courier' : 'Edit Courier'; ?>
            </h1>
            <div class="flex space-x-4">
                <input type="hidden" name="mode" value="<?php echo $mode; ?>">
                <?php if ($mode === 'edit'): ?>
                    <input type="hidden" name="courier_id" value="<?php echo $courier_id; ?>">
                <?php endif; ?>

                <div class="flex-1">
                    <h2 class="text-xl font-semibold mb-2">Sender Details</h2>
                    <div class="border border-gray-300 p-6 rounded-lg bg-white shadow-sm">
                        <div class="mb-4">
                            <label for="sender_first_name" class="block text-gray-700 font-medium">First Name</label>
                            <input type="text" id="sender_first_name" name="sender_first_name"
                                class="form-input w-full px-3 py-2 border border-gray-300 rounded ring-1 focus:ring-sky-400 ring-transparent transition-all duration-300 outline-none mt-1"
                                value="<?php echo $mode === 'edit' ? htmlspecialchars($courier['sender_first_name']) : ''; ?>"
                                required>
                        </div>

                        <div class="mb-4">
                            <label for="sender_last_name" class="block text-gray-700 font-medium">Last Name</label>
                            <input type="text" id="sender_last_name" name="sender_last_name"
                                class="form-input w-full px-3 py-2 border border-gray-300 rounded ring-1 focus:ring-sky-400 ring-transparent transition-all duration-300 outline-none mt-1"
                                value="<?php echo $mode === 'edit' ? htmlspecialchars($courier['sender_last_name']) : ''; ?>"
                                required>
                        </div>

                        <div class="mb-4">
                            <label for="sender_email" class="block text-gray-700 font-medium">Email</label>
                            <input type="email" id="sender_email" name="sender_email"
                                class="form-input w-full px-3 py-2 border border-gray-300 rounded ring-1 focus:ring-sky-400 ring-transparent transition-all duration-300 outline-none mt-1"
                                value="<?php echo $mode === 'edit' ? htmlspecialchars($courier['sender_email']) : ''; ?>"
                                required>
                        </div>

                        <div class="mb-4">
                            <label for="sender_phone" class="block text-gray-700 font-medium">Phone</label>
                            <input type="text" id="sender_phone" name="sender_phone"
                                class="form-input w-full px-3 py-2 border border-gray-300 rounded ring-1 focus:ring-sky-400 ring-transparent transition-all duration-300 outline-none mt-1"
                                value="<?php echo $mode === 'edit' ? htmlspecialchars($courier['sender_phone']) : ''; ?>">
                        </div>

                        <div class=" mb-4">
                            <label for="sender_address" class="block text-gray-700 font-medium">Address</label>
                            <textarea id="sender_address" name="sender_address"
                                class="form-textarea w-full px-3 py-2 border border-gray-300 rounded ring-1 focus:ring-sky-400 ring-transparent transition-all duration-300 outline-none mt-1"
                                required><?php echo $mode === 'edit' ? htmlspecialchars($courier['sender_address']) : ''; ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="w-px bg-gray-300 mx-4"></div>

                <div class="flex-1">
                    <h2 class="text-xl font-semibold mb-2">Receiver Details</h2>
                    <div class="border border-gray-300 p-6 rounded-lg bg-white shadow-sm">
                        <div class="mb-4">
                            <label for="receiver_first_name" class="block text-gray-700 font-medium">First Name</label>
                            <input type="text" id="receiver_first_name" name="receiver_first_name"
                                class="form-input w-full px-3 py-2 border border-gray-300 rounded ring-1 focus:ring-sky-400 ring-transparent transition-all duration-300 outline-none mt-1"
                                value="<?php echo $mode === 'edit' ? htmlspecialchars($courier['receiver_first_name']) : ''; ?>"
                                required>
                        </div>

                        <div class="mb-4">
                            <label for="receiver_last_name" class="block text-gray-700 font-medium">Last Name</label>
                            <input type="text" id="receiver_last_name" name="receiver_last_name"
                                class="form-input w-full px-3 py-2 border border-gray-300 rounded ring-1 focus:ring-sky-400 ring-transparent transition-all duration-300 outline-none mt-1"
                                value="<?php echo $mode === 'edit' ? htmlspecialchars($courier['receiver_last_name']) : ''; ?>"
                                required>
                        </div>

                        <div class="mb-4">
                            <label for="receiver_email" class="block text-gray-700 font-medium">Email</label>
                            <input type="email" id="receiver_email" name="receiver_email"
                                class="form-input w-full px-3 py-2 border border-gray-300 rounded ring-1 focus:ring-sky-400 ring-transparent transition-all duration-300 outline-none mt-1"
                                value="<?php echo $mode === 'edit' ? htmlspecialchars($courier['receiver_email']) : ''; ?>"
                                required>
                        </div>

                        <div class="mb-4">
                            <label for="receiver_phone" class="block text-gray-700 font-medium">Phone</label>
                            <input type="text" id="receiver_phone" name="receiver_phone"
                                class="form-input w-full px-3 py-2 border border-gray-300 rounded ring-1 focus:ring-sky-400 ring-transparent transition-all duration-300 outline-none mt-1"
                                value="<?php echo $mode === 'edit' ? htmlspecialchars($courier['receiver_phone']) : ''; ?>">
                        </div>

                        <div class="mb-4">
                            <label for="receiver_address" class="block text-gray-700 font-medium">Address</label>
                            <textarea id="receiver_address" name="receiver_address"
                                class="form-textarea w-full px-3 py-2 border border-gray-300 rounded ring-1 focus:ring-sky-400 ring-transparent transition-all duration-300 outline-none mt-1"
                                required><?php echo $mode === 'edit' ? htmlspecialchars($courier['receiver_address']) : ''; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <h2 class="text-xl font-semibold mb-2">Courier Details</h2>
                <div class="bg-white border border-gray-300 p-6 rounded-lg shadow-sm">
                    <div class="mb-4">
                        <label for="weight" class="block text-gray-700 font-medium">Weight (kg)</label>
                        <input type="number" id="weight" name="weight"
                            class="form-input w-full px-3 py-2 border border-gray-300 rounded ring-1 focus:ring-sky-400 ring-transparent transition-all duration-300 outline-none mt-1"
                            step="0.01"
                            value="<?php echo $mode === 'edit' ? htmlspecialchars($courier['weight']) : ''; ?>"
                            required>
                    </div>

                    <div class="mb-4">
                        <label for="dimensions" class="block text-gray-700 font-medium">Dimensions (LxWxH)</label>
                        <input type="text" id="dimensions" name="dimensions"
                            class="form-input w-full px-3 py-2 border border-gray-300 rounded ring-1 focus:ring-sky-400 ring-transparent transition-all duration-300 outline-none mt-1"
                            value="<?php echo $mode === 'edit' ? htmlspecialchars($courier['dimensions']) : ''; ?>"
                            required>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="block text-gray-700 font-medium">Status</label>
                        <select id="status" name="status"
                            class="form-select w-full px-3 py-2 border border-gray-300 rounded ring-1 focus:ring-sky-400 ring-transparent transition-all duration-300 outline-none mt-1"
                            required>
                            <option value="">Select Status</option>
                            <option value="delivered" <?php echo $mode === 'edit' && $courier['status'] === 'delivered' ? 'selected' : ''; ?>>
                                Delivered
                            </option>
                            <option value="pending" <?php echo $mode === 'edit' && $courier['status'] === 'pending' ? 'selected' : ''; ?>>
                                Pending
                            </option>
                            <option value="returned" <?php echo $mode === 'edit' && $courier['status'] === 'returned' ? 'selected' : ''; ?>>
                                Returned
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <button id="submitButton"
                class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded mt-6 transition-all duration-300 inline-flex items-center">
                <?php echo $mode === 'create' ? 'Create Courier' : 'Update Courier'; ?>
                <span
                    class="hidden loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-6 w-6 ml-2"></span>
            </button>
        </form>
    </main>

    <script>
        document.getElementById('courierDetailsForm').addEventListener('submit', function () {
            var submitButton = document.getElementById('submitButton');
            var loader = submitButton.querySelector('.loader');
            submitButton.disabled = true;
            loader.classList.remove('hidden');
        });
    </script>
</body>

</html>