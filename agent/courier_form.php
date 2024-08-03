<?php session_start();
include "../config/database.php";
include "../includes/functions.php";
redirectIfNotLoggedInAgent();
displaySessionMessages();
$pageTitle = "Create Courier";
$mode = "create"; ?>
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
                        <!-- Sender Details Fields -->
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
                            <div id="email-check-status" class="mt-2"></div>
                        </div>
                        <div class="mb-4">
                            <label for="sender_phone" class="block text-gray-700 font-medium">Phone</label>
                            <input type="text" id="sender_phone" name="sender_phone"
                                class="form-input w-full px-3 py-2 border border-gray-300 rounded ring-1 focus:ring-sky-400 ring-transparent transition-all duration-300 outline-none mt-1"
                                value="<?php echo $mode === 'edit' ? htmlspecialchars($courier['sender_phone']) : ''; ?>">
                        </div>
                        <div class="mb-4">
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
                        <div id="receiver-email-check-status" class="mt-2"></div>

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
            <div class="border border-gray-300 p-6 rounded-lg bg-white shadow-sm mt-4">
                <h2 class="text-xl font-semibold mb-2">Parcel Details</h2>
                <div class="mb-4">
                    <label for="weight" class="block text-gray-700 font-medium">Weight</label>
                    <input type="text" id="weight" name="weight"
                        class="form-input w-full px-3 py-2 border border-gray-300 rounded ring-1 focus:ring-sky-400 ring-transparent transition-all duration-300 outline-none mt-1"
                        value="<?php echo $mode === 'edit' ? htmlspecialchars($courier['weight']) : ''; ?>" required>
                </div>
                <div class="mb-4">
                    <label for="dimensions" class="block text-gray-700 font-medium">Dimensions</label>
                    <input type="text" id="dimensions" name="dimensions"
                        class="form-input w-full px-3 py-2 border border-gray-300 rounded ring-1 focus:ring-sky-400 ring-transparent transition-all duration-300 outline-none mt-1"
                        value="<?php echo $mode === 'edit' ? htmlspecialchars($courier['dimensions']) : ''; ?>"
                        required>
                </div>
                <div class="mb-4">
                    <label for="status" class="block text-gray-700 font-medium">Status</label>
                    <select id="status" name="status"
                        class="form-select w-full px-3 py-2 border border-gray-300 rounded ring-1 focus:ring-sky-400 ring-transparent transition-all duration-300 outline-none mt-1">
                        <option value="pending" <?php echo $mode === 'edit' && $courier['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="shipped" <?php echo $mode === 'edit' && $courier['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                        <option value="delivered" <?php echo $mode === 'edit' && $courier['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="delivery_date" class="block text-gray-700 font-medium">Delivery Date</label>
                    <input type="date" id="delivery_date" name="delivery_date"
                        class="form-input w-full px-3 py-2 border border-gray-300 rounded ring-1 focus:ring-sky-400 ring-transparent transition-all duration-300 outline-none mt-1"
                        value="<?php echo $mode === 'edit' ? htmlspecialchars($courier['delivery_date']) : ''; ?>"
                        required>
                </div>
            </div>
            <button type="submit"
                class="bg-sky-500 text-white font-semibold py-2 px-4 rounded-lg mt-4 transition-all duration-300 hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-400">
                <?php echo $mode === 'create' ? 'Create Courier' : 'Update Courier'; ?>
            </button>
        </form>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const senderEmailInput = document.getElementById('sender_email');
            const receiverEmailInput = document.getElementById('receiver_email');
            const senderStatusDiv = document.getElementById('email-check-status');
            const receiverStatusDiv = document.getElementById('receiver-email-check-status');

            function handleEmailCheck(input, statusDiv, prefix) {
                input.addEventListener('input', function () {
                    const email = input.value;

                    if (email.length > 0) {
                        statusDiv.innerHTML = '<div class="flex items-center space-x-2"><svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 1 1 16 0A8 8 0 0 1 4 12z"></path></svg><span>Checking...</span></div>';
                        fetch('../admin/check_customers.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ email: email }),
                        })
                            .then(response => response.json())
                            .then(data => {
                                statusDiv.innerHTML = '';

                                if (data.exists) {
                                    statusDiv.innerHTML = `<p class="text-red-500">User found: ${data.first_name} ${data.last_name}. <a href="#" class="text-blue-500 underline select-user" data-prefix="${prefix}" data-first-name="${data.first_name}" data-last-name="${data.last_name}" data-email="${email}" data-phone="${data.phone}" data-address="${data.address}">Select this user</a></p>`;
                                } else {
                                    statusDiv.innerHTML = '<p class="text-green-500">Email available</p>';
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                statusDiv.innerHTML = '<p class="text-red-500">An error occurred</p>';
                            });
                    } else {
                        statusDiv.innerHTML = '';
                    }
                });
            }

            handleEmailCheck(senderEmailInput, senderStatusDiv, 'sender');
            handleEmailCheck(receiverEmailInput, receiverStatusDiv, 'receiver');

            document.addEventListener('click', function (e) {
                if (e.target && e.target.matches('.select-user')) {
                    e.preventDefault();
                    const prefix = e.target.getAttribute('data-prefix');
                    const firstName = e.target.getAttribute('data-first-name');
                    const lastName = e.target.getAttribute('data-last-name');
                    const email = e.target.getAttribute('data-email');
                    const phone = e.target.getAttribute('data-phone');
                    const address = e.target.getAttribute('data-address');

                    if (prefix === 'sender') {
                        document.getElementById('sender_first_name').value = firstName;
                        document.getElementById('sender_last_name').value = lastName;
                        document.getElementById('sender_email').value = email;
                        document.getElementById('sender_phone').value = phone;
                        document.getElementById('sender_address').value = address;
                        senderStatusDiv.innerHTML = '<p class="text-green-500">User selected</p>';
                    } else if (prefix === 'receiver') {
                        document.getElementById('receiver_first_name').value = firstName;
                        document.getElementById('receiver_last_name').value = lastName;
                        document.getElementById('receiver_email').value = email;
                        document.getElementById('receiver_phone').value = phone;
                        document.getElementById('receiver_address').value = address;
                        receiverStatusDiv.innerHTML = '<p class="text-green-500">User selected</p>';
                    }
                }
            });
        });
    </script>
    <?php require "../includes/script.php"; ?>
</body>

</html>