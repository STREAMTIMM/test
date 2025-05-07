// EDIT USERNAME
const updateBtn = document.getElementById('updateBtn');
const usernameInput = document.getElementById('usernameInput');
const usernameDisplay = document.getElementById('usernameDisplay');

updateBtn.addEventListener('click', () => {
  if (usernameInput.disabled) {

    usernameInput.disabled = false;
    usernameInput.focus();
    updateBtn.textContent = 'SAVE';
  } else {

    const newUsername = usernameInput.value.trim();
    if (newUsername !== "") {
      usernameDisplay.textContent = newUsername;
    }
    usernameInput.disabled = true;
    updateBtn.textContent = 'UPDATE';
  }
});

// LOGOUT
document.addEventListener("DOMContentLoaded", function () {
    const logoutBtn = document.querySelector('.logout-btn');
    const confirmPopup = document.querySelector('.confirm-popup');
    const leftPopup = document.querySelector('.left-popup');
    const yesBtn = document.querySelector('.yes-btn');
    const noBtn = document.querySelector('.no-btn');
  
    logoutBtn.addEventListener('click', () => {
      confirmPopup.classList.remove('hidden');
    });
  
    yesBtn.addEventListener('click', () => {
      confirmPopup.classList.add('hidden');
      leftPopup.classList.remove('hidden');
    });
  
    noBtn.addEventListener('click', () => {
      confirmPopup.classList.add('hidden');
    });
  
    document.querySelectorAll('.close-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        btn.closest('.popup').classList.add('hidden');
        // Check if this is the left popup (after clicking yes)
        if (btn.closest('.popup').classList.contains('left-popup')) {
          window.location.href = "LOGIN PAGE.html"; // Redirect to login page
        }
      });
    });
  });

document.addEventListener("DOMContentLoaded", function () {
  const monthlyReportBtn = document.querySelector(".nav-btn");

  if (monthlyReportBtn) {
    monthlyReportBtn.addEventListener("click", function () {
      window.location.href = "STUDENT REPORTS PAGE.html";
    });
  }
});

document.addEventListener("DOMContentLoaded", function() {
    const profileIcon = document.getElementById("profileIcon");
    
    if (profileIcon) {
        profileIcon.addEventListener("click", function() {
            window.location.href = "STUDENT PROFILE SETTINGs PAGE.html";
        });
        
        // Optional: Add cursor pointer to indicate it's clickable
        profileIcon.style.cursor = "pointer";
    }
});

document.addEventListener("DOMContentLoaded", function() {
  const notifIcon = document.getElementById("notifIcon");
  
  if (notifIcon) {
      notifIcon.addEventListener("click", function() {
          window.location.href = "STUDENT NOTIFICATION PAGE.html";
      });
      
      // Optional: Add cursor pointer to indicate it's clickable
      notifIcon.style.cursor = "pointer";
  }
});