<?php
require_once '../config/database.php';

$database = new Database;
$conn = $database->getConnection();
$parcel_id = $_GET['parcel_id'];

try {
    $sql = "SELECT p.*, s.first_name AS sender_first_name, s.last_name AS sender_last_name, s.email AS sender_email, s.phone AS sender_phone, s.address AS sender_address,
                   r.first_name AS receiver_first_name, r.last_name AS receiver_last_name, r.email AS receiver_email, r.phone AS receiver_phone, r.address AS receiver_address
            FROM parcels p
            JOIN customers s ON p.sender_id = s.customer_id
            JOIN customers r ON p.receiver_id = r.customer_id
            WHERE p.parcel_id = :parcel_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':parcel_id', $parcel_id, PDO::PARAM_STR);
    $stmt->execute();
    $parcel = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$parcel) {
        throw new Exception('Parcel not found.');
    }

    // Define status colors
    $status_colors = [
        'Pending' => 'bg-yellow-200 text-yellow-900',
        'In Transit' => 'bg-blue-200 text-blue-900',
        'Delivered' => 'bg-green-200 text-green-900',
        'Returned' => 'bg-red-200 text-red-900'
    ];

    // Define status icons
    $status_icons = [
        'Pending' => 'fas fa-clock',
        'In Transit' => 'fas fa-shipping-fast',
        'Delivered' => 'fas fa-check-circle',
        'Returned' => 'fas fa-undo-alt'
    ];
    $current_status_colors = [
        'Pending' => 'bg-yellow-600 text-yellow-200',
        'In Transit' => 'bg-blue-600 text-blue-200',
        'Delivered' => 'bg-green-600 text-green-200',
        'Returned' => 'bg-red-500 text-red-200'
    ];
    // Get the current status
    $current_status = $parcel['status'];

} catch (Exception $e) {
    $error_message = $e->getMessage();
}
?>

<?php include '../includes/header.php'; ?>

<main class="container mx-auto p-6 bg-white shadow-md rounded-lg mt-10">
    <section class="mb-6 animate__animated animate__fadeIn animate__delay-1s">
        <form action="order_tracking.php" method="get"
            class="flex items-center">
            <input type="text" name="parcel_id" placeholder="Enter Parcel ID"
                class="border border-gray-300 rounded-l-lg px-4 py-2 w-full lg:w-1/3"
                required>
            <button type="submit"
                class="bg-red-600 text-white rounded-r-lg px-4 py-2">Track</button>
        </form>
    </section>

    <?php if (isset($error_message)): ?>
        <section class="animate__animated animate__fadeIn animate__delay-1s">
            <div class="bg-red-100 text-red-800 p-4 rounded-lg shadow-sm">
                <h2 class="text-xl font-bold">Error</h2>
                <p><?php echo htmlspecialchars($error_message); ?></p>
            </div>
        </section>
    <?php else: ?>
        <section class="mb-6 animate__animated animate__fadeIn animate__delay-1s">
            <h1 class="text-2xl font-bold mb-6">Parcel Tracking</h1>
            <!-- Status Tracker -->
            <div class="relative mb-6">
                <div class="flex items-center justify-between relative z-10">
                    <?php
                    $statuses = ['Pending', 'In Transit', 'Delivered', 'Returned'];
                    $total_statuses = count($statuses);
                    $current_index = array_search($current_status, $statuses);

                    foreach ($statuses as $index => $status) {
                        $color = $status_colors[$status];
                        $icon = $status_icons[$status];
                        $is_current = $status === $current_status ? ($current_status_colors[$status] ?? 'text-gray-700') : 'text-gray-700';

                        $is_last = $status === end($statuses) ? 'gap-6' : '';

                        echo '<div class="flex items-center relative text-center ' . $is_last . '">';
                        echo '<div class="w-12 h-12 rounded-full ' . $color . ' flex items-center justify-center text-2xl '. $is_current .'">';
                        echo '<i class="' . $icon . '"></i>';
                        echo '</div>';
                        if ($status !== end($statuses)) {
                            echo '<div class="w-12 h-0.5 ' . $is_current . ' bg-gray-300 mx-2 transition-transform duration-300 ease-in-out transform hover:scale-110"></div>';
                        }
                        echo '<div class="text-sm">' . $status . '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>

            <div class="text-center mt-6">
                <?php if ($current_status === 'Delivered'): ?>
                    <p class="text-lg font-semibold">Your package has been delivered!</p>
                <?php else: ?>
                    <p class="text-lg font-semibold">Your package will be delivered soon. Current status:
                        <?php echo htmlspecialchars($current_status); ?>.
                    </p>
                <?php endif; ?>
            </div>

        </section>

        <section
            class="flex flex-col lg:flex-row lg:justify-between mb-6 animate__animated animate__fadeIn animate__delay-1s">
            <!-- Parcel Info -->
            <article class="lg:w-1/2 mb-6 lg:mb-0">
                <h2 class="text-xl font-semibold mb-4">Parcel Information</h2>
                <div
                    class="bg-gray-50 p-6 rounded-lg shadow-sm">
                    <p><span class="font-semibold">Parcel ID:</span> <?php echo htmlspecialchars($parcel['parcel_id']); ?>
                    </p>
                    <p><span class="font-semibold">Weight:</span> <?php echo htmlspecialchars($parcel['weight']); ?> kg</p>
                    <p><span class="font-semibold">Dimensions:</span> <?php echo htmlspecialchars($parcel['dimensions']); ?>
                    </p>
                    <p><span class="font-semibold">Status:</span>
                        <span class="px-3 py-1 rounded <?php echo $status_colors[$parcel['status']]; ?>">
                            <?php echo htmlspecialchars($parcel['status']); ?>
                        </span>
                    </p>
                    <p><span class="font-semibold">Delivery Date:</span>
                        <?php echo htmlspecialchars($parcel['delivery_date'] ? $parcel['delivery_date'] : 'N/A'); ?>
                    </p>
                </div>
            </article>

            <!-- Customer Info -->
            <h2 class="text-xl font-semibold mb-4">Customer Information</h2>
            <article>
                <div class="flex flex-col lg:flex-row lg:space-x-6 ">
                    <!-- Sender Info -->
                    <div
                        class="bg-gray-50 p-6 rounded-lg shadow-sm mb-6 lg:mb-0 lg:w-1/2">
                        <h3 class="text-lg font-semibold mb-2">Sender</h3>
                        <p><span class="font-semibold">Name:</span>
                            <?php echo htmlspecialchars($parcel['sender_first_name'] . ' ' . $parcel['sender_last_name']); ?>
                        </p>
                        <p><span class="font-semibold">Email:</span>
                            <?php echo htmlspecialchars($parcel['sender_email']); ?>
                        </p>
                        <p><span class="font-semibold">Phone:</span>
                            <?php echo htmlspecialchars($parcel['sender_phone']); ?>
                        </p>
                        <p><span class="font-semibold">Address:</span>
                            <?php echo htmlspecialchars($parcel['sender_address']); ?>
                        </p>
                    </div>

                    <!-- Receiver Info -->
                    <div
                        class="bg-gray-50 p-6 rounded-lg shadow-sm lg:w-1/2">
                        <h3 class="text-lg font-semibold mb-2">Receiver</h3>
                        <p><span class="font-semibold">Name:</span>
                            <?php echo htmlspecialchars($parcel['receiver_first_name'] . ' ' . $parcel['receiver_last_name']); ?>
                        </p>
                        <p><span class="font-semibold">Email:</span>
                            <?php echo htmlspecialchars($parcel['receiver_email']); ?>
                        </p>
                        <p><span class="font-semibold">Phone:</span>
                            <?php echo htmlspecialchars($parcel['receiver_phone']); ?>
                        </p>
                        <p><span class="font-semibold">Address:</span>
                            <?php echo htmlspecialchars($parcel['receiver_address']); ?>
                        </p>
                    </div>
                </div>
            </article>
        </section>

    <?php endif; ?>
</main>

<?php include '../includes/footer' ?>