<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedIn();

$pageTitle = "Manage Parcels";

$database = new Database();
$db = $database->getConnection();

// Fetch all parcels
$query = "SELECT * FROM parcels where deleted=false";
$stmt = $db->prepare($query);
$stmt->execute();
$parcels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once '../includes/head.php'; ?>

<body class="bg-gray-100">
    <!-- Sidebar and Topbar -->
    <?php include "../includes/sidebar.php"; ?>
    <?php include "../includes/topbar.php"; ?>

    <main class="ml-64 p-6 mt-16">
        <h1 class="text-3xl font-bold mb-4">Manage Parcels</h1>

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

        <div class="bg-white shadow-md rounded-lg p-6">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-3 px-4 border-b">Parcel ID</th>
                        <th class="py-3 px-4 border-b">Sender ID</th>
                        <th class="py-3 px-4 border-b">Receiver ID</th>
                        <th class="py-3 px-4 border-b">Weight</th>
                        <th class="py-3 px-4 border-b">Dimensions</th>
                        <th class="py-3 px-4 border-b">Status</th>
                        <th class="py-3 px-4 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($parcels as $parcel): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($parcel['parcel_id']); ?></td>
                            <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($parcel['sender_id']); ?></td>
                            <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($parcel['receiver_id']); ?></td>
                            <td class="py-3 px-4 border-b">
                                <?php if ($parcel['status'] !== 'Delivered'): ?>
                                    <input type="text" class="border rounded p-1 weight"
                                        value="<?php echo htmlspecialchars($parcel['weight']); ?>"
                                        data-id="<?php echo $parcel['parcel_id']; ?>" />
                                <?php else: ?>
                                    <?php echo htmlspecialchars($parcel['weight']); ?>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 border-b">
                                <?php if ($parcel['status'] !== 'Delivered'): ?>
                                    <input type="text" class="border rounded p-1 dimensions"
                                        value="<?php echo htmlspecialchars($parcel['dimensions']); ?>"
                                        data-id="<?php echo $parcel['parcel_id']; ?>" />
                                <?php else: ?>
                                    <?php echo htmlspecialchars($parcel['dimensions']); ?>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 border-b">
                                <span class="status-text"><?php echo htmlspecialchars($parcel['status']); ?></span>
                                <select class="status-select hidden" data-id="<?php echo $parcel['parcel_id']; ?>">
                                    <option value="Pending" <?php echo $parcel['status'] == 'Pending' ? 'selected' : ''; ?>>
                                        Pending</option>
                                    <option value="In Transit" <?php echo $parcel['status'] == 'In Transit' ? 'selected' : ''; ?>>In Transit</option>
                                    <option value="Delivered" <?php echo $parcel['status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="Cancelled" <?php echo $parcel['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </td>
                            <td class="py-3 px-4 border-b">
                                <a href="view_parcel.php?id=<?php echo $parcel['parcel_id']; ?>"
                                    class="text-blue-600 hover:underline">View</a>
                                <a href="delete_parcel.php?id=<?php echo $parcel['parcel_id']; ?>"
                                    class="text-red-600 hover:underline ml-4 delete-link">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Custom Deletion Popup -->
        <div id="deletePopup" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-bold mb-4">Confirm Deletion</h2>
                <p class="mb-4">Are you sure you want to delete this parcel? You can also choose to delete the
                    associated customer.</p>
                <div class="mb-4">
                    <input type="checkbox" id="deleteCustomer" />
                    <label for="deleteCustomer" class="ml-2">Delete associated customer</label>
                </div>
                <div class="flex justify-end">
                    <button id="confirmDelete" class="bg-red-500 text-white px-4 py-2 rounded-md">Delete</button>
                    <button id="cancelDelete"
                        class="ml-2 bg-gray-300 text-gray-700 px-4 py-2 rounded-md">Cancel</button>
                </div>
            </div>
        </div>

    </main>

    <?php include "../includes/script.php"; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusTexts = document.querySelectorAll('.status-text');
            const statusSelects = document.querySelectorAll('.status-select');
            const weights = document.querySelectorAll('.weight');
            const dimensions = document.querySelectorAll('.dimensions');
            const notification = document.getElementById('notification');
            const notificationMessage = document.getElementById('notificationMessage');

            statusTexts.forEach(text => {
                const status = text.textContent.trim();
                text.classList.add(...getStatusColorClass(status));
            });

            const showNotification = (message, type = 'success') => {
                notification.classList.remove('hidden');
                notification.classList.add(`bg-${type === 'success' ? 'green' : 'red'}-500`);
                notificationMessage.textContent = message;
                setTimeout(() => {
                    notification.classList.add('hidden');
                }, 3000); // Hide after 3 seconds
            };

            statusTexts.forEach(text => {
                text.addEventListener('dblclick', function () {
                    this.classList.add('hidden');
                    const select = this.nextElementSibling;
                    select.classList.remove('hidden');
                    select.focus();
                });
            });

            statusSelects.forEach(select => {
                select.addEventListener('blur', function () {
                    this.classList.add('hidden');
                    const text = this.previousElementSibling;
                    text.classList.remove('hidden');
                });

                select.addEventListener('change', function () {
                    const parcelId = this.getAttribute('data-id');
                    const newStatus = this.value;

                    fetch('update_parcel_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${parcelId}&status=${newStatus}`,
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const text = this.previousElementSibling;
                                text.textContent = newStatus;
                                text.className = 'status-text ' + getStatusColorClass(newStatus).join(' ');
                                this.classList.add('hidden');
                                text.classList.remove('hidden');
                                showNotification('Status updated successfully');
                            } else {
                                showNotification(data.message, 'error');
                            }
                        });
                });
            });

            const updateDetails = (id, weight, dimensions) => {
                fetch('update_parcel_details.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}&weight=${weight}&dimensions=${dimensions}`,
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification('Details updated successfully');
                        } else {
                            showNotification(data.message, 'error');
                        }
                    });
            };

            weights.forEach(weight => {
                weight.addEventListener('blur', function () {
                    const id = this.getAttribute('data-id');
                    const value = this.value;
                    const dimensionsField = document.querySelector(`.dimensions[data-id="${id}"]`).value;
                    updateDetails(id, value, dimensionsField);
                });
            });

            dimensions.forEach(dim => {
                dim.addEventListener('blur', function () {
                    const id = this.getAttribute('data-id');
                    const value = this.value;
                    const weightField = document.querySelector(`.weight[data-id="${id}"]`).value;
                    updateDetails(id, weightField, value);
                });
            });
        });

        function getStatusColorClass(status) {
            switch (status) {
                case 'Pending':
                    return ['bg-yellow-200', 'text-yellow-600', 'px-2', 'py-px', 'rounded-md'];
                case 'In Transit':
                    return ['bg-blue-200', 'text-blue-600', 'px-2', 'py-px', 'rounded-md'];
                case 'Delivered':
                    return ['bg-green-200', 'text-green-600', 'px-2', 'py-px', 'rounded-md'];
                case 'Cancelled':
                    return ['bg-red-200', 'text-red-600', 'px-2', 'py-px', 'rounded-md'];
                default:
                    return [];
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deletePopup = document.getElementById('deletePopup');
            const confirmDeleteButton = document.getElementById('confirmDelete');
            const cancelDeleteButton = document.getElementById('cancelDelete');
            const deleteCustomerCheckbox = document.getElementById('deleteCustomer');

            let deleteId = null; // Store the ID of the parcel to delete

            // Show delete popup
            document.querySelectorAll('.delete-link').forEach(link => {
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    deleteId = this.getAttribute('data-id');
                    deletePopup.classList.remove('hidden');
                });
            });

            // Confirm deletion
            confirmDeleteButton.addEventListener('click', function () {
                const deleteCustomer = deleteCustomerCheckbox.checked;

                fetch('delete_parcel.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${deleteId}&deleteCustomer=${deleteCustomer}`,
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload(); // Reload the page after successful deletion
                        } else {
                            alert(data.message); // Show error message
                        }
                    });

                deletePopup.classList.add('hidden');
            });

            // Cancel deletion
            cancelDeleteButton.addEventListener('click', function () {
                deletePopup.classList.add('hidden');
            });
        });
    </script>
    <script>
        gsap.from(".parcel-row", {
            opacity: 0,
            y: 20,
            duration: 0.8,
            stagger: 0.1,
            ease: "power4.out",
        });
    </script>
</body>

</html>