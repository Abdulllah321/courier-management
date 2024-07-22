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
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'ASC';
$sortOrder = ($sortOrder === 'ASC') ? 'ASC' : 'DESC'; // Validate sortOrder

// Search setup
$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';

// Debug: Display the fetched parameters
echo "Page: $page<br>";
echo "Sort Column: $sortColumn<br>";
echo "Sort Order: $sortOrder<br>";
echo "Search: " . htmlspecialchars($_GET['search'] ?? '') . "<br>";

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

// Debug: Display fetched agents data
echo "<pre>";
print_r($agents);
echo "</pre>";

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
            <table class="w-full text-left border-collapse">
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
                <tbody>
                    <?php foreach ($agents as $agent): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($agent['id']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($agent['username']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($agent['branches']); ?></td>
                            <td class="py-2 px-4 border-b">
                                <a href="edit_agent.php?id=<?php echo htmlspecialchars($agent['id']); ?>"
                                    class="text-blue-600 hover:underline"><i class="fas fa-edit"></i> Edit</a>
                                <a href="delete_agent.php?id=<?php echo htmlspecialchars($agent['id']); ?>"
                                    class="text-red-600 hover:underline ml-4"><i class="fas fa-trash-alt"></i> Delete</a>
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
    <?php include_once '../includes/script.php'; ?>
</body>

</html>