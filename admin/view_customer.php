<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedIn();

$pageTitle = "Customer Details";

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

// Fetch customer parcels
$query = "SELECT * FROM parcels WHERE sender_id = :customer_id OR receiver_id = :customer_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':customer_id', $customerId);
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
        <h1 class="text-3xl font-bold mb-4">Customer Details</h1>

        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold mb-4">Customer Information</h2>
            <p><strong>First Name:</strong> <?php echo htmlspecialchars($customer['first_name']); ?></p>
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($customer['last_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer['phone']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($customer['address']); ?></p>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-4">Parcels</h2>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-3 px-4 border-b">Parcel ID</th>
                        <th class="py-3 px-4 border-b">Role</th>
                        <th class="py-3 px-4 border-b">Weight</th>
                        <th class="py-3 px-4 border-b">Dimensions</th>
                        <th class="py-3 px-4 border-b">Status</th>
                        <th class="py-3 px-4 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($parcels as $parcel): ?>
                        <tr class="hover:bg-gray-100 parcel-row">
                            <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($parcel['parcel_id']); ?></td>
                            <td
                                class="py-3 px-4 border-b <?php echo ($parcel['sender_id'] == $customerId) ? 'bg-blue-100' : 'bg-yellow-100'; ?>">
                                <?php echo ($parcel['sender_id'] == $customerId) ? 'Sender' : 'Receiver'; ?>
                            </td>
                            <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($parcel['weight']); ?></td>
                            <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($parcel['dimensions']); ?></td>
                            <td class="py-3 px-4 border-b">
                                <span class="status-text px-1 py-px rounded-md <?php echo $parcel['status']; ?>">
                                    <?php echo htmlspecialchars($parcel['status']); ?>
                                </span>
                                <select class="status-select hidden" data-id="<?php echo $parcel['parcel_id']; ?>">
                                    <option value="Pending" <?php echo $parcel['status'] == 'Pending' ? 'selected' : ''; ?>>
                                        Pending</option>
                                    <option value="Delivered" <?php echo $parcel['status'] == 'Delivered' ? 'selected' : ''; ?>>
                                        Delivered</option>
                                    <option value="returned" <?php echo $parcel['status'] == 'returned' ? 'selected' : ''; ?>>
                                        returned</option>
                                </select>
                            </td>
                            <td class="py-3 px-4 border-b">
                                <a href="view_parcel.php?id=<?php echo $parcel['parcel_id']; ?>"
                                    class="text-blue-600 hover:underline">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Notification -->
    <div id="notification" class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg hidden">
        <p id="notificationMessage"></p>
    </div>

    <?php include "../includes/script.php"; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusTexts = document.querySelectorAll('.status-text');
            const statusSelects = document.querySelectorAll('.status-select');
            const notification = document.getElementById('notification');
            const notificationMessage = document.getElementById('notificationMessage');

            // Apply status color classes
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
        });

        function getStatusColorClass(status) {
            switch (status) {
                case 'Pending':
                    return ['bg-yellow-200', 'text-yellow-600', 'px-2', 'py-px', 'rounded-md'];
                case 'In Transit':
                    return ['bg-blue-200', 'text-blue-600', 'px-2', 'py-px', 'rounded-md'];
                case 'Delivered':
                    return ['bg-green-200', 'text-green-600', 'px-2', 'py-px', 'rounded-md'];
                case 'returned':
                    return ['bg-red-200', 'text-red-600', 'px-2', 'py-px', 'rounded-md'];
                default:
                    return [];
            }
        }

    </script>

    <script>
        // GSAP animations for table rows
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