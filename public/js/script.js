document.addEventListener("DOMContentLoaded", () => {
  const logoutButtons = document.querySelectorAll("#logoutBtn");
  const logoutModal = document.getElementById("logoutModal");
  const confirmLogout = document.getElementById("confirmLogout");
  const cancelLogout = document.getElementById("cancelLogout");

  logoutButtons.forEach((button) => {
    button.addEventListener("click", () => {
      logoutModal.classList.remove("hidden");
    });
  });

  confirmLogout.addEventListener("click", () => {
    window.location.href = "logout.php";
  });

  cancelLogout.addEventListener("click", () => {
    logoutModal.classList.add("hidden");
  });

  const notificationsDropdown = document.getElementById(
    "notificationsDropdown"
  );
  const notificationList = document.getElementById("notificationsList");
  const notificationBadge = document.getElementById("notificationBadge");

  // Initialize dropdown and modal to hidden state
  gsap.set(notificationsDropdown, {
    autoAlpha: 0,
    y: -20,
  });

  // Toggle notifications dropdown visibility
  document.getElementById("notificationsBtn").addEventListener("click", () => {
    const isVisible = !notificationsDropdown.classList.contains("hidden");
    gsap.to(notificationsDropdown, {
      autoAlpha: isVisible ? 0 : 1,
      y: isVisible ? -20 : 0,
      duration: 0.3,
      ease: "power1.out",
    });
    if (notificationsDropdown.classList.contains("hidden")) {
      notificationsDropdown.classList.remove("hidden");
    } else {
      setTimeout(function () {
        notificationsDropdown.classList.add("hidden");
      }, 300);
    }
  });

  // Close notifications dropdown when clicking outside
  document.addEventListener("click", (event) => {
    const button = document.getElementById("notificationsBtn");
    if (
      !notificationsDropdown.contains(event.target) &&
      !button.contains(event.target)
    ) {
      gsap.to(notificationsDropdown, {
        autoAlpha: 0,
        y: -20,
        duration: 0.3,
        ease: "power1.out",
      });
      setTimeout(function () {
        notificationsDropdown.classList.add("hidden");
      }, 300);
    }
  });

  // Fetch notifications
  fetch("fetch_notifications.php")
    .then((response) => response.json())
    .then((notifications) => {
      try {
        notificationList.innerHTML = "";
        let unreadCount = 0;

        if (notifications.length === 0) {
          notificationList.innerHTML = `
                        <div id="noNotificationsMessage" class="text-gray-600 text-center flex flex-col items-center">
                            <i class="fas fa-bell-slash text-4xl mb-2"></i>
                            <p>No notifications</p>
                        </div>`;
        } else {
          notifications.forEach((notification) => {
            const notificationItem = document.createElement("span");
            notificationItem.className =
              "py-2 px-4 border-b border-gray-200 text-gray-800 block relative cursor-pointer";
            notificationItem.dataset.href = notification.url;
            notificationItem.dataset.id = notification.id;
            notificationItem.innerHTML = `
                            <p>${notification.message}</p>
                            ${
                              notification.status == "unread"
                                ? '<span class="absolute top-0 right-0 w-2.5 h-2.5 bg-red-600 rounded-full"></span>'
                                : ""
                            }
                        `;
            if (notification.status == "unread") {
              notificationItem.classList.add("font-bold");
              unreadCount++;
            }
            notificationList.appendChild(notificationItem);
          });

          if (unreadCount > 0) {
            notificationBadge.textContent = unreadCount;
            notificationBadge.classList.remove("hidden");
          } else {
            notificationBadge.classList.add("hidden");
          }
        }

        notificationList.addEventListener("click", (event) => {
          const notificationItem = event.target.closest("span[data-id]");
          if (notificationItem) {
            event.preventDefault();
            const notificationId = notificationItem.dataset.id;
            fetch("mark_notification_read.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/x-www-form-urlencoded",
              },
              body: `id=${notificationId}`,
            })
              .then((response) => response.text())
              .then(() => {
                notificationItem.classList.remove("font-bold");
                unreadCount--;
                if (unreadCount > 0) {
                  notificationBadge.textContent = unreadCount;
                } else {
                  notificationBadge.classList.add("hidden");
                }
                window.location.href = notificationItem.dataset.href;
              })
              .catch((error) =>
                console.error("Error marking notification as read:", error)
              );
          }
        });
      } catch (error) {
        console.error("Error parsing JSON:", error);
      }
    })
    .catch((error) => console.error("Error fetching notifications:", error));
});
