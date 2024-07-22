<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedIn();
$pageTitle = 'Manage Branches';

$database = new Database();
$db = $database->getConnection();

// Pagination setup
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Filter by status
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';

// Fetch total number of records
$totalQuery = "SELECT COUNT(*) as total FROM branches WHERE deleted = false";
$totalStmt = $db->prepare($totalQuery);
$totalStmt->execute();
$totalRecords = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $limit);

// Fetch branches with pagination
$query = "SELECT * FROM branches WHERE deleted = false LIMIT :start, :limit";
$stmt = $db->prepare($query);
$stmt->bindParam(':start', $start, PDO::PARAM_INT);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$branches = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once '../includes/head.php'; ?>

<body class="bg-gray-100">
    <!-- Sidebar and Topbar -->
    <?php include "../includes/sidebar.php"; ?>
    <?php include "../includes/topbar.php"; ?>

    <main class="ml-64 p-6 mt-16">
        <h1 class="text-3xl font-bold mb-4">Manage Branches</h1>

        <div class="mb-4 flex gap-4">
            <a href="add_branch.php" class="inline-block bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition duration-300">
                <i class="fas fa-building mr-2"></i>Add New Branch
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
            <table class="w-full text-left border-collapse ">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-3 px-4 border-b">Branch Name</th>
                        <th class="py-3 px-4 border-b">Location</th>
                        <th class="py-3 px-4 border-b">Status</th>
                        <th class="py-3 px-4 border-b">Manager</th>
                        <th class="py-3 px-4 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody id="branchesTableBody">
                    <?php foreach ($branches as $branch) : ?>
                        <tr class="hover:bg-gray-100 branch-row" data-status="<?php echo htmlspecialchars($branch['status']); ?>">
                            <td class="py-3 px-4 border-b whitespace-nowrap"><?php echo htmlspecialchars($branch['branch_name']); ?></td>
                            <td class="py-3 px-4 border-b truncate whitespace-nowrap" title="<?php echo htmlspecialchars($branch['address'] . ', ' . $branch['city'] . ', ' . $branch['state_province'] . ', ' . $branch['zip_postal_code'] . ', ' . $branch['country']); ?>">
                                <?php echo htmlspecialchars($branch['address'] . ', ' . $branch['city'] . ', ' . $branch['state_province'] . ', ' . $branch['zip_postal_code'] . ', ' . $branch['country']); ?>
                            </td>
                            <td class="py-3 px-4 border-b status-cell" data-id="<?php echo $branch['id']; ?>" data-status="<?php echo $branch['status']; ?>">
                                <span class="status <?php echo htmlspecialchars($branch['status']); ?>">
                                    <?php echo htmlspecialchars(ucfirst($branch['status'])); ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 border-b whitespace-nowrap"><?php echo htmlspecialchars($branch['branch_manager']); ?></td>
                            <td class="py-3 px-4 border-b flex gap-2 flex-nowrap items-center">
                                <a href="view_branch.php?id=<?php echo $branch['id']; ?>" class="flex gap-1 flex-nowrap text-blue-600 hover:underline"><i class="fas fa-eye"></i> View</a>
                                <a href="edit_branch.php?id=<?php echo $branch['id']; ?>" class="flex gap-1 flex-nowrap text-blue-600 hover:underline ml-4"><i class="fas fa-edit"></i> Edit</a>
                                <a href="#" data-id="<?php echo $branch['id']; ?>" class="flex gap-1 flex-nowrap text-red-600 hover:underline ml-4 delete-branch"><i class="fas fa-trash-alt"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center mt-4">
            <div>
                <a href="?page=1" class="text-blue-600 hover:underline">&laquo; First</a>
                <a href="?page=<?php echo max(1, $page - 1); ?>" class="text-blue-600 hover:underline">&lt; Prev</a>
            </div>
            <div>
                <span>Showing <?php echo $start + 1; ?> to <?php echo min($start + $limit, $totalRecords); ?> of <?php echo $totalRecords; ?> items</span>
            </div>
            <div class="flex gap-4">
                <a href="?page=<?php echo min($totalPages, $page + 1); ?>" class="text-blue-600 hover:underline">Next &gt;</a>
                <a href="?page=<?php echo $totalPages; ?>" class="text-blue-600 hover:underline">Last &raquo;</a>
            </div>
        </div>

    </main>

    <!-- Custom Confirmation Modal -->
    <div id="confirmationModal" class="fixed inset-0 items-center justify-center bg-gray-600 bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
            <h2 class="text-xl font-semibold mb-4">Confirm Deletion</h2>
            <p id="confirmationMessage" class="mb-4">Are you sure you want to delete this branch?</p>
            <div class="flex justify-end gap-4">
                <button id="confirmButton" class="bg-blue-500 text-white px-4 py-2 rounded">Yes</button>
                <button id="cancelButton" class="bg-red-500 text-white px-4 py-2 rounded">No</button>
            </div>
        </div>
    </div>

    <!-- Notification -->
    <div id="notification" class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg hidden">
        <p id="notificationMessage"></p>
    </div>


    <?php include "../includes/script.php"; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-branch');
            const modal = document.getElementById('confirmationModal');
            const confirmButton = document.getElementById('confirmButton');
            const cancelButton = document.getElementById('cancelButton');
            const notification = document.getElementById('notification');
            const notificationMessage = document.getElementById('notificationMessage');
            let currentBranchId;

            const showModal = (branchId) => {
                currentBranchId = branchId;
                modal.classList.remove('hidden');
            };

            const hideModal = () => {
                modal.classList.add('hidden');
            };

            const showNotification = (message, type = 'success') => {
                notification.classList.remove('hidden');
                notification.classList.add(`bg-${type === 'success' ? 'green' : 'red'}-500`);
                notificationMessage.textContent = message;
                setTimeout(() => {
                    notification.classList.add('hidden');
                }, 3000); // Hide after 3 seconds
            };

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const branchId = this.getAttribute('data-id');
                    showModal(branchId);
                });
            });

            confirmButton.addEventListener('click', function() {
                fetch('delete_branch.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${currentBranchId}`,
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification('Branch successfully deleted');
                            location.reload();
                        } else {
                            showNotification('Failed to delete the branch', 'error');
                        }
                        hideModal();
                    });
            });

            cancelButton.addEventListener('click', hideModal);
        });
    </script>

    <script>
        // GSAP animations for table rows
        gsap.from(".branch-row", {
            opacity: 0,
            y: 20,
            duration: 0.5,
            stagger: 0.1
        });

        // Add hover animations
        document.querySelectorAll(".branch-row").forEach(row => {
            row.addEventListener('mouseenter', () => {
                gsap.to(row, {
                    scale: 1.01,
                    duration: 0.3
                });
            });
            row.addEventListener('mouseleave', () => {
                gsap.to(row, {
                    scale: 1,
                    duration: 0.3
                });
            });
        });
    </script>
</body>

</html>