<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedInAgent();
    

$pageTitle = "Agent Dashboard";
?>

<!DOCTYPE html>
<html lang="en">

<?php include_once '../includes/head.php'; ?>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <?php include_once '../includes/sidebar.php'; ?>
    <?php include_once '../includes/topbar.php'; ?>

    <main class="ml-64 mt-16 p-6">
        <h1 class="text-3xl font-bold text-red-600 mb-6">Agent Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <!-- Statistics Cards -->
            <div class="bg-white shadow-md rounded-lg p-4">
                <div class="flex items-center">
                    <div class="bg-red-600 text-white p-3 rounded-full mr-4">
                        <i class="fas fa-box"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-700">Assigned Couriers</h2>
                        <p id="assigned-couriers" class="text-3xl font-bold text-red-600">...</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-md rounded-lg p-4">
                <div class="flex items-center">
                    <div class="bg-red-600 text-white p-3 rounded-full mr-4">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-700">Customers</h2>
                        <p id="customers-count" class="text-3xl font-bold text-red-600">...</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-md rounded-lg p-4">
                <div class="flex items-center">
                    <div class="bg-red-600 text-white p-3 rounded-full mr-4">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-700">Notifications</h2>
                        <p id="notifications-count" class="text-3xl font-bold text-red-600">...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Courier Chart -->
            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Courier Statistics</h2>
                <canvas id="courierChart" class="w-full h-64"></canvas>
            </div>

            <!-- Additional Features -->
            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Manage Couriers</h2>
                <button class="bg-red-600 text-white px-4 py-2 rounded" onclick="location.href='add_courier.php'">Add
                    New Courier</button>
                <button class="bg-blue-600 text-white px-4 py-2 rounded ml-2"
                    onclick="location.href='view_couriers.php'">View All Couriers</button>
            </div>
        </div>
    </main>

    <?php include_once '../includes/script.php'; ?>
    <script>
        fetch('fetch_agent_dashboard_data.php')
            .then(response => response.json())
            .then(data => {
                // Update total counts
                document.getElementById('assigned-couriers').textContent = data.assignedCouriers;
                document.getElementById('customers-count').textContent = data.customersCount;
                document.getElementById('notifications-count').textContent = data.notificationsCount;

                // Extract unique days and statuses
                const currentDate = new Date();
                const currentMonth = currentDate.getMonth();
                const currentYear = currentDate.getFullYear();
                const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
                const days = Array.from({ length: daysInMonth }, (_, i) => (i + 1).toString());

                const courierStatuses = [...new Set(data.courierStats.map(stat => stat.status))];

                const statusColors = {
                    'pending': 'rgba(255, 99, 132, 0.5)',
                    'delivered': 'rgba(54, 162, 235, 0.5)',
                    'returned': 'rgba(255, 206, 86, 0.5)',
                    'in transit': 'rgba(153, 102, 255, 0.5)',
                    'default': 'rgba(0, 0, 0, 0.5)'
                };

                const courierDatasets = courierStatuses.map((status, index) => {
                    const normalizedStatus = status.trim().toLowerCase();
                    const statusData = data.courierStats.filter(stat => stat.status === status);

                    const totals = days.map(day => {
                        const dayData = statusData.find(stat => stat.day === day);
                        return dayData ? dayData.total : 0;
                    });

                    return {
                        label: status,
                        data: totals,
                        backgroundColor: statusColors[normalizedStatus] || statusColors['default'],
                        borderColor: statusColors[normalizedStatus] || statusColors['default'],
                        borderWidth: 1
                    };
                });

                const ctx1 = document.getElementById('courierChart').getContext('2d');
                new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        labels: days,
                        datasets: courierDatasets
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching data:', error));
    </script>

</body>

</html>