const navLinks = document.getElementById('nav-links');
const navMenuButton = document.getElementById('nav-menu-button');
const header = document.querySelector('header');
const nav = document.querySelector('nav');

if (navMenuButton && navLinks) {
    navMenuButton.addEventListener('click', () => {
        navLinks.classList.toggle('show');

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

        if (header) {
            header.style.paddingTop = '100px';
        }
    }
});

document.addEventListener("DOMContentLoaded", () => {
    const navMenuProfile = document.getElementById("nav-menu-profile");
    const navButton = document.getElementById("register-button");
    const navProfile = document.getElementById("profile-button");
    const navProfileName = document.getElementById("profile-name");
    const isLoggedIn = typeof userId !== "undefined" && Boolean(userId);
    const currentPath = window.location.pathname.replace(/\/+$/, "") || "/";
    const isProfilePage = currentPath === "/profile" || currentPath === "/files/profile.php" || currentPath === "/files/profile";
    const existingProfileName = navProfileName ? navProfileName.textContent.trim() : "";
    const fullName = (typeof userFullName !== "undefined" ? String(userFullName).trim() : "") || existingProfileName;
    const openProfile = () => {
        window.location.href = "/profile";
    };
    const openRegister = () => {
        window.location.href = "/register";
    };
    const makeKeyboardLink = (element, handler) => {
        if (!element) {
            return;
        }

        element.setAttribute("role", "button");
        element.setAttribute("tabindex", "0");
        element.addEventListener("keydown", (event) => {
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                handler();
            }
        });
    };

    if (isLoggedIn) {
        if (navProfile) {
            navProfile.removeAttribute("hidden");
            navProfile.setAttribute("aria-label", "Open profile");
            navProfile.addEventListener("click", openProfile);
        }
        if (navMenuProfile) {
            navMenuProfile.addEventListener("click", openProfile);
        }
        if (navButton || isProfilePage) {
            navButton?.setAttribute("hidden", "hidden");
        }
        if (navProfileName && !isProfilePage) {
            navProfileName.textContent = fullName || "PROFILE";
            navProfileName.removeAttribute("hidden");
            navProfileName.addEventListener("click", openProfile);
            makeKeyboardLink(navProfileName, openProfile);
        }
    } else {
        if (navProfile) {
            navProfile.setAttribute("hidden", "hidden");
        }
        if (navProfileName) {
            navProfileName.setAttribute("hidden", "hidden");
        }
        if (navButton) {
            navButton.removeAttribute("hidden");
            navButton.addEventListener("click", openRegister);
        }
        if (navMenuProfile) {
            navMenuProfile.addEventListener("click", openRegister);
        }
    }
});
