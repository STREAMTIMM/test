
// Password toggle function remains exactly the same
function togglePassword(id, toggleElement) {
    const input = document.getElementById(id);
    const showEye = toggleElement.querySelector('.show-eye');
    const hideEye = toggleElement.querySelector('.hide-eye');
  
    if (input.type === 'password') {
        input.type = 'text';
        showEye.style.display = 'none';
        hideEye.style.display = 'inline';
    } else {
        input.type = 'password';
        showEye.style.display = 'inline';
        hideEye.style.display = 'none';
    }
}