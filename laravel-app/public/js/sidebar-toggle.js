document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    const mainArea = document.querySelector('.main-area');
    const body = document.body;

    // Toggle Sidebar Collapse (Desktop)
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            if (window.innerWidth > 1024) {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            } else if (window.innerWidth > 768) {
                sidebar.classList.toggle('expanded');
            } else {
                sidebar.classList.toggle('open');
            }
        });
    }

    // Restore Sidebar State on Desktop
    if (window.innerWidth > 1024) {
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('collapsed');
        }
    }

    // Submenu Toggles
    const submenus = document.querySelectorAll('.has-submenu > .nav-link');
    submenus.forEach(link => {
        link.addEventListener('click', function(e) {
            if (sidebar.classList.contains('collapsed') && window.innerWidth > 1024) {
                return; // Don't toggle submenus when collapsed on desktop
            }
            e.preventDefault();
            const parent = this.parentElement;
            parent.classList.toggle('open');
            
            // Close other submenus (Optional accordion behavior)
            /*
            submenus.forEach(otherLink => {
                if (otherLink !== link) {
                    otherLink.parentElement.classList.remove('open');
                }
            });
            */
        });
    });

    // Mobile Overlay (Close sidebar when clicking outside)
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target) && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        }
    });

    // Active Item Marking (Simple script if not handled by Blade)
    const currentPath = window.location.pathname;
    const allLinks = document.querySelectorAll('.nav-link, .submenu-link');
    allLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
            // If it's a submenu link, open the parent
            const submenu = link.closest('.submenu');
            if (submenu) {
                submenu.parentElement.classList.add('open');
            }
        }
    });
});
