document.addEventListener('DOMContentLoaded', function() {
    const menuButton = document.getElementById('menuButton');
    const menuItems = document.querySelectorAll('.menu li.menu-item');

    menuButton.addEventListener('click', function() {
        menuItems.forEach(item => {
            item.classList.toggle('show');
        });
    });
});
