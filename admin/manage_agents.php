<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedIn();
$pageTitle = 'Manage Agents';

$database = new Database();
$db = $database->getConnection();

// Pagination setup
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Sorting setup
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'username';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'ASC';
$sortOrder = ($sortOrder === 'ASC') ? 'ASC' : 'DESC'; // Validate sortOrder

// Search setup
$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';

// Fetch total number of records
$totalQuery = "SELECT COUNT(DISTINCT a.id) as total 
               FROM agents a 
               JOIN agent_branches ab ON a.id = ab.agent_id
               WHERE a.username LIKE :search";
$totalStmt = $db->prepare($totalQuery);
$totalStmt->bindParam(':search', $search, PDO::PARAM_STR);
$totalStmt->execute();
$totalRecords = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $limit);

// Fetch agents with pagination, sorting, and searching
$query = "
    SELECT a.id, a.username, GROUP_CONCAT(b.branch_name SEPARATOR ', ') as branches
    FROM agents a
    JOIN agent_branches ab ON a.id = ab.agent_id
    JOIN branches b ON ab.branch_id = b.id
    WHERE a.username LIKE :search
    GROUP BY a.id, a.username
    ORDER BY $sortColumn $sortOrder
    LIMIT :start, :limit
";
$stmt = $db->prepare($query);
$stmt->bindParam(':search', $search, PDO::PARAM_STR);
$stmt->bindParam(':start', $start, PDO::PARAM_INT);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$agents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch branches for the branch selection dropdown
$branchQuery = "SELECT id, branch_name FROM branches";
$branchStmt = $db->prepare($branchQuery);
$branchStmt->execute();
$branches = $branchStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once '../includes/head.php'; ?>

<body class="bg-gray-100">
    <!-- Sidebar and Topbar -->
    <?php include "../includes/sidebar.php"; ?>
    <?php include "../includes/topbar.php"; ?>

    <main class="ml-64 p-6 mt-16">
        <h1 class="text-3xl font-bold mb-4">Manage Agents</h1>

        <div class="mb-4 flex gap-4">
            <a href="add_agent.php"
                class="inline-block bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 transition duration-300">
                <i class="fas fa-user-plus mr-2"></i>Add New Agent
            </a>
            <a href="add_branch.php"
                class="inline-block bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition duration-300">
                <i class="fas fa-building mr-2"></i>Add New Branch
            </a>
        </div>

        <div class="mb-4">
            <form method="get" action="" class="flex gap-4 items-center">
                <input type="text" name="search" placeholder="Search by username"
                    class="px-3 py-2 border border-gray-300 rounded"
                    value="<?php echo htmlspecialchars(isset($_GET['search']) ? $_GET['search'] : ''); ?>">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">Search</button>
            </form>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-x-auto mb-4">
            <table class="w-full text-left border-collapse overflow-hidden">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-2 px-4 border-b">
                            <a href="?sort=id&order=<?php echo ($sortColumn == 'id' && $sortOrder == 'ASC') ? 'DESC' : 'ASC'; ?>&search=<?php echo urlencode(isset($_GET['search']) ? $_GET['search'] : ''); ?>"
                                class="flex items-center">
                                ID
                                <?php if ($sortColumn == 'id'): ?>
                                    <i class="fas fa-sort-<?php echo $sortOrder == 'ASC' ? 'up' : 'down'; ?> ml-1"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th class="py-2 px-4 border-b">
                            <a href="?sort=username&order=<?php echo ($sortColumn == 'username' && $sortOrder == 'ASC') ? 'DESC' : 'ASC'; ?>&search=<?php echo urlencode(isset($_GET['search']) ? $_GET['search'] : ''); ?>"
                                class="flex items-center">
                                Username
                                <?php if ($sortColumn == 'username'): ?>
                                    <i class="fas fa-sort-<?php echo $sortOrder == 'ASC' ? 'up' : 'down'; ?> ml-1"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th class="py-2 px-4 border-b">Branches</th>
                        <th class="py-2 px-4 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody id="agent-table-body">
                    <?php foreach ($agents as $agent): ?>
                        <tr class="hover:bg-gray-100 agent-row"
                            data-agent-id="<?php echo htmlspecialchars($agent['id']); ?>">
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($agent['id']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($agent['username']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($agent['branches']); ?></td>
                            <td class="py-2 px-4 border-b">
                                <a href="edit_agent.php?id=<?php echo htmlspecialchars($agent['id']); ?>"
                                    class="text-blue-600 hover:underline"><i class="fas fa-edit"></i> Edit</a>
                                <button class="text-red-600 hover:underline ml-4 delete-button"
                                    data-agent-id="<?php echo htmlspecialchars($agent['id']); ?>"><i
                                        class="fas fa-trash-alt"></i> Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center mt-4">
            <div>
                <a href="?page=1&sort=<?php echo $sortColumn; ?>&order=<?php echo $sortOrder; ?>&search=<?php echo urlencode(isset($_GET['search']) ? $_GET['search'] : ''); ?>"
                    class="text-blue-600 hover:underline">&laquo; First</a>
                <a href="?page=<?php echo max(1, $page - 1); ?>&sort=<?php echo $sortColumn; ?>&order=<?php echo $sortOrder; ?>&search=<?php echo urlencode(isset($_GET['search']) ? $_GET['search'] : ''); ?>"
                    class="text-blue-600 hover:underline">Prev</a>
            </div>
            <div>
                <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
            </div>
            <div>
                <a href="?page=<?php echo min($totalPages, $page + 1); ?>&sort=<?php echo $sortColumn; ?>&order=<?php echo $sortOrder; ?>&search=<?php echo urlencode(isset($_GET['search']) ? $_GET['search'] : ''); ?>"
                    class="text-blue-600 hover:underline">Next</a>
                <a href="?page=<?php echo $totalPages; ?>&sort=<?php echo $sortColumn; ?>&order=<?php echo $sortOrder; ?>&search=<?php echo urlencode(isset($_GET['search']) ? $_GET['search'] : ''); ?>"
                    class="text-blue-600 hover:underline">Last &raquo;</a>
            </div>
        </div>
    </main>

    <!-- Confirmation Modal -->
    <div id="confirmation-modal"
        class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm mx-auto">
            <h2 class="text-xl font-semibold mb-4">Confirm Deletion</h2>
            <p class="mb-4">Are you sure you want to delete this agent?</p>
            <div class="flex justify-end gap-4">
                <button id="confirm-delete-button"
                    class="bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 transition duration-300">Delete</button>
                <button id="cancel-delete-button"
                    class="bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 transition duration-300">Cancel</button>
            </div>
        </div>
    </div>

    <?php include_once '../includes/script.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.delete-button');
            const confirmationModal = document.getElementById('confirmation-modal');
            const confirmDeleteButton = document.getElementById('confirm-delete-button');
            const cancelDeleteButton = document.getElementById('cancel-delete-button');
            let agentIdToDelete = null;

            // Function to show the confirmation modal with animation
            function showConfirmationModal() {
                gsap.fromTo(confirmationModal, { opacity: 0, scale: 0.7 }, { opacity: 1, scale: 1, duration: 0.5 });
                confirmationModal.classList.remove('hidden');
            }

            // Function to hide the confirmation modal with animation
            function hideConfirmationModal() {
                gsap.to(confirmationModal, { opacity: 0, scale: 0.7, duration: 0.5, onComplete: () => confirmationModal.classList.add('hidden') });
            }

            // Event listener for delete buttons
            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    agentIdToDelete = this.getAttribute('data-agent-id');
                    showConfirmationModal();
                });
            });

            // Confirm delete action
            confirmDeleteButton.addEventListener('click', function () {
                if (agentIdToDelete) {
                    window.location.href = `delete_agent.php?id=${agentIdToDelete}`;
                }
            });

            // Cancel delete action
            cancelDeleteButton.addEventListener('click', function () {
                hideConfirmationModal();
            });

            const rows = document.querySelectorAll('.agent-row');
            rows.forEach((row, index) => {
                gsap.from(row, {
                    opacity: 0,
                    y: 20,
                    duration: 0.5,
                    delay: index * 0.1
                });
            });
        });

        // Add hover animations
        document.querySelectorAll(".agent-row").forEach(row => {
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