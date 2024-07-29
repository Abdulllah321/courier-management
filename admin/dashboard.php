<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedIn();

$pageTitle = "Admin Dashboard";

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
                        <p id="total-customers" class="text-3xl font-bold text-red-600">...
                        </p>
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
        fetch('fetch_dashboard_data.php')
            .then(response => response.json())
            .then(data => {
                // Update total counts
                document.getElementById('total-couriers').textContent = data.totalCouriers;
                document.getElementById('total-customers').textContent = data.totalCustomers;
                document.getElementById('total-agents').textContent = data.totalAgents;

                // Extract unique days and statuses
                const currentDate = new Date();
                const currentMonth = currentDate.getMonth();
                const currentYear = currentDate.getFullYear();
                const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
                const days = Array.from({ length: daysInMonth }, (_, i) => (i + 1).toString());

                const courierStatuses = [...new Set(data.courierStats.map(stat => stat.status))];
                const customerStatuses = [...new Set(data.customerStats.map(stat => stat.status))];

                // Define colors for statuses
                const statusColors = {
                    'pending': 'rgba(255, 99, 132, 0.5)', // Pink
                    'delivered': 'rgba(54, 162, 235, 0.5)', // Blue
                    'returned': 'rgba(255, 206, 86, 0.5)', // Yellow
                    'in transit': 'rgba(153, 102, 255, 0.5)', // Purple
                    'default': 'rgba(0, 0, 0, 0.5)' // Black
                };
                // Prepare data for courier chart
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

                // Create Courier Chart
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

                // Prepare data for customer chart
                const customerDatasets = customerStatuses.map((status, index) => {
                    const statusData = data.customerStats.filter(stat => stat.status === status);

                    const totals = days.map(day => {
                        const dayData = statusData.find(stat => stat.day === day);
                        return dayData ? dayData.total : 0;
                    });

                    return {
                        label: status,
                        data: totals,
                        backgroundColor: statusColors[status] || statusColors['default'],
                        borderColor: statusColors[status] || statusColors['default'],
                        borderWidth: 1
                    };
                });

                // Create Customer Chart
                const ctx2 = document.getElementById('customerChart').getContext('2d');
                new Chart(ctx2, {
                    type: 'line',
                    data: {
                        labels: days,
                        datasets: customerDatasets
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