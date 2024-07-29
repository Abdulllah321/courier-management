<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedIn();

$pageTitle = "Parcel Details";

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Parcel ID is required.";
    header("Location: manage_parcels.php");
    exit();
}

$parcelId = $_GET['id'];
$database = new Database();
$db = $database->getConnection();

// Fetch parcel details
$query = "SELECT * FROM parcels WHERE parcel_id = :parcel_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':parcel_id', $parcelId);
$stmt->execute();
$parcel = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$parcel) {
    $_SESSION['error'] = "Parcel not found.";
    header("Location: manage_parcels.php");
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
        <h1 class="text-3xl font-bold mb-4">Parcel Details</h1>

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

        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <div class="mb-4">
                <h2 class="text-2xl font-semibold mb-2">Parcel Information</h2>
                <p><strong>Parcel ID:</strong> <?php echo htmlspecialchars($parcel['parcel_id']); ?></p>
                <p><strong>Sender ID:</strong> <?php echo htmlspecialchars($parcel['sender_id']); ?></p>
                <p><strong>Receiver ID:</strong> <?php echo htmlspecialchars($parcel['receiver_id']); ?></p>
                <p><strong>Weight:</strong> <?php echo htmlspecialchars($parcel['weight']); ?></p>
                <p><strong>Dimensions:</strong> <?php echo htmlspecialchars($parcel['dimensions']); ?></p>
                <p><strong>Status:</strong> <span
                        class="status-text"><?php echo htmlspecialchars($parcel['status']); ?></span></p>
            </div>
        </div>
    </main>

    <?php include "../includes/script.php"; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusText = document.querySelector('.status-text');

            if (statusText) {
                statusText.addEventListener('dblclick', function () {
                    this.classList.add('hidden');
                    const select = document.createElement('select');
                    select.className = 'status-select';
                    select.innerHTML = `
                        <option value="Pending" ${statusText.textContent === 'Pending' ? 'selected' : ''}>Pending</option>
                        <option value="In Transit" ${statusText.textContent === 'In Transit' ? 'selected' : ''}>In Transit</option>
                        <option value="Delivered" ${statusText.textContent === 'Delivered' ? 'selected' : ''}>Delivered</option>
                        <option value="returned" ${statusText.textContent === 'returned' ? 'selected' : ''}>returned</option>
                    `;
                    this.parentNode.appendChild(select);
                    select.focus();

                    select.addEventListener('blur', function () {
                        this.classList.add('hidden');
                        statusText.classList.remove('hidden');
                    });

                    select.addEventListener('change', function () {
                        const parcelId = '<?php echo $parcel['parcel_id']; ?>';
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
                                    statusText.textContent = newStatus;
                                    statusText.className = 'status-text ' + getStatusColorClass(newStatus);
                                    this.classList.add('hidden');
                                    statusText.classList.remove('hidden');
                                    showNotification('Status updated successfully');
                                } else {
                                    showNotification(data.message, 'error');
                                }
                            });
                    });
                });
            }

            function getStatusColorClass(status) {
                switch (status) {
                    case 'Pending':
                        return 'bg-yellow-200 text-yellow-600';
                    case 'In Transit':
                        return 'bg-blue-200 text-blue-600';
                    case 'Delivered':
                        return 'bg-green-200 text-green-600';
                    case 'returned':
                        return 'bg-red-200 text-red-600';
                    default:
                        return '';
                }
            }

            const showNotification = (message, type = 'success') => {
                notification.classList.remove('hidden');
                notification.classList.add(`bg-${type === 'success' ? 'green' : 'red'}-500`);
                notificationMessage.textContent = message;
                setTimeout(() => {
                    notification.classList.add('hidden');
                }, 3000); // Hide after 3 seconds
            };
        });
    </script>

    <script>
        gsap.from(".parcel-details", {
            opacity: 0,
            y: 20,
            duration: 0.8,
            ease: "power4.out",
        });
    </script>
</body>

</html>