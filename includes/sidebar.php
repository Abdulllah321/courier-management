<?php
// Get the current script name
$current_page = basename($_SERVER['PHP_SELF']);

// Retrieve the saved sidebar color from local storage
$sidebarColor = isset($_COOKIE['sidebarColor']) ? $_COOKIE['sidebarColor'] : 'bg-gray-800';

// Check the user role from session
$userRole = $_SESSION['role']; // Assuming 'role' is stored in session
?>

<aside id="sidebar"
    class="w-64 h-screen fixed top-0 left-0 sidebar-transition <?php echo htmlspecialchars($sidebarColor); ?>">
    <div class="pt-4 pl-4 text-white">
        <h1 class="text-2xl font-bold mb-6">
            <a href="dashboard.php">Dashboard</a>
        </h1>
        <ul>
            <!-- Dashboard Item -->
            <li class="<?php echo $current_page == 'dashboard.php' ? 'bg-gray-700 backdrop-blur-3xl' : ''; ?>">
                <a href="dashboard.php"
                    class="relative flex items-center text-gray-300 hover:text-white px-4 py-2 rounded-md">
                    <i class="fas fa-dashboard mr-3"></i>Dashboard
                    <span
                        class="<?php echo $current_page == 'dashboard.php' ? 'bg-white' : 'bg-transparent'; ?> absolute right-0 top-0 rounded-l w-1 h-full"></span>
                </a>
            </li>

            <?php if ($userRole === 'admin' || $userRole === 'agent'): ?>
                <!-- New Courier Item -->
                <li class="<?php echo $current_page == 'courier_form.php' ? 'bg-gray-700' : ''; ?>">
                    <a href="courier_form.php"
                        class="relative flex items-center text-gray-300 hover:text-white px-4 py-2 rounded-md">
                        <i class="fas fa-box mr-3"></i>New Courier
                        <span
                            class="<?php echo $current_page == 'courier_form.php' ? 'bg-white' : 'bg-transparent'; ?> absolute right-0 top-0 rounded-l w-1 h-full"></span>
                    </a>
                </li>

                <!-- View All Couriers Item -->
                <li class="<?php echo $current_page == 'manage_parcels.php' ? 'bg-gray-700' : ''; ?>">
                    <a href="manage_parcels.php"
                        class="relative flex items-center text-gray-300 hover:text-white px-4 py-2 rounded-md">
                        <i class="fas fa-eye mr-3"></i>View All Couriers
                        <span
                            class="<?php echo $current_page == 'manage_parcels.php' ? 'bg-white' : 'bg-transparent'; ?> absolute right-0 top-0 rounded-l w-1 h-full"></span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($userRole === 'admin'): ?>
                <!-- Manage Customers Item -->
                <li class="<?php echo $current_page == 'manage_customers.php' ? 'bg-gray-700' : ''; ?>">
                    <a href="manage_customers.php"
                        class="relative flex items-center text-gray-300 hover:text-white px-4 py-2 rounded-md">
                        <i class="fas fa-users mr-3"></i>Manage Customers
                        <span
                            class="<?php echo $current_page == 'manage_customers.php' ? 'bg-white' : 'bg-transparent'; ?> absolute right-0 top-0 rounded-l w-1 h-full"></span>
                    </a>
                </li>

                <!-- Manage Branches Item -->
                <li class="<?php echo $current_page == 'manage_branches.php' ? 'bg-gray-700' : ''; ?>">
                    <a href="manage_branches.php"
                        class="relative flex items-center text-gray-300 hover:text-white px-4 py-2 rounded-md">
                        <i class="fas fa-building mr-3"></i>Manage Branches
                        <span
                            class="<?php echo $current_page == 'manage_branches.php' ? 'bg-white' : 'bg-transparent'; ?> absolute right-0 top-0 rounded-l w-1 h-full"></span>
                    </a>
                </li>

                <!-- Manage Agents Item -->
                <li class="<?php echo $current_page == 'manage_agents.php' ? 'bg-gray-700' : ''; ?>">
                    <a href="manage_agents.php"
                        class="relative flex items-center text-gray-300 hover:text-white px-4 py-2 rounded-md">
                        <i class="fas fa-user-tie mr-3"></i>Manage Agents
                        <span
                            class="<?php echo $current_page == 'manage_agents.php' ? 'bg-white' : 'bg-transparent'; ?> absolute right-0 top-0 rounded-l w-1 h-full"></span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Logout Item -->
            <li class="<?php echo $current_page == 'logout.php' ? 'bg-gray-700' : ''; ?>">
                <p id="logoutBtn"
                    class="relative cursor-pointer flex items-center text-gray-300 hover:text-white px-4 py-2 rounded-md">
                    <i class="fas fa-sign-out-alt mr-3"></i>Logout
                    <span
                        class="<?php echo $current_page == 'logout.php' ? 'bg-white' : 'bg-transparent'; ?> absolute right-0 top-0 rounded-l w-1 h-full"></span>
                </p>
            </li>
        </ul>

        <!-- Color Selector -->
        <div class="mt-6">
            <h2 class="text-lg font-semibold text-white mb-2">Change Sidebar Color</h2>
            <div class="flex flex-wrap gap-2 justify-center">
                <button class="color-box bg-gray-800" data-color="bg-gray-800" title="Gray Dark"></button>
                <button class="color-box bg-sky-800" data-color="bg-sky-800" title="Sky Blue"></button>
                <button class="color-box bg-green-800" data-color="bg-green-800" title="Green"></button>
                <button class="color-box bg-rose-800" data-color="bg-rose-800" title="Rose"></button>
                <button class="color-box bg-orange-600" data-color="bg-orange-600" title="Orange"></button>
                <button class="color-box bg-yellow-500" data-color="bg-yellow-500" title="Yellow"></button>

                <!-- Gradients -->
                <button class="color-box gradient bg-gradient-to-r from-blue-900 via-blue-800 to-blue-700"
                    data-gradient="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-700"
                    title="Midnight Blue"></button>
                <button class="color-box gradient bg-gradient-to-r from-gray-900 via-gray-800 to-black"
                    data-gradient="bg-gradient-to-r from-gray-900 via-gray-800 to-black" title="Deep Space"></button>
                <button class="color-box gradient bg-gradient-to-r from-green-900 via-green-800 to-teal-700"
                    data-gradient="bg-gradient-to-r from-green-900 via-green-800 to-teal-700"
                    title="Dark Forest"></button>
                <button class="color-box gradient bg-nebula-void" data-gradient="bg-nebula-void"
                    title="Flame Gradient"></button>
                <button class="color-box gradient bg-gradient-to-r from-teal-500 to-cyan-500"
                    data-gradient="bg-gradient-to-r from-teal-500 to-cyan-500" title="Tropical Gradient"></button>
                <button class="color-box gradient bg-gradient-to-r from-gray-600 to-gray-900"
                    data-gradient="bg-gradient-to-r from-gray-600 to-gray-900" title="Steel Gradient"></button>
            </div>
        </div>
    </div>
</aside>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', () => {
        // Apply GSAP animations
        gsap.from("#sidebar", { x: -100, opacity: 0, duration: 0.5, ease: "power3.out" });

        // Function to remove existing color and gradient classes
        function removeExistingClasses() {
            const sidebar = document.getElementById('sidebar');
            sidebar.className = sidebar.className
                .split(' ')
                .filter(cls => !(cls.startsWith('bg-') || cls.startsWith('from-') || cls.startsWith('to-') || cls.startsWith('via-')))
                .join(' ');
        }

        // Color change functionality
        document.querySelectorAll('[data-color]').forEach(button => {
            button.addEventListener('click', () => {
                const colorClass = button.getAttribute('data-color');
                removeExistingClasses();
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.add(colorClass);

                // Save the selected color to local storage
                localStorage.setItem('sidebarColor', colorClass);
            });
        });

        // Gradient change functionality
        document.querySelectorAll('[data-gradient]').forEach(button => {
            button.addEventListener('click', () => {
                const gradientClasses = button.getAttribute('data-gradient').split(' ');
                removeExistingClasses();
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.add(...gradientClasses);

                // Save the selected gradient to local storage
                localStorage.setItem('sidebarColor', button.getAttribute('data-gradient'));
            });
        });

        // Retrieve and apply the saved color or gradient from local storage
        const savedColor = localStorage.getItem('sidebarColor');
        if (savedColor) {
            removeExistingClasses();
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.add(...savedColor.split(' '));
        }
    });
</script>
<style>
    .color-box {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 2px solid white;
        position: relative;
        cursor: pointer;
        transition: transform 0.3s;
    }

    .color-box:hover {
        transform: scale(1.1);
    }

    .color-box.gradient {
        border: none;
    }

    .color-box::after {
        content: attr(title);
        position: absolute;
        bottom: -30px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.75);
        color: #fff;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        display: none;
        /* Ensure tooltip is above other elements */
    }

    .color-box:hover::after {
        display: block;
    }
</style>