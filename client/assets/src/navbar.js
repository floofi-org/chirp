document.getElementById("navbar-placeholder").outerHTML = `
<header id="mobile-navbar" class="fella-nav fella-nav-no-border fella-nav-mobile">
    <nav id="mobile-navbar-inner" class="fella-nav-inner">
        <div id="mobile-navbar-inner-left" class="fella-nav-left">
            <div id="mobile-navbar-logo">
                <a href="/app" id="mobile-navbar-logo-link" class="fella-nav-icon-outer">
                    <img src="/assets/wordmark.png" alt="Floofi Voice Generator" id="mobile-navbar-logo-img" class="fella-nav-icon">
                </a>
            </div>
        </div>
        <div id="mobile-navbar-inner-right" class="fella-nav-right">
            <a class="fella-btn-round fella-btn fella-btn-secondary fella-nav-hamburger-container" id="mobile-navbar-hamburger-container" onclick="mobileNavbar();">
                <svg id="mobile-navbar-hamburger-closed" class="fella-nav-hamburger-closed" height="16" stroke-linejoin="round" style="color: currentcolor;" viewBox="0 0 16 16" width="16">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.75 4H1V5.5H1.75H14.25H15V4H14.25H1.75ZM1.75
                    10.5H1V12H1.75H14.25H15V10.5H14.25H1.75Z" fill="currentColor" />
                </svg>
                <svg id="mobile-navbar-hamburger-open" class="fella-nav-hamburger-open" height="16" stroke-linejoin="round" style="color: currentcolor;" viewBox="0 0 16 16" width="16">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12.4697 13.5303L13 14.0607L14.0607 13L13.5303
                    12.4697L9.06065 7.99999L13.5303 3.53032L14.0607 2.99999L13 1.93933L12.4697 2.46966L7.99999
                    6.93933L3.53032 2.46966L2.99999 1.93933L1.93933 2.99999L2.46966 3.53032L6.93933 7.99999L2.46966
                    12.4697L1.93933 13L2.99999 14.0607L3.53032 13.5303L7.99999 9.06065L12.4697 13.5303Z" fill="currentColor" />
                </svg>
            </a>
        </div>
    </nav>
</header>
<header id="navbar" class="fella-nav fella-nav-no-border fella-nav-desktop">
    <nav id="navbar-inner" class="fella-nav-inner">
        <div id="navbar-inner-left" class="fella-nav-left">
            <div id="navbar-category-outer" class="fella-nav-category-outer">
                <div id="navbar-category-indicator" class="fella-nav-category-indicator">
                    <svg id="navbar-category-indicator-icon" class="fella-nav-category-indicator-icon" fill="none" height="46" viewBox="0 0 158 46" width="158">
                        <path d="M70.952 36.8018L55.1106 20.9604C47.384 13.2338 43.5207 9.37047 39.0122 6.6077C35.0151
                        4.15823 30.6573 2.35317 26.0988 1.25878C20.9573 0.0244141 15.4938 0.0244141
                        4.56672 0.0244141H153.132C142.205 0.0244141 136.742 0.0244141 131.6 1.25878C127.042
                        2.35317 122.684 4.15823 118.687 6.6077C114.178 9.37047 110.315 13.2338 102.588
                        20.9604L86.7469 36.8018C82.3853 41.1635 75.3137 41.1635 70.952 36.8018Z" fill="#000" />

                        <path d="M153.132 5.60877C141.939 5.60877 137.253 5.64466 132.904 6.68885C128.915 7.64643
                        125.102 9.22586 121.605 11.3691C117.791 13.7062 114.452 16.9946 106.537 24.9091L90.6957
                        40.7506C84.1532 47.293 73.5457 47.293 67.0033 40.7505L51.1618 24.9091C43.2473 16.9946
                        39.9082 13.7062 36.0944 11.3691C32.5969 9.22586 28.7838 7.64643 24.7952 6.68885C20.4458
                        5.64466 15.7596 5.60877 4.56672 5.60877H0.668457V0.0244141H4.56672C15.4938 0.0244141
                        20.9573 0.0244141 26.0988 1.25878C30.6573 2.35317 35.0151 4.15823 39.0122 6.6077C43.5207
                        9.37047 47.384 13.2338 55.1106 20.9604L70.952 36.8018C75.3137 41.1635 82.3853 41.1635
                        86.7469 36.8018L102.588 20.9604C110.315 13.2338 114.178 9.37047 118.687 6.6077C122.684
                        4.15823 127.042 2.35317 131.6 1.25878C136.742 0.0244141 142.205 0.0244141 153.132
                        0.0244141H157.03V5.60877H153.132Z" fill="hsl(0, 0%, 18%)" /></svg>
                </div>
                <div id="navbar-category" class="fella-nav-category"></div>
            </div>
            <div id="navbar-logo">
                <a href="/" id="navbar-logo-link" class="fella-nav-icon-outer">
                    <img src="/assets/wordmark.png" alt="Floofi Voice Generator" id="navbar-logo-img" class="fella-nav-icon">
                </a>
            </div>
            <div id="navbar-tabs" class="fella-nav-tabs"></div>
            <ul id="navbar-navigation" class="fella-nav-items">
                <li class="fella-nav-navigation-item">
                    <a href="/" class="fella-nav-navigation-item-link">Generate</a>
                </li>
                <li class="fella-nav-navigation-item">
                    <a href="/terms" class="fella-nav-navigation-item-link">Policy</a>
                </li>
                <li class="fella-nav-navigation-item">
                    <a href="https://voice-api.floo.fi/docs/" class="fella-nav-navigation-item-link" target="_blank">API Docs</a>
                </li>
            </ul>
        </div>
        <div id="navbar-inner-right" class="fella-nav-right">
            <span id="version" class="fella-nav-subtitle">Version <span id="navbar-version"></span></span>
        </div>
    </nav>
</header>
`;

window.onscroll = () => {
    updateScroll();
}

function updateScroll() {
    if (window.scrollY === 0) {
        document.getElementById("navbar").classList.add("fella-nav-no-border");
    } else {
        document.getElementById("navbar").classList.remove("fella-nav-no-border");
    }
}

async function prepareNavbar() {
    document.getElementById("navbar-version").innerText = (await (await fetch("/version")).text()).trim();
    loadNavigation();
}

function loadNavigation() {
    window.categoryNavigationItem = null;
    window.categorySection = null;
    window.lastPosition = -1;

    Array.from(document.getElementsByClassName("fella-nav-navigation-item")).map(i => {
        i.onmouseenter = (e) => {
            if (document.getElementById("navbar").classList.contains("fella-nav-mobile-open")) return;
            if (e.target.getAttribute("data-category-id")) {
                let category = document.getElementById("navbar-category-base-" + e.target.getAttribute("data-category-id"));
                document.getElementById("navbar-category").style.width = category.clientWidth + "px";
                document.getElementById("navbar-category").style.height = category.clientHeight + "px";

                if (window.categoryNavigationItem) document.getElementById("navbar-category-outer").onmouseleave({
                    _switching: true
                });

                category.classList.remove("hide");
                category.classList.add("show");
                window.categorySection = category;
            } else {
                document.getElementById("navbar-category-outer").onmouseleave({});
            }

            document.getElementById("navbar-tabs").style.left = e.target.getBoundingClientRect().left + "px";
            document.getElementById("navbar-category-indicator").style.left = (e.target.getBoundingClientRect().left + e.target.clientWidth / 3) + "px";
            document.getElementById("navbar-tabs").style.width = e.target.clientWidth + "px";
            document.getElementById("navbar-tabs").style.opacity = "1";
            e.target.classList.add("open");
            if (window.categoryNavigationItem) {
                window.categoryNavigationItem.classList.remove("open");
            }
            window.categoryNavigationItem = e.target;
        };

        i.onmouseleave = (e) => {
            if (document.getElementById("navbar").classList.contains("fella-nav-mobile-open")) return;
            if (e.target.classList.contains("fella-nav-navigation-item-with-category")) return;
            e.target.classList.remove("open");
        }
    });

    document.getElementById("navbar-category-outer").onmouseleave = document.body.onmouseleave = (e) => {
        if (document.getElementById("navbar").classList.contains("fella-nav-mobile-open")) return;
        if (window.categoryNavigationItem) {
            window.categoryNavigationItem.classList.remove("open");
            window.categoryNavigationItem = null;
        }
        if (window.categorySection) {
            window.categorySection.classList.remove("show");
            Array.from(document.getElementsByClassName("fella-nav-category-base")).map(i => i.classList.remove("hide"));
            window.categorySection.classList.add("hide");
            window.categorySection = null;
        }
        if (!e._switching) document.getElementById("navbar-navigation").onmouseleave(null);
    }

    document.getElementById("navbar-inner-left").onmouseleave = () => {
        document.getElementById("navbar-tabs").style.transition = "opacity 150ms ease";
    }

    document.getElementById("navbar-navigation").onmousemove = (e) => {
        if (document.getElementById("navbar").classList.contains("fella-nav-mobile-open")) return;
        document.getElementById("navbar-category-outer").onmousemove(e);
        document.getElementById("navbar-category").style.transition = "width 150ms ease, height 150ms ease";
        document.getElementById("navbar-tabs").style.transition = "left 250ms ease, width 250ms ease, opacity 150ms ease";
        document.getElementById("navbar-category-indicator").style.transition = "left 250ms ease";
    };

    document.getElementById("navbar-navigation").onmouseleave = (e) => {
        if (document.getElementById("navbar").classList.contains("fella-nav-mobile-open")) return;
        if (window.categoryNavigationItem && window.categoryNavigationItem.classList.contains("fella-nav-navigation-item-with-category")) return;
        document.getElementById("navbar-tabs").style.opacity = "0";
        if (e) document.getElementById("navbar-tabs").style.transition = "opacity 150ms ease";
        document.getElementById("navbar-category").style.transition = "";
        document.getElementById("navbar-category-indicator").style.transition = "";
        document.getElementById("navbar-tabs").style.left = "0";
        document.getElementById("navbar-tabs").style.width = "0";
    };

    document.getElementById("navbar-category-outer").onmousemove = (e) => {
        document.getElementById("navbar-inner-left").classList.remove("navigating");
        if (document.getElementById("navbar").classList.contains("fella-nav-mobile-open")) return;
        if (window.lastPosition === -1) {
            window.lastPosition = e.clientX;
        }

        if (e.clientX - window.lastPosition < -2) {
            Array.from(document.getElementsByClassName("fella-nav-category-base")).map(i => i.classList.add("reverse"));
        } else if (e.clientX - window.lastPosition > 2) {
            Array.from(document.getElementsByClassName("fella-nav-category-base")).map(i => i.classList.remove("reverse"));
        }

        window.lastPosition = e.clientX;
    }

    document.getElementById("navbar-tabs").style.opacity = "0";
    document.getElementById("navbar-tabs").style.transition = "opacity 150ms ease";
    document.getElementById("navbar-tabs").style.left = "0";
    document.getElementById("navbar-tabs").style.width = "0";

    window.onresize = () => {
        if (window.innerWidth > 900) {
            document.getElementById("navbar").classList.remove("fella-nav-mobile-open");
        }
    }
}

function mobileNavbar() {
    if (document.getElementById("navbar").classList.contains("fella-nav-mobile-open")) {
        document.getElementById("navbar").classList.remove("fella-nav-mobile-open");
    } else {
        document.getElementById("navbar").classList.add("fella-nav-mobile-open");
    }
}
