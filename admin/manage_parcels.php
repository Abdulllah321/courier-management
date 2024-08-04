<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedIn();

$pageTitle = "Manage Parcels";

$database = new Database();
$db = $database->getConnection();

$agentId = $_SESSION['agent_id'];

// Default sorting
$validSortColumns = ['parcel_id', 'sender_id', 'receiver_id', 'weight', 'dimensions', 'delivery_date', 'status'];
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'parcel_id';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'ASC';
$sortColumn = in_array($sortColumn, $validSortColumns) ? $sortColumn : 'parcel_id';
$sortOrder = $sortOrder === 'DESC' ? 'DESC' : 'ASC';

// Search and filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

// Pagination
$perPage = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Query with search and filter
$query = "SELECT * FROM parcels WHERE deleted = false";
if ($search) {
    $query .= " AND (parcel_id LIKE :search OR sender_id LIKE :search OR receiver_id LIKE :search)";
}
if ($statusFilter) {
    $query .= " AND status = :status";
}
$query .= " ORDER BY $sortColumn $sortOrder LIMIT :offset, :perPage";

$stmt = $db->prepare($query);

if ($search) {
    $searchParam = "%$search%";
    $stmt->bindValue(':search', $searchParam, PDO::PARAM_STR);
}
if ($statusFilter) {
    $stmt->bindValue(':status', $statusFilter, PDO::PARAM_STR);
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);

if (!$stmt->execute()) {
    $error = $stmt->errorInfo();
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $error[2]]);
    exit;
}

// Fetch all parcels
$parcels = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total number of parcels for pagination
$countQuery = "SELECT COUNT(*) FROM parcels WHERE deleted = false";
if ($search) {
    $countQuery .= " AND (parcel_id LIKE :search OR sender_id LIKE :search OR receiver_id LIKE :search)";
}
if ($statusFilter) {
    $countQuery .= " AND status = :status";
}
$countStmt = $db->prepare($countQuery);

if ($search) {
    $countStmt->bindValue(':search', $searchParam, PDO::PARAM_STR);
}
if ($statusFilter) {
    $countStmt->bindValue(':status', $statusFilter, PDO::PARAM_STR);
}
$countStmt->execute();
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $perPage);
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once '../includes/head.php'; ?>

<body class="bg-gray-100 " style="width: 100%; overflow-x:hidden;">
    <!-- Sidebar and Topbar -->
    <?php include "../includes/sidebar.php"; ?>
    <?php include "../includes/topbar.php"; ?>

    <main class="ml-64 p-6 mt-16">
        <h1 class="text-3xl font-bold mb-4">Manage Parcels</h1>

        <div id="notification" class="text-white p-4 rounded-md mb-4 hidden">
            <p id="notificationMessage"></p>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6 w-full">
            <!-- Search and Filter -->
            <div class="mb-4 flex space-x-4">
                <input type="text" id="searchInput" placeholder="Search..." class="border rounded p-2 w-1/2"
                    value="<?php echo htmlspecialchars($search); ?>">
                <select id="statusFilter" class="border rounded p-2">
                    <option value="">All Statuses</option>
                    <option value="Pending" <?php echo $statusFilter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="In Transit" <?php echo $statusFilter == 'In Transit' ? 'selected' : ''; ?>>In Transit
                    </option>
                    <option value="Delivered" <?php echo $statusFilter == 'Delivered' ? 'selected' : ''; ?>>Delivered
                    </option>
                    <option value="Returned" <?php echo $statusFilter == 'Returned' ? 'selected' : ''; ?>>Returned
                    </option>
                </select>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="min-w-max w-[90%] table-auto table-row-group overflow-hidden p-1 mx-auto">
                    <thead>
                        <tr class="bg-gray-800 text-white">
                            <th class="py-3 px-4 border-b cursor-pointer <?php echo $sortColumn == 'parcel_id' ? 'bg-gray-700 text-white whitespace-normal' : ''; ?>"
                                data-sort="parcel_id" style="width: 15%;">
                                Parcel ID
                                <?php if ($sortColumn == 'parcel_id'): ?>
                                    <span
                                        class="sort-arrow"><?php echo $sortOrder == 'ASC' ? '&#9650;' : '&#9660;'; ?></span>
                                <?php endif; ?>
                            </th>
                            <th class="py-3 px-4 border-b cursor-pointer <?php echo $sortColumn == 'sender_id' ? 'bg-gray-700 text-white' : ''; ?>"
                                data-sort="sender_id">
                                Sender ID
                                <?php if ($sortColumn == 'sender_id'): ?>
                                    <span
                                        class="sort-arrow"><?php echo $sortOrder == 'ASC' ? '&#9650;' : '&#9660;'; ?></span>
                                <?php endif; ?>
                            </th>
                            <th class="py-3 px-4 border-b cursor-pointer <?php echo $sortColumn == 'receiver_id' ? 'bg-gray-700 text-white' : ''; ?>"
                                data-sort="receiver_id">
                                Receiver ID
                                <?php if ($sortColumn == 'receiver_id'): ?>
                                    <span
                                        class="sort-arrow"><?php echo $sortOrder == 'ASC' ? '&#9650;' : '&#9660;'; ?></span>
                                <?php endif; ?>
                            </th>
                            <th class="py-3 px-4 border-b cursor-pointer <?php echo $sortColumn == 'weight' ? 'bg-gray-700 text-white' : ''; ?>"
                                data-sort="weight">
                                Weight
                                <?php if ($sortColumn == 'weight'): ?>
                                    <span
                                        class="sort-arrow"><?php echo $sortOrder == 'ASC' ? '&#9650;' : '&#9660;'; ?></span>
                                <?php endif; ?>
                            </th>
                            <th class="py-3 px-4 border-b cursor-pointer <?php echo $sortColumn == 'dimensions' ? 'bg-gray-700 text-white' : ''; ?>"
                                data-sort="dimensions">
                                Dimensions
                                <?php if ($sortColumn == 'dimensions'): ?>
                                    <span
                                        class="sort-arrow"><?php echo $sortOrder == 'ASC' ? '&#9650;' : '&#9660;'; ?></span>
                                <?php endif; ?>
                            </th>
                            <th class="py-3 px-4 border-b cursor-pointer <?php echo $sortColumn == 'delivery_date' ? 'bg-gray-700 text-white' : ''; ?>"
                                data-sort="delivery_date">
                                Delivery Date
                                <?php if ($sortColumn == 'delivery_date'): ?>
                                    <span
                                        class="sort-arrow"><?php echo $sortOrder == 'ASC' ? '&#9650;' : '&#9660;'; ?></span>
                                <?php endif; ?>
                            </th>
                            <th class="py-3 px-4 border-b cursor-pointer <?php echo $sortColumn == 'status' ? 'bg-gray-700 text-white' : ''; ?>"
                                data-sort="status">
                                Status
                                <?php if ($sortColumn == 'status'): ?>
                                    <span
                                        class="sort-arrow"><?php echo $sortOrder == 'ASC' ? '&#9650;' : '&#9660;'; ?></span>
                                <?php endif; ?>
                            </th>
                            <th class="py-3 px-4 border-b">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="parcelTableBody">
                        <?php foreach ($parcels as $parcel): ?>
                            <tr class="hover:bg-gray-100 parcel-row">
                                <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($parcel['parcel_id']); ?>
                                </td>
                                <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($parcel['sender_id']); ?>
                                </td>
                                <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($parcel['receiver_id']); ?>
                                </td>
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
                                <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($parcel['delivery_date']); ?>
                                </td>
                                <td class="py-3 px-4 border-b status" data-id="<?php echo $parcel['parcel_id']; ?>">
                                    <span class="status-text"><?php echo htmlspecialchars($parcel['status']); ?></span>
                                </td>
                                <td class="py-3 px-4 border-b">
                                    <button class="edit-btn bg-blue-500 text-white py-1 px-2 rounded"
                                        data-id="<?php echo $parcel['parcel_id']; ?>">Edit</button>
                                    <button class="delete-btn bg-red-500 text-white py-1 px-2 rounded"
                                        data-id="<?php echo $parcel['parcel_id']; ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>


            <div class="mt-4">
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&sort=<?php echo $sortColumn; ?>&order=<?php echo $sortOrder; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>"
                            class="prev-arrow">‹ Previous</a>
                    <?php endif; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&sort=<?php echo $sortColumn; ?>&order=<?php echo $sortOrder; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>"
                            class="next-arrow">Next ›</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Sorting
            $('th[data-sort]').click(function () {
                var column = $(this).data('sort');
                var currentOrder = $(this).hasClass('asc') ? 'ASC' : 'DESC'; // Determine current order
                var order = currentOrder === 'ASC' ? 'DESC' : 'ASC'; // Toggle sort order

                window.location.href = '?sort=' + column + '&order=' + order + '&search=' + $('#searchInput').val() + '&status=' + $('#statusFilter').val();
            });

            // Initialize sorting arrows
            function updateSortArrows() {
                $('th[data-sort]').each(function () {
                    var column = $(this).data('sort');
                    var sortOrder = '<?php echo $sortOrder; ?>';
                    var sortColumn = '<?php echo $sortColumn; ?>';
                    if (column === sortColumn) {
                        $(this).addClass(sortOrder.toLowerCase());
                        $(this).find('.sort-arrow').html(sortOrder === 'ASC' ? '&#9650;' : '&#9660;');
                    } else {
                        $(this).removeClass('asc desc');
                        $(this).find('.sort-arrow').html('');
                    }
                });
            }
            updateSortArrows(); // Call this function to initialize sort arrows

            // Search and Filter
            $('#searchInput, #statusFilter').on('change keyup', function () {
                var search = $('#searchInput').val();
                var status = $('#statusFilter').val();
                window.location.href = '?sort=<?php echo $sortColumn; ?>&order=<?php echo $sortOrder; ?>&search=' + encodeURIComponent(search) + '&status=' + encodeURIComponent(status);
            });

            // Edit Parcel
            $('.edit-btn').click(function () {
                var parcelId = $(this).data('id');
                var weight = $(this).closest('tr').find('.weight').val();
                var dimensions = $(this).closest('tr').find('.dimensions').val();
                $.ajax({
                    url: 'update_parcel.php',
                    method: 'POST',
                    data: { id: parcelId, weight: weight, dimensions: dimensions },
                    success: function (response) {
                        $('#notification').removeClass('hidden').addClass('bg-green-500');
                        $('#notificationMessage').text('Parcel updated successfully.');
                    },
                    error: function () {
                        $('#notification').removeClass('hidden').addClass('bg-red-500');
                        $('#notificationMessage').text('Error updating parcel.');
                    }
                });
            });

            // Delete Parcel
            $('.delete-btn').click(function () {
                var parcelId = $(this).data('id');
                if (confirm('Are you sure you want to delete this parcel?')) {
                    $.ajax({
                        url: 'delete_parcel.php',
                        method: 'POST',
                        data: { id: parcelId },
                        success: function (response) {
                            $('#notification').removeClass('hidden').addClass('bg-green-500');
                            $('#notificationMessage').text('Parcel deleted successfully.');
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                        },
                        error: function () {
                            $('#notification').removeClass('hidden').addClass('bg-red-500');
                            $('#notificationMessage').text('Error deleting parcel.');
                        }
                    });
                }
            });

            // Status Edit
            $('.status').dblclick(function () {
                var $this = $(this);
                var currentStatus = $this.find('.status-text').text();
                var statusOptions = ['Pending', 'In Transit', 'Delivered', 'Returned'];
                var selectBox = $('<select class="border rounded p-1"></select>');

                statusOptions.forEach(function (status) {
                    selectBox.append('<option value="' + status + '" ' + (status === currentStatus ? 'selected' : '') + '>' + status + '</option>');
                });

                $this.html(selectBox);
                selectBox.focus();

                selectBox.on('blur', function () {
                    var newStatus = $(this).val();
                    $.ajax({
                        url: 'update_parcel_status.php',
                        method: 'POST',
                        data: { id: $this.data('id'), status: newStatus },
                        success: function () {
                            $this.html('<span class="status-text">' + newStatus + '</span>');
                            $('#notification').removeClass('hidden').addClass('bg-green-500');
                            $('#notificationMessage').text('Status updated successfully.');
                        },
                        error: function () {
                            $('#notification').removeClass('hidden').addClass('bg-red-500');
                            $('#notificationMessage').text('Error updating status.');
                        }
                    });
                });
            });

            $('.status').each(function () {
                var status = $(this).text().trim();
                var colorClass = '';

                switch (status) {
                    case 'Pending':
                        colorClass = 'bg-yellow-200 text-yellow-800';
                        break;
                    case 'pending':
                        colorClass = 'bg-yellow-200 text-yellow-800';
                        break;
                    case 'In Transit':
                        colorClass = 'bg-blue-200 text-blue-800';
                        break;
                    case 'in Transit':
                        colorClass = 'bg-blue-200 text-blue-800';
                        break;
                    case 'in transit':
                        colorClass = 'bg-blue-200 text-blue-800';
                        break;
                    case 'In transit':
                        colorClass = 'bg-blue-200 text-blue-800';
                        break;
                    case 'Delivered':
                        colorClass = 'bg-green-200 text-green-800';
                        break;
                    case 'delivered':
                        colorClass = 'bg-green-200 text-green-800';
                        break;
                    case 'Returned':
                        colorClass = 'bg-red-200 text-red-800';
                        break;
                    case 'returned':
                        colorClass = 'bg-red-200 text-red-800';
                        break;
                }

                $(this).addClass(colorClass);
            });

            gsap.from(".parcel-row", {
                opacity: 0,
                y: 20,
                duration: 0.8,
                stagger: 0.1,
                ease: "power4.out",
            });
        });

    </script>
</body>

</html>