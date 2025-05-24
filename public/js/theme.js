document.addEventListener('DOMContentLoaded', function () {
  const theme = localStorage.getItem('theme');
  const icon = document.querySelector('#theme-toggle i');

  if (theme === 'dark') {
    document.body.classList.add('dark-mode');
    if (icon) {
      icon.classList.remove('fa-moon');
      icon.classList.add('fa-sun');
    }
  }
});

function toggleDarkMode() {
  const body = document.body;
  const icon = document.querySelector('#theme-toggle i');

  body.classList.toggle('dark-mode');

  const isDark = body.classList.contains('dark-mode');
  localStorage.setItem('theme', isDark ? 'dark' : 'light');

  if (icon) {
    if (isDark) {
      icon.classList.remove('fa-moon');
      icon.classList.add('fa-sun');
    } else {
      icon.classList.remove('fa-sun');
      icon.classList.add('fa-moon');
    }
  }
}
