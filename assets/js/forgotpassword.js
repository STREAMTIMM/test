document.getElementById("forgot-form").addEventListener("submit", function (e) {
    e.preventDefault();
    document.getElementById("forgot-box").style.display = "none";
    document.getElementById("reset-box").style.display = "block";
});

document.getElementById("back-to-forgot").addEventListener("click", function (e) {
    e.preventDefault();
    document.getElementById("reset-box").style.display = "none";
    document.getElementById("forgot-box").style.display = "block";
});

// Go from Forgot Password → Reset Code
document.getElementById("forgot-form").addEventListener("submit", function (e) {
    e.preventDefault();
    document.getElementById("forgot-box").style.display = "none";
    document.getElementById("reset-box").style.display = "block";
});

// Go from Reset Code → New Password
// document.getElementById("reset-password-btn").addEventListener("click", function () {
//     document.getElementById("reset-box").style.display = "none";
//     document.getElementById("new-password-box").style.display = "block";
// });

// Back from Reset Code → Forgot Password
// document.getElementById("back-to-forgot").addEventListener("click", function (e) {
//     e.preventDefault();
//     document.getElementById("reset-box").style.display = "none";
//     document.getElementById("forgot-box").style.display = "block";
// });

// Back from New Password → Reset Code
// document.getElementById('back-to-reset').addEventListener('click', function (e) {
//     e.preventDefault();
//     document.getElementById('new-password-box').style.display = 'none';
//     document.getElementById('reset-box').style.display = 'block';
// });

function togglePassword(inputId, iconEl) {
    const input = document.getElementById(inputId);
    const isPassword = input.type === "password";
    input.type = isPassword ? "text" : "password";

    iconEl.innerHTML = isPassword
        ? `<svg xmlns="http://www.w3.org/2000/svg" class="eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
           d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a10.06 10.06 0 012.653-4.362m3.136-2.113A9.957 9.957 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.056 10.056 0 01-4.422 5.225M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
         </svg>`
        : `<svg xmlns="http://www.w3.org/2000/svg" class="eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
           d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
           d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z" />
         </svg>`;
}

// Back from Login Page
document.getElementById('new-password-form').addEventListener('submit', function (e) {
    e.preventDefault();
    window.location.href = "logipage1.html";
});
