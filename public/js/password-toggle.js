function togglePassword() {
    const passwordInput = document.getElementById('password');
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text'; // show password
    } else {
      passwordInput.type = 'password'; // hide password
    }
  }