
        // EDIT USERNAME
        const updateBtn = document.getElementById('updateBtn');
        const usernameInput = document.getElementById('usernameInput');
        const usernameDisplay = document.getElementById('usernameDisplay');

        // Set initial value of input to match display (without the emoji)
        document.addEventListener('DOMContentLoaded', function() {
            const displayText = usernameDisplay.textContent.trim();
            // Remove emoji if present (first character if it's an emoji)
            const emojiRegex = /^[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{1F700}-\u{1F77F}\u{1F780}-\u{1F7FF}\u{1F800}-\u{1F8FF}\u{1F900}-\u{1F9FF}\u{1FA00}-\u{1FA6F}\u{1FA70}-\u{1FAFF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{FE00}-\u{FE0F}]/u;
            const cleanUsername = emojiRegex.test(displayText.charAt(0)) 
                ? displayText.substring(1).trim() 
                : displayText;
            usernameInput.value = cleanUsername;
        });

        updateBtn.addEventListener('click', () => {
            if (usernameInput.disabled) {
                usernameInput.disabled = false;
                usernameInput.focus();
                updateBtn.textContent = 'SAVE';
            } else {
                const newUsername = usernameInput.value.trim();
                if (newUsername !== "") {
                    // Keep the emoji if it existed before
                    const displayText = usernameDisplay.textContent.trim();
                    const emojiRegex = /^[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{1F700}-\u{1F77F}\u{1F780}-\u{1F7FF}\u{1F800}-\u{1F8FF}\u{1F900}-\u{1F9FF}\u{1FA00}-\u{1FA6F}\u{1FA70}-\u{1FAFF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{FE00}-\u{FE0F}]/u;
                    const hadEmoji = emojiRegex.test(displayText.charAt(0));
                    
                    usernameDisplay.textContent = hadEmoji 
                        ? `${displayText.charAt(0)} ${newUsername}`
                        : newUsername;
                }
                usernameInput.disabled = true;
                updateBtn.textContent = 'CHANGE';
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
                });
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            const monthlyReportBtn = document.querySelector(".nav-btn");

            if (monthlyReportBtn) {
                monthlyReportBtn.addEventListener("click", function () {
                    window.location.href = "monthlyreport1.html";
                });
            }
        });
    