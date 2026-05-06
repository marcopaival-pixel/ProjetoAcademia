document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    const mainArea = document.querySelector('.main-area');
    const body = document.body;

    /**
     * Mantém o item de menu ativo visível na área rolável (não volta ao topo após navegar).
     */
    function scrollSidebarActiveIntoView() {
        if (!sidebar) {
            return;
        }
        const content = sidebar.querySelector('.sidebar-content');
        if (!content) {
            return;
        }
        const active = sidebar.querySelector('.nav-link.active, .submenu-link.active');
        if (!active) {
            return;
        }
        const sub = active.closest('.submenu');
        if (sub) {
            const parentItem = sub.closest('.nav-item.has-submenu');
            if (parentItem) {
                parentItem.classList.add('open');
            }
        }
        const cRect = content.getBoundingClientRect();
        const aRect = active.getBoundingClientRect();
        const pad = 12;
        if (aRect.top < cRect.top + pad) {
            content.scrollTop -= (cRect.top + pad) - aRect.top;
        } else if (aRect.bottom > cRect.bottom - pad) {
            content.scrollTop += aRect.bottom - (cRect.bottom - pad);
        }
    }

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

    // Submenu Toggles (só intercepta âncoras que não navegam para outra página)
    const submenus = document.querySelectorAll('.has-submenu > .nav-link');
    submenus.forEach(link => {
        link.addEventListener('click', function(e) {
            if (sidebar.classList.contains('collapsed') && window.innerWidth > 1024) {
                return; // Don't toggle submenus when collapsed on desktop
            }
            const href = (this.getAttribute('href') || '').trim();
            const isToggleOnly = href === '' || href === '#' || href.toLowerCase().startsWith('javascript:');
            if (!isToggleOnly) {
                return; // deixar o browser seguir o link (ex.: upgrade / plano)
            }
            e.preventDefault();
            const parent = this.parentElement;
            parent.classList.toggle('open');
        });
    });

    // Mobile Overlay (Close sidebar when clicking outside)
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768 && sidebar && toggleBtn) {
            if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target) && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        }
    });

    // Destaque ativo: o layout principal define .active no servidor (MenuService). Só complementar se não houver nenhum.
    const serverMarkedActive = sidebar && sidebar.querySelector('.nav-link.active, .submenu-link.active');
    if (!serverMarkedActive) {
        const currentPath = window.location.pathname.replace(/\/$/, '') || '/';
        const allLinks = document.querySelectorAll('#sidebar .nav-link[href], #sidebar .submenu-link[href]');
        allLinks.forEach(link => {
            const href = (link.getAttribute('href') || '').trim();
            if (!href || href === '#' || href.toLowerCase().startsWith('javascript:')) {
                return;
            }
            let path;
            try {
                path = new URL(href, window.location.origin).pathname.replace(/\/$/, '') || '/';
            } catch (e) {
                return;
            }
            if (path === currentPath) {
                link.classList.add('active');
                const submenu = link.closest('.submenu');
                if (submenu && submenu.parentElement && submenu.parentElement.classList.contains('has-submenu')) {
                    submenu.parentElement.classList.add('open');
                }
            }
        });
    }

    // Após layout (estado colapsado, submenus), posicionar scroll no item ativo
    requestAnimationFrame(function() {
        requestAnimationFrame(scrollSidebarActiveIntoView);
    });
});
