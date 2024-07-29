<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedIn();

$database = new Database();
$db = $database->getConnection();
$pageTitle = "Admin Dashboard";
// Fetch total couriers
$query = "SELECT COUNT(*) as total FROM parcels";
$stmt = $db->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$totalCouriers = $row['total'];

// Fetch total customers
$query = "SELECT COUNT(*) as total FROM customers";
$stmt = $db->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$totalCustomers = $row['total'];

// Fetch total agents
$query = "SELECT COUNT(*) as total FROM agents";
$stmt = $db->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$totalAgents = $row['total'];

// Fetch courier statistics
$query = "SELECT MONTHNAME(delivery_date) as month, COUNT(*) as total FROM parcels WHERE YEAR(delivery_date) = YEAR(CURDATE()) GROUP BY MONTH(delivery_date)";
$stmt = $db->prepare($query);
$stmt->execute();
$courierStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch customer statistics
$query = "SELECT MONTHNAME(created_at) as month, COUNT(*) as total FROM customers WHERE YEAR(created_at) = YEAR(CURDATE()) GROUP BY MONTH(created_at)";
$stmt = $db->prepare($query);
$stmt->execute();
$customerStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

$response = [
    'totalCouriers' => $totalCouriers,
    'totalCustomers' => $totalCustomers,
    'totalAgents' => $totalAgents,
    'courierStats' => $courierStats,
    'customerStats' => $customerStats
];

echo json_encode($response);
?>


<!DOCTYPE html>
<html lang="en">

<?php include_once '../includes/head.php'; ?>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <?php include_once '../includes/sidebar.php'; ?>
    <?php include_once '../includes/topbar.php'; ?>

    <main class="ml-64 mt-16 p-6">
        <h1 class="text-3xl font-bold text-red-600 mb-6">Admin Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <!-- Statistics Cards -->
            <div class="bg-white shadow-md rounded-lg p-4">
                <div class="flex items-center">
                    <div class="bg-red-600 text-white p-3 rounded-full mr-4">
                        <i class="fas fa-box"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-700">Total Couriers</h2>
                        <p id="total-couriers" class="text-3xl font-bold text-red-600">...</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-md rounded-lg p-4">
                <div class="flex items-center">
                    <div class="bg-red-600 text-white p-3 rounded-full mr-4">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-700">Total Customers</h2>
                        <p id="total-customers" class="text-3xl font-bold text-red-600">...</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-md rounded-lg p-4">
                <div class="flex items-center">
                    <div class="bg-red-600 text-white p-3 rounded-full mr-4">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-700">Total Agents</h2>
                        <p id="total-agents" class="text-3xl font-bold text-red-600">...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Charts -->
            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Courier Statistics</h2>
                <canvas id="courierChart" class="w-full h-64"></canvas>
            </div>

            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Customer Statistics</h2>
                <canvas id="customerChart" class="w-full h-64"></canvas>
            </div>
        </div>
    </main>

    <?php include_once '../includes/script.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetch('fetch_dashboard_data.php')
                .then(response => response.json())
                .then(data => {
                    // Update total counts
                    document.getElementById('total-couriers').textContent = data.totalCouriers;
                    document.getElementById('total-customers').textContent = data.totalCustomers;
                    document.getElementById('total-agents').textContent = data.totalAgents;

                    // Prepare data for charts
                    const courierLabels = data.courierStats.map(stat => stat.month);
                    const courierData = data.courierStats.map(stat => stat.total);

                    const customerLabels = data.customerStats.map(stat => stat.month);
                    const customerData = data.customerStats.map(stat => stat.total);

                    // Create Courier Chart
                    const ctx1 = document.getElementById('courierChart').getContext('2d');
                    const courierChart = new Chart(ctx1, {
                        type: 'bar',
                        data: {
                            labels: courierLabels,
                            datasets: [{
                                label: 'Couriers Delivered',
                                data: courierData,
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                    // Create Customer Chart
                    const ctx2 = document.getElementById('customerChart').getContext('2d');
                    const customerChart = new Chart(ctx2, {
                        type: 'line',
                        data: {
                            labels: customerLabels,
                            datasets: [{
                                label: 'New Customers',
                                data: customerData,
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                });
        });
    </script>
</body>

</html>