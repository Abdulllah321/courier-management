<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedInAgent();

$pageTitle = "Manage Parcels";

$database = new Database();
$db = $database->getConnection();

$agentId = $_SESSION['agent_id'];

$query = "SELECT * FROM parcels WHERE deleted = false AND agent_id = :agent_id";
$stmt = $db->prepare($query);

$stmt->bindParam(':agent_id', $agentId, PDO::PARAM_STR);

if (!$stmt->execute()) {
    $error = $stmt->errorInfo();
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $error[2]]);
    exit;
}

// Fetch all parcels
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

        <div id="notification" class="text-white p-4 rounded-md mb-4 hidden">
            <p id="notificationMessage"></p>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-3 px-4 border-b">Parcel ID</th>
                        <th class="py-3 px-4 border-b">Sender ID</th>
                        <th class="py-3 px-4 border-b">Receiver ID</th>
                        <th class="py-3 px-4 border-b">Weight</th>
                        <th class="py-3 px-4 border-b">Dimensions</th>
                        <th class="py-3 px-4 border-b">Delivery Date</th>
                        <th class="py-3 px-4 border-b">Status</th>
                        <th class="py-3 px-4 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($parcels as $parcel): ?>
                        <tr class="hover:bg-gray-100 parcel-row">
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
                                <?php if ($parcel['status'] !== 'Delivered'): ?>
                                    <input type="date" class="border rounded p-1 delivery-date"
                                        value="<?php echo htmlspecialchars($parcel['delivery_date']); ?>"
                                        data-id="<?php echo $parcel['parcel_id']; ?>" />
                                <?php else: ?>
                                    <?php echo htmlspecialchars($parcel['delivery_date']); ?>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 border-b">
                                <span class="status-text"><?php echo htmlspecialchars($parcel['status']); ?></span>
                                <select class="status-select hidden" data-id="<?php echo $parcel['parcel_id']; ?>">
                                    <option value="Pending" <?php echo $parcel['status'] == 'Pending' ? 'selected' : ''; ?>>
                                        Pending
                                    </option>
                                    <option value="In Transit" <?php echo $parcel['status'] == 'In Transit' ? 'selected' : ''; ?>>
                                        In Transit
                                    </option>
                                    <option value="Delivered" <?php echo $parcel['status'] == 'Delivered' ? 'selected' : ''; ?>>
                                        Delivered
                                    </option>
                                    <option value="returned" <?php echo $parcel['status'] == 'returned' ? 'selected' : ''; ?>>
                                        returned
                                    </option>
                                </select>
                            </td>
                            <td class="py-3 px-4 border-b">
                                <a href="view_parcel.php?id=<?php echo $parcel['parcel_id']; ?>"
                                    class="text-blue-600 hover:underline">View</a>
                                <a href="#" class="text-red-600 hover:underline ml-4 delete-link"
                                    data-id="<?php echo $parcel['parcel_id']; ?>">Delete</a>
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
            const deliveryDates = document.querySelectorAll('.delivery-date');
            const weights = document.querySelectorAll('.weight');
            const dimensions = document.querySelectorAll('.dimensions');
            const notification = document.getElementById('notification');
            const notificationMessage = document.getElementById('notificationMessage');

            const showNotification = (message, type = 'success') => {
                notification?.classList.remove('hidden');
                console.log(notificationMessage)
                notification?.classList.add(`bg-${type === 'success' ? 'green' : 'red'}-500`);
                notificationMessage.textContent = message;
                setTimeout(() => {
                    notification?.classList.add('opacity-0');
                }, 3000);
                setTimeout(() => {
                    notification?.classList.add('hidden');
                }, 3500);
            };

            const updateDetails = (id, weight, dimensions, deliveryDate) => {
                fetch('update_parcel_details.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}&weight=${weight}&dimensions=${dimensions}&delivery_date=${deliveryDate}`,
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data.message)
                        if (data.success) {
                            showNotification(data.message);
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
                    const deliveryDateField = document.querySelector(`.delivery-date[data-id="${id}"]`).value;
                    updateDetails(id, value, dimensionsField, deliveryDateField);
                });
            });

            dimensions.forEach(dim => {
                dim.addEventListener('blur', function () {
                    const id = this.getAttribute('data-id');
                    const value = this.value;
                    const weightField = document.querySelector(`.weight[data-id="${id}"]`).value;
                    const deliveryDateField = document.querySelector(`.delivery-date[data-id="${id}"]`).value;
                    updateDetails(id, weightField, value, deliveryDateField);
                });
            });

            deliveryDates.forEach(date => {
                date.addEventListener('blur', function () {
                    const id = this.getAttribute('data-id');
                    const value = this.value;
                    const weightField = document.querySelector(`.weight[data-id="${id}"]`).value;
                    const dimensionsField = document.querySelector(`.dimensions[data-id="${id}"]`).value;
                    updateDetails(id, weightField, dimensionsField, value);
                });
            });
        });

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