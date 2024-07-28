<?php
// Get the current script name
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="w-64 bg-gray-800 h-screen fixed top-0 left-0">
    <div class="pt-4 pl-4 text-white">
        <h1 class="text-2xl font-bold mb-6"><a href="dashboard.php">Dashboard</a></h1>
        <ul class="">
            <!-- Dashboard Item -->
            <li class="<?php echo $current_page == 'dashboard.php' ? 'bg-gray-700' : ''; ?>">
                <a href="dashboard.php" class="relative flex items-center text-gray-300 hover:text-white px-4 py-2 rounded-md">
                    <i class="fas fa-dashboard mr-3"></i>Dashboard
                    <span class="<?php echo $current_page == 'dashboard.php' ? 'bg-white' : 'bg-transparent'; ?> absolute right-0 top-0 rounded-l w-1 h-full"></span>
                </a>
            </li>

            <!-- New Courier Item -->
            <li class="<?php echo $current_page == 'courier_form.php' ? 'bg-gray-700' : ''; ?>">
                <a href="courier_form.php" class="relative flex items-center text-gray-300 hover:text-white px-4 py-2 rounded-md">
                    <i class="fas fa-box mr-3"></i>New Courier
                    <span class="<?php echo $current_page == 'courier_form.php' ? 'bg-white' : 'bg-transparent'; ?> absolute right-0 top-0 rounded-l w-1 h-full"></span>
                </a>
            </li>

            <!-- View All Couriers Item -->
            <li class="<?php echo $current_page == 'manage_parcels.php' ? 'bg-gray-700' : ''; ?>">
                <a href="manage_parcels.php" class="relative flex items-center text-gray-300 hover:text-white px-4 py-2 rounded-md">
                    <i class="fas fa-eye mr-3"></i>View All Couriers
                    <span class="<?php echo $current_page == 'manage_parcels.php' ? 'bg-white' : 'bg-transparent'; ?> absolute right-0 top-0 rounded-l w-1 h-full"></span>
                </a>
            </li>

            <!-- Manage Customers Item -->
            <li class="<?php echo $current_page == 'manage_customers.php' ? 'bg-gray-700' : ''; ?>">
                <a href="manage_customers.php" class="relative flex items-center text-gray-300 hover:text-white px-4 py-2 rounded-md">
                    <i class="fas fa-users mr-3"></i>Manage Customers
                    <span class="<?php echo $current_page == 'manage_customers.php' ? 'bg-white' : 'bg-transparent'; ?> absolute right-0 top-0 rounded-l w-1 h-full"></span>
                </a>
            </li>

            <!-- Manage branches Item -->
            <li class="<?php echo $current_page == 'manage_branches.php' ? 'bg-gray-700' : ''; ?>">
                <a href="manage_branches.php" class="relative flex items-center text-gray-300 hover:text-white px-4 py-2 rounded-md">
                    <i class="fas fa-building mr-3"></i>Manage Branches
                    <span class="<?php echo $current_page == 'manage_branches.php' ? 'bg-white' : 'bg-transparent'; ?> absolute right-0 top-0 rounded-l w-1 h-full"></span>
                </a>
            </li>

            <!-- Manage Agents Item -->
            <li class="<?php echo $current_page == 'manage_agents.php' ? 'bg-gray-700' : ''; ?>">
                <a href="manage_agents.php" class="relative flex items-center text-gray-300 hover:text-white px-4 py-2 rounded-md">
                    <i class="fas fa-user-tie mr-3"></i>Manage Agents
                    <span class="<?php echo $current_page == 'manage_agents.php' ? 'bg-white' : 'bg-transparent'; ?> absolute right-0 top-0 rounded-l w-1 h-full"></span>
                </a>
            </li>

            <!-- Logout Item -->
            <li class="<?php echo $current_page == 'logout.php' ? 'bg-gray-700' : ''; ?>">
                <p id="logoutBtn" class="relative cursor-pointer flex items-center text-gray-300 hover:text-white px-4 py-2 rounded-md">
                    <i class="fas fa-sign-out-alt mr-3"></i>Logout
                    <span class="<?php echo $current_page == 'logout.php' ? 'bg-white' : 'bg-transparent'; ?> absolute right-0 top-0 rounded-l w-1 h-full"></span>
                </p>
            </li>
        </ul>
    </div>
</aside>