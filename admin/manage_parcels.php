<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedIn();

$pageTitle = "Manage Parcels";

$database = new Database();
$db = $database->getConnection();

// Fetch all parcels
$query = "SELECT * FROM parcels";
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
                            <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($parcel['weight']); ?></td>
                            <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($parcel['dimensions']); ?></td>
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
                                <a href="courier_form.php?id=<?php echo $parcel['parcel_id']; ?>"
                                    class="text-yellow-600 hover:underline ml-4">Edit</a>
                                <a href="delete_parcel.php?id=<?php echo $parcel['parcel_id']; ?>"
                                    class="text-red-600 hover:underline ml-4"
                                    onclick="return confirm('Are you sure you want to delete this parcel?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include "../includes/script.php"; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusTexts = document.querySelectorAll('.status-text');
            const statusSelects = document.querySelectorAll('.status-select');
            const notification = document.getElementById('notification');
            const notificationMessage = document.getElementById('notificationMessage');

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
                                text.className = 'status-text ' + getStatusColorClass(newStatus);
                                this.classList.add('hidden');
                                text.classList.remove('hidden');
                                showNotification('Status updated successfully');
                            } else {
                                showNotification(data.message, 'error');
                            }
                        });
                });
            });
        });

        function getStatusColorClass(status) {
            switch (status) {
                case 'Pending':
                    return 'bg-yellow-200 text-yellow-600';
                case 'In Transit':
                    return 'bg-blue-200 text-blue-600';
                case 'Delivered':
                    return 'bg-green-200 text-green-600';
                case 'Cancelled':
                    return 'bg-red-200 text-red-600';
                default:
                    return '';
            }
        }
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