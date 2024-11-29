document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggle-btn');
    const body = document.body;

    toggleBtn.addEventListener("click", function () {
        sidebar.classList.toggle("expand");
        body.classList.toggle("sidebar-expanded");
    });

    function handleResponsive() {
        if (window.innerWidth <= 768) {
            sidebar.classList.remove("expand");
            body.classList.remove("sidebar-expanded");
        }
    }

    window.addEventListener('resize', handleResponsive);
    handleResponsive();
});