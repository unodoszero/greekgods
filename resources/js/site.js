const navLinks = document.getElementById('nav-links');
const navMenuButton = document.getElementById('nav-menu-button');
const header = document.querySelector('header');
const nav = document.querySelector('nav');

if (navMenuButton && navLinks) {
    navMenuButton.addEventListener('click', () => {
        navLinks.classList.toggle('show');
        navMenuButton.setAttribute('aria-expanded', navLinks.classList.contains('show') ? 'true' : 'false');

        if (header && nav) {
            if (navLinks.classList.contains('show')) {
                const totalNavHeight = nav.offsetHeight + navLinks.offsetHeight;
                header.style.paddingTop = `${totalNavHeight}px`;
            } else {
                header.style.paddingTop = '100px';
            }
        }
    });
}

window.addEventListener('resize', () => {
    if (window.innerWidth > 980) {
        navLinks?.classList.remove('show');
        navMenuButton?.setAttribute('aria-expanded', 'false');

        if (header) {
            header.style.paddingTop = '100px';
        }
    }
});
