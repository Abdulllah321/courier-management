<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedIn();
$pageTitle = 'Admin Dashboard';

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
                        <p class="text-3xl font-bold text-red-600">1,234</p>
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
                        <p class="text-3xl font-bold text-red-600">567</p>
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
                        <p class="text-3xl font-bold text-red-600">12</p>
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

    <script>
        // Chart.js Scripts
        const ctx1 = document.getElementById('courierChart').getContext('2d');
        const courierChart = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June'],
                datasets: [{
                    label: 'Couriers Delivered',
                    data: [10, 20, 15, 25, 30, 20],
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

        const ctx2 = document.getElementById('customerChart').getContext('2d');
        const customerChart = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June'],
                datasets: [{
                    label: 'New Customers',
                    data: [5, 15, 10, 20, 25, 30],
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
    </script>
    <?php include_once '../includes/script.php'; ?>
</body>

</html>