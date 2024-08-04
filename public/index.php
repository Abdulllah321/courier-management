<?php include "../includes/header.php" ?>
<style>
    /* Fade-in Animation */
    .fade-in {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 1s ease-out, transform 1s ease-out;
    }

    .fade-in.visible {
        opacity: 1;
        transform: translateY(0);
    }

    /* General Styling */
    .back-to-top {
        transition: opacity 0.3s;
    }

    .back-to-top.visible {
        opacity: 1;
    }
</style>

<!-- Hero Section -->
<section id="home" class="bg-red-600 min-h-screen flex items-center justify-center text-center text-white hero-bg">
    <div class="p-6 md:p-12 rounded-lg shadow-lg max-w-xl mx-auto bg-white backdrop-blur-3xl bg-opacity-5">
        <h1 class="text-3xl md:text-5xl font-extrabold mb-4 md:mb-6">Efficient Courier Management</h1>
        <p class="text-base md:text-lg mb-6 md:mb-8 fade-in">Streamline your delivery operations with our
            state-of-the-art management system.</p>
        <!-- Tracking Form -->
        <form action="order_tracking.php" method="get" class="flex flex-col md:flex-row justify-center">
            <input type="text" name="parcel_id" placeholder="Enter Parcel ID"
                class="border border-gray-300 bg-transparent rounded-l-lg px-4 py-3 w-full md:w-2/3 lg:w-3/4 mb-4 md:mb-0 placeholder:text-gray-200"
                required>
            <button type="submit"
                class="bg-red-600 text-white font-bold py-3 px-6 rounded-r-lg transition-colors duration-300 hover:bg-red-700">Track</button>
        </form>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="py-16 md:py-20 bg-gray-100">
    <div class="container mx-auto text-center">
        <h2 class="text-4xl font-bold mb-4 fade-in">Our Services</h2>
        <div class="flex flex-wrap justify-center gap-8 mt-8">
            <div
                class="w-full md:w-1/3 p-6 bg-white rounded-lg shadow-md transition-transform transform hover:-translate-y-2 hover:shadow-lg fade-in">
                <svg class="w-12 h-12 mx-auto mb-4 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8h18M4 6v12m16-12v12" />
                </svg>
                <h3 class="text-xl md:text-2xl font-semibold mb-4">Admin Dashboard</h3>
                <p>Manage all courier operations, create and update bills, and generate comprehensive reports.</p>
            </div>
            <div
                class="w-full md:w-1/3 p-6 bg-white rounded-lg shadow-md transition-transform transform hover:-translate-y-2 hover:shadow-lg fade-in">
                <svg class="w-12 h-12 mx-auto mb-4 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 12h14M5 12l7-7m0 14l7-7" />
                </svg>
                <h3 class="text-xl md:text-2xl font-semibold mb-4">Agent Features</h3>
                <p>Handle local deliveries, update status, and manage branch-specific shipments and reports.</p>
            </div>
            <div
                class="w-full md:w-1/3 p-6 bg-white rounded-lg shadow-md transition-transform transform hover:-translate-y-2 hover:shadow-lg fade-in">
                <svg class="w-12 h-12 mx-auto mb-4 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 9l10 10-3 3-10-10 3-3" />
                </svg>
                <h3 class="text-xl md:text-2xl font-semibold mb-4">User Tracking</h3>
                <p>Track shipments, view status, and print tracking details with ease.</p>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-16 md:py-20">
    <div class="container mx-auto text-center">
        <h2 class="text-4xl font-bold mb-4 fade-in">Key Features</h2>
        <p class="text-xl text-gray-600 mb-8 fade-in">Our system offers a wide range of features designed to optimize
            your delivery process.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mt-8">
            <div
                class="p-6 bg-white rounded-lg shadow-md transition-transform transform hover:-translate-y-2 hover:shadow-lg fade-in">
                <h3 class="text-lg md:text-xl font-semibold mb-4">Real-Time Tracking</h3>
                <p>Monitor your shipments' status in real-time with our advanced tracking system.</p>
            </div>
            <div
                class="p-6 bg-white rounded-lg shadow-md transition-transform transform hover:-translate-y-2 hover:shadow-lg fade-in">
                <h3 class="text-lg md:text-xl font-semibold mb-4">Automated Notifications</h3>
                <p>Receive timely updates and alerts for your deliveries and shipments.</p>
            </div>
            <div
                class="p-6 bg-white rounded-lg shadow-md transition-transform transform hover:-translate-y-2 hover:shadow-lg fade-in">
                <h3 class="text-lg md:text-xl font-semibold mb-4">Flexible Scheduling</h3>
                <p>Customize delivery schedules to fit your needs and optimize efficiency.</p>
            </div>
            <div
                class="p-6 bg-white rounded-lg shadow-md transition-transform transform hover:-translate-y-2 hover:shadow-lg fade-in">
                <h3 class="text-lg md:text-xl font-semibold mb-4">Comprehensive Reporting</h3>
                <p>Generate detailed reports for analysis and improved decision-making.</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section id="testimonials" class="py-16 md:py-20 bg-gray-50">
    <div class="container mx-auto text-center">
        <h2 class="text-3xl font-bold mb-6 fade-in">What Our Users Say</h2>
        <p class="text-lg text-gray-600 mb-8 fade-in">Read the experiences of those who have benefited from our
            services.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div
                class="p-6 bg-white rounded-lg shadow-md transition-transform transform hover:-translate-y-2 hover:shadow-xl fade-in">
                <blockquote class="italic mb-4">"The best courier management system I've ever used. It's intuitive and
                    efficient!"</blockquote>
                <p class="font-semibold text-gray-800">John Doe</p>
                <p class="text-gray-600">Business Owner</p>
            </div>
            <div
                class="p-6 bg-white rounded-lg shadow-md transition-transform transform hover:-translate-y-2 hover:shadow-xl fade-in">
                <blockquote class="italic mb-4">"Our delivery operations have improved significantly since we started
                    using this platform."</blockquote>
                <p class="font-semibold text-gray-800">Jane Smith</p>
                <p class="text-gray-600">Logistics Manager</p>
            </div>
            <div
                class="p-6 bg-white rounded-lg shadow-md transition-transform transform hover:-translate-y-2 hover:shadow-xl fade-in">
                <blockquote class="italic mb-4">"A fantastic tool for managing our courier services. Highly recommend it
                    to anyone needing a robust courier management solution."</blockquote>
                <p class="font-semibold text-gray-800">Alice Johnson</p>
                <p class="text-gray-600">Customer</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-20 bg-gray-200">
    <div class="container mx-auto text-center">
        <h2 class="text-4xl font-bold mb-4 fade-in">Contact Us</h2>
        <p class="text-xl text-gray-600 mb-8 fade-in">Have questions or need assistance? Reach out to us and we'll be happy to help.</p>
        <form action="contact_submit.php" method="post" class="mx-auto max-w-lg text-left fade-in">
            <div class="mb-4">
                <label for="name" class="block text-gray-700">Name</label>
                <input type="text" id="name" name="name"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-600"
                    required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700">Email</label>
                <input type="email" id="email" name="email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-600"
                    required>
            </div>
            <div class="mb-4">
                <label for="message" class="block text-gray-700">Message</label>
                <textarea id="message" name="message"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-600"
                    rows="4" required></textarea>
            </div>
            <button type="submit" class="bg-red-600 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-300 hover:bg-red-700 w-full rounded-lg">Send Message</button>
        </form>
    </div>
</section>


<!-- Back to Top Button -->
<a href="#home"
    class="fixed bottom-4 right-4 bg-red-600 hover:bg-red-700 text-white rounded-full p-4 transition-opacity duration-300 back-to-top">▲</a>

<?php include "../includes/footer.php" ?>

<!-- Custom JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // Fade-in animations
        const faders = document.querySelectorAll('.fade-in');
        const appearOptions = { threshold: 0.1, rootMargin: "0px 0px -100px 0px" };

        const appearOnScroll = new IntersectionObserver(function (entries, appearOnScroll) {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('visible');
                appearOnScroll.unobserve(entry.target);
            });
        }, appearOptions);

        faders.forEach(fader => appearOnScroll.observe(fader));

        // Back to Top button functionality
        const backToTopButton = document.querySelector('.back-to-top');
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 100) backToTopButton.classList.add('visible');
            else backToTopButton.classList.remove('visible');
        });
    });
</script>