/* =======================
   Landing - Navigation
   ======================= */

document.addEventListener("DOMContentLoaded", function () {
    const navigation = document.getElementById("landingNavigation");
    const navLinks = document.querySelectorAll(".landingNavLink");
    const sections = document.querySelectorAll(".landingSection");

    if (!navigation || !navLinks.length || !sections.length) {
        return;
    }

    // Update Active Navigation
    function setActiveNavigation(sectionId) {
        navLinks.forEach(function (link) {
            const isActive = link.getAttribute("href") === "#" + sectionId;

            link.classList.toggle("active", isActive);

            if (isActive) {
                link.setAttribute("aria-current", "location");
            } else {
                link.removeAttribute("aria-current");
            }
        });
    }

    // Detect Visible Section
    const sectionObserver = new IntersectionObserver(
        function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    setActiveNavigation(entry.target.id);
                }
            });
        },
        {
            root: null,
            rootMargin: "-35% 0px -35% 0px",
            threshold: 0,
        },
    );

    sections.forEach(function (section) {
        sectionObserver.observe(section);
    });

    // Navigation Click
    navLinks.forEach(function (link) {
        link.addEventListener("click", function () {
            const sectionId = link.getAttribute("href").substring(1);
            setActiveNavigation(sectionId);
        });
    });

    // Set Initial Active Section
    function updateInitialNavigation() {
        const viewportCenter = window.innerHeight / 2;
        let closestSection = null;
        let closestDistance = Infinity;

        sections.forEach(function (section) {
            const rect = section.getBoundingClientRect();
            const sectionCenter = rect.top + rect.height / 2;
            const distance = Math.abs(sectionCenter - viewportCenter);

            if (distance < closestDistance) {
                closestDistance = distance;
                closestSection = section;
            }
        });

        if (closestSection) {
            setActiveNavigation(closestSection.id);
        }
    }

    updateInitialNavigation();
});

/* =======================
   Landing - Scroll Reveal Animation
   ======================= */

document.addEventListener("DOMContentLoaded", function () {
    const revealElements = [
        {
            selector: "#landingHeroContent",
            animation: "landingRevealUp",
        },
        {
            selector: "#landingAboutImageContainer",
            animation: "landingRevealLeft",
        },
        {
            selector: "#landingAboutInformation",
            animation: "landingRevealRight",
        },
        {
            selector: "#landingPosSystemContent",
            animation: "landingRevealZoom",
        },
        {
            selector: "#landingPosLaptopImage",
            animation: "landingRevealUp",
        },
        {
            selector: "#landingPosFeatures",
            animation: "landingRevealUp",
        },
        {
            selector: "#landingTeamHeader",
            animation: "landingRevealUp",
        },
        {
            selector: ".landingTeamCard",
            animation: "landingRevealUp",
        },
    ];

    const elements = [];

    // Prepare Animation Elements
    revealElements.forEach(function (item) {
        const matches = document.querySelectorAll(item.selector);

        matches.forEach(function (element, index) {
            element.classList.add("landingReveal", item.animation);

            // Stagger Team Cards
            if (item.selector === ".landingTeamCard") {
                element.style.setProperty(
                    "--landingRevealDelay",
                    index * 0.12 + "s",
                );
            }

            elements.push(element);
        });
    });

    // Respect Reduced Motion Preferences
    const prefersReducedMotion = window.matchMedia(
        "(prefers-reduced-motion: reduce)",
    ).matches;

    if (prefersReducedMotion || !("IntersectionObserver" in window)) {
        elements.forEach(function (element) {
            element.classList.add("landingRevealVisible");
        });
        return;
    }

    // Observe Elements While Scrolling
    const revealObserver = new IntersectionObserver(
        function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add("landingRevealVisible");

                    // Animate Only Once
                    revealObserver.unobserve(entry.target);
                }
            });
        },
        {
            root: null,
            threshold: 0.12,
            rootMargin: "0px 0px -5% 0px",
        },
    );

    elements.forEach(function (element) {
        revealObserver.observe(element);
    });
});
