const passwordInput = document.getElementById('password');
const constraints = {
  length: document.getElementById('length'),
  uppercase: document.getElementById('uppercase'),
  number: document.getElementById('number'),
  special: document.getElementById('special')
};

passwordInput.addEventListener('input', () => {
  const value = passwordInput.value;
  constraints.length.className = value.length >= 6 ? 'valid' : 'invalid';
  constraints.uppercase.className = /[A-Z]/.test(value) ? 'valid' : 'invalid';
  constraints.number.className = /\d/.test(value) ? 'valid' : 'invalid';
  constraints.special.className = /[!@#$%^&*(),.?":{}|<>]/.test(value) ? 'valid' : 'invalid';
});

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

function clearForm() {
  document.getElementById('signup-form').reset();
  for (let key in constraints) constraints[key].className = 'invalid';
}

document.getElementById('signup-form').addEventListener('submit', function (e) {
  e.preventDefault();

  const email = document.getElementById('email').value.trim();
  const pass = document.getElementById('password').value;
  const confirm = document.getElementById('confirm-password')?.value; 

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    alert('Please enter a valid email address.');
    return;
  }

  if (document.querySelectorAll('.valid').length < 4) {
    alert('Please meet all password requirements.');
    return;
  }

  if (confirm !== undefined && pass !== confirm) {
    alert('Passwords do not match.');
    return;
  }


});
