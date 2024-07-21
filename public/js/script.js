// Custom Logout Confirmation Modal
const logoutBtn = document.getElementById("logoutBtn");
const logoutModal = document.getElementById("logoutModal");
const confirmLogout = document.getElementById("confirmLogout");
const cancelLogout = document.getElementById("cancelLogout");

logoutBtn.addEventListener("click", () => {
  logoutModal.classList.remove("hidden");
});

confirmLogout.addEventListener("click", () => {
  window.location.href = "logout.php";
});

cancelLogout.addEventListener("click", () => {
  logoutModal.classList.add("hidden");
});
