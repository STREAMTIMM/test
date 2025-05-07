let selectedAvatar = null;

function selectAvatar(avatar) {
    if (selectedAvatar === avatar) {
        avatar.classList.remove('selected');
        selectedAvatar = null;
        document.getElementById('confirmBtn').disabled = true;
        return;
    }

    if (selectedAvatar) {
        selectedAvatar.classList.remove('selected');
    }

    avatar.classList.add('selected');
    selectedAvatar = avatar;

    document.getElementById('confirmBtn').disabled = false;
}

function confirmSelection() {
    if (!selectedAvatar) {
        alert('Please select an avatar!');
        return;
    }
    submitDataToDatabase();
}

function submitDataToDatabase() {
    const style = window.getComputedStyle(selectedAvatar);
    const backgroundImage = style.getPropertyValue('background-image');
    const url = backgroundImage.slice(5, -2); // Removes 'url("...' and '")'
    const filename = url.substring(url.lastIndexOf('/') + 1);

    axios.post('../server/avatar.php', {
        avatar: filename
    })
        .then(function (response) {
            if (response.data.success) {
                alert('Avatar updated successfully!');
                window.location.href = '../'; // 🔁 Redirect
            } else {
                alert('Failed to update avatar: ' + response.data.message);
            }
        })
        .catch(function (error) {
            console.error('Error submitting data:', error);
            alert('An error occurred while submitting data.');
        });
}

