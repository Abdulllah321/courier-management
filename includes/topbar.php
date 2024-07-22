<!-- Topbar HTML -->
<header class="bg-white shadow-lg fixed top-0 left-64 right-0 flex justify-between items-center p-4 border-b border-gray-200 z-50">
    <div class="text-gray-800 font-semibold flex items-center">
        <i class="fas fa-user-circle text-xl mr-2"></i>
        <span id="username" class="text-lg"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
    </div>

    <div class="flex items-center space-x-4">
        <!-- Notifications Icon -->
        <button id="notificationsBtn" class="relative p-2 text-gray-600 hover:text-gray-800 transition duration-300">
            <i class="fas fa-bell text-xl"></i>
            <span id="notificationBadge" class="absolute top-0 right-0 text-xs text-white w-5 h-5 flex items-center justify-center bg-red-600 rounded-full hidden"></span>
        </button>

        <!-- Settings Icon -->
        <button id="settingsBtn" class="p-2 text-gray-600 hover:text-gray-800 transition duration-300">
            <i class="fas fa-cog text-xl"></i>
        </button>

        <!-- Logout Button -->
        <button id="logoutBtn" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300 flex items-center">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </button>
    </div>
</header>

<!-- Notifications Dropdown -->
<div id="notificationsDropdown" class="fixed top-16 right-8 bg-white shadow-lg rounded-lg w-80 hidden z-50">
    <div class="p-4 border-b border-gray-200 font-semibold text-gray-800">Notifications</div>
    <div id="notificationsList" class="max-h-80 overflow-y-auto p-4">
        <!-- Notifications will be dynamically inserted here -->

    </div>
</div>


<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-6 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Confirm Logout</h2>
        <p class="text-gray-600 mb-6">Are you sure you want to logout? Any unsaved changes will be lost.</p>
        <div class="flex justify-end space-x-4">
            <button id="confirmLogout" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300">Logout</button>
            <button id="cancelLogout" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400 transition duration-300">Cancel</button>
        </div>
    </div>
</div>