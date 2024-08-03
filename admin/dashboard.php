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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 place-items-center">
            <!-- Charts -->
            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Courier Statistics</h2>
                <canvas id="courierChart" class="w-full h-64"></canvas>
            </div>



            <div class="bg-white shadow-md rounded-lg p-4 mt-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Yearly Parcel Statistics</h2>
                <canvas id="yearlyChart" class="w-full h-64"></canvas>
            </div>

            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Branches Status</h2>
                <canvas id="branchStatusChart" class="w-auto !h-64"></canvas>
            </div>

        </div>
    </main>

    <?php include_once '../includes/script.php'; ?>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            fetch('fetch_dashboard_data.php')
                .then(response => response.json())
                .then(data => {
                    // Update total counts
                    document.getElementById('total-couriers').textContent = data.totalCouriers;
                    document.getElementById('total-customers').textContent = data.totalCustomers;
                    document.getElementById('total-agents').textContent = data.totalAgents;

                    // Define status colors
                    const statusColors = {
                        'pending': 'rgba(255, 99, 132, 0.5)',
                        'delivered': 'rgba(54, 162, 235, 0.5)',
                        'returned': 'rgba(255, 206, 86, 0.5)',
                        'in transit': 'rgba(153, 102, 255, 0.5)',
                        'active': 'rgba(0, 255, 0, 0.5)',
                        'inactive': 'rgba(255, 0, 0, 0.5)',
                        'default': 'rgba(0, 0, 0, 0.5)'
                    };

                    // Prepare data for courier chart
                    const daysInMonth = new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).getDate();
                    const days = Array.from({ length: daysInMonth }, (_, i) => (i + 1).toString());

                    const courierStatuses = [...new Set(data.courierStats.map(stat => stat.status))];
                    const courierDatasets = courierStatuses.map(status => {
                        const statusData = data.courierStats.filter(stat => stat.status === status);

                        const totals = days.map(day => {
                            const dayData = statusData.find(stat => stat.day === day);
                            return dayData ? parseInt(dayData.total) : 0;
                        });

                        return {
                            label: status,
                            data: totals,
                            backgroundColor: statusColors[status.toLowerCase()] || statusColors['default'],
                            borderColor: statusColors[status.toLowerCase()] || statusColors['default'],
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

                    // Prepare data for yearly chart
                    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                    const yearlyStatuses = [...new Set(data.yearlyStats.map(stat => stat.status))];

                    const yearlyDatasets = yearlyStatuses.map(status => {
                        const statusData = data.yearlyStats.filter(stat => stat.status === status);

                        const totals = monthNames.map((month, index) => {
                            const monthData = statusData.find(stat => parseInt(stat.month) === index + 1);
                            return monthData ? parseInt(monthData.total) : 0;
                        });

                        return {
                            label: status,
                            data: totals,
                            backgroundColor: statusColors[status.toLowerCase()] || statusColors['default'],
                            borderColor: statusColors[status.toLowerCase()] || statusColors['default'],
                            borderWidth: 1
                        };
                    });

                    // Create Yearly Chart
                    const ctx3 = document.getElementById('yearlyChart').getContext('2d');
                    new Chart(ctx3, {
                        type: 'bar',
                        data: {
                            labels: monthNames,
                            datasets: yearlyDatasets
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                    // Prepare Branch Status Pie Chart
                    const branchLabels = data.branchStats.map(stat => stat.status);
                    const branchData = data.branchStats.map(stat => parseInt(stat.total));

                    const branchPieChart = document.getElementById('branchStatusChart').getContext('2d');
                    new Chart(branchPieChart, {
                        type: 'pie',
                        data: {
                            labels: branchLabels,
                            datasets: [{
                                data: branchData,
                                backgroundColor: branchLabels.map(label => statusColors[label.toLowerCase()] || statusColors['default']),
                                borderColor: branchLabels.map(label => statusColors[label.toLowerCase()] || statusColors['default']),
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function (tooltipItem) {
                                            const label = tooltipItem.label || '';
                                            const value = tooltipItem.raw || 0;
                                            return `${label} : ${value} branches`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error fetching dashboard data:', error));
        });

    </script>



</body>

</html>