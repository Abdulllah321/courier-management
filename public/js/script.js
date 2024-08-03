document.addEventListener("DOMContentLoaded", () => {
  function dismissMessage(id) {
    const msgElement = document.getElementById(id);
    if (msgElement) {
      msgElement.classList.add("opacity-0");
      setTimeout(() => msgElement.remove(), 300);
    }
  }

  // Set up progress bar animations for session messages
  document.querySelectorAll(".session-message").forEach((message) => {
    const duration = parseInt(message.dataset.duration, 10) || 15;
    const progressBar = message.querySelector(".progress");
    const closeButton = message.querySelector(".close");
    let countdown = duration;

    // Close button event listener
    closeButton.addEventListener("click", () => {
      dismissMessage(message.id);
    });

    const updateProgress = () => {
      const progressPercentage = (countdown / duration) * 100;
      progressBar.style.width = `${progressPercentage}%`;

      if (countdown <= 0) {
        dismissMessage(message.id);
      } else {
        if (countdown <= duration * 0.5) {
          progressBar.classList.replace("bg-green-500", "bg-yellow-500");
        }
        if (countdown <= duration * 0.2) {
          progressBar.classList.replace("bg-yellow-500", "bg-red-500");
        }
        countdown--;
        setTimeout(updateProgress, 1000);
      }
    };

    setTimeout(updateProgress, 1000);
  });

  // Handle logout modal
  const logoutButtons = document.querySelectorAll("#logoutBtn");
  const logoutModal = document.getElementById("logoutModal");
  const confirmLogout = document.getElementById("confirmLogout");
  const cancelLogout = document.getElementById("cancelLogout");

  if (
    logoutButtons.length > 0 &&
    logoutModal &&
    confirmLogout &&
    cancelLogout
  ) {
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
  }

  // Handle notifications dropdown if element exists
  const notificationsDropdown = document.getElementById(
    "notificationsDropdown"
  );
  const notificationList = document.getElementById("notificationsList");
  const notificationBadge = document.getElementById("notificationBadge");
  const notificationsBtn = document.getElementById("notificationsBtn");

  if (
    notificationsDropdown &&
    notificationList &&
    notificationBadge &&
    notificationsBtn
  ) {
    gsap.set(notificationsDropdown, { autoAlpha: 0, y: -20 });

    notificationsBtn.addEventListener("click", () => {
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
        setTimeout(() => {
          notificationsDropdown.classList.add("hidden");
        }, 300);
      }
    });

    document.addEventListener("click", (event) => {
      if (
        !notificationsDropdown.contains(event.target) &&
        !notificationsBtn.contains(event.target)
      ) {
        gsap.to(notificationsDropdown, {
          autoAlpha: 0,
          y: -20,
          duration: 0.3,
          ease: "power1.out",
        });
        setTimeout(() => {
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
                  notification.status === "unread"
                    ? '<span class="absolute top-0 right-0 w-2.5 h-2.5 bg-red-600 rounded-full"></span>'
                    : ""
                }
              `;
              if (notification.status === "unread") {
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
  }

  
});


