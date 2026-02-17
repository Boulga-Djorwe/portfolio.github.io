document.addEventListener("DOMContentLoaded", () => {
    const navbar = document.getElementById("navbar");
    const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    const onScroll = () => {
        if (!navbar) return;
        if (window.scrollY > 64) {
            navbar.classList.add("scrolled");
        } else {
            navbar.classList.remove("scrolled");
        }
    };
    window.addEventListener("scroll", onScroll, { passive: true });
    onScroll();

    const navLinks = document.querySelectorAll('.nav-link[href^="#"]');
    navLinks.forEach((link) => {
        link.addEventListener("click", (e) => {
            const targetId = link.getAttribute("href");
            const targetSection = targetId ? document.querySelector(targetId) : null;
            if (!targetSection) return;

            e.preventDefault();
            const offset = 82;
            const top = targetSection.getBoundingClientRect().top + window.scrollY - offset;
            window.scrollTo({ top, behavior: prefersReducedMotion ? "auto" : "smooth" });
        });
    });

    const navbarCollapse = document.querySelector(".navbar-collapse");
    if (navbarCollapse) {
        navbarCollapse.querySelectorAll(".nav-link").forEach((link) => {
            link.addEventListener("click", () => navbarCollapse.classList.remove("show"));
        });
    }

    const animateIn = (selector, setup) => {
        const items = document.querySelectorAll(selector);
        if (!items.length) return;

        if (prefersReducedMotion || !("IntersectionObserver" in window)) {
            items.forEach((item) => setup(item, true));
            return;
        }

        const observer = new IntersectionObserver(
            (entries, obs) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    setup(entry.target, true);
                    obs.unobserve(entry.target);
                });
            },
            { threshold: 0.2, rootMargin: "0px 0px -10% 0px" }
        );

        items.forEach((item) => {
            setup(item, false);
            observer.observe(item);
        });
    };

    animateIn(".skill-progress", (el, visible) => {
        if (!visible) {
            el.style.width = "0";
            return;
        }
        const width = el.getAttribute("data-width") || "0";
        el.style.setProperty("--target-width", width);
        el.classList.add("animate");
        el.style.width = width;
    });

    animateIn(".project-card", (el, visible) => {
        el.style.opacity = visible ? "1" : "0";
        el.style.transform = visible ? "translateY(0)" : "translateY(14px)";
        el.style.transition = "opacity 420ms ease, transform 420ms ease";
    });

    animateIn(".timeline-item", (el, visible) => {
        el.style.opacity = visible ? "1" : "0";
        el.style.transform = visible ? "translateX(0)" : "translateX(-14px)";
        el.style.transition = "opacity 480ms ease, transform 480ms ease";
    });

    const contactForm = document.getElementById("contactForm");
    if (contactForm) {
        contactForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            if (!submitBtn) return;

            const initialText = submitBtn.textContent;
            submitBtn.textContent = "Envoi en cours...";
            submitBtn.disabled = true;

            try {
                const endpoint = contactForm.action
                    ? new URL(contactForm.action, window.location.href).href
                    : new URL("contact.php", window.location.href).href;

                const response = await fetch(endpoint, {
                    method: "POST",
                    body: new FormData(contactForm)
                });

                const contentType = response.headers.get("content-type") || "";
                const data = contentType.includes("application/json")
                    ? await response.json()
                    : { success: false, message: `Reponse inattendue (HTTP ${response.status})` };

                if (data.success) {
                    submitBtn.textContent = "Message envoye !";
                    submitBtn.classList.remove("btn-primary", "btn-danger");
                    submitBtn.classList.add("btn-success");
                    contactForm.reset();
                } else {
                    submitBtn.textContent = "Erreur - Reessayer";
                    submitBtn.classList.remove("btn-primary", "btn-success");
                    submitBtn.classList.add("btn-danger");
                    alert(data.message || "Une erreur est survenue.");
                }
            } catch (_error) {
                submitBtn.textContent = "Erreur - Reessayer";
                submitBtn.classList.remove("btn-primary", "btn-success");
                submitBtn.classList.add("btn-danger");
                alert("Impossible d'envoyer le message pour le moment.");
            } finally {
                setTimeout(() => {
                    submitBtn.textContent = initialText;
                    submitBtn.disabled = false;
                    submitBtn.classList.remove("btn-success", "btn-danger");
                    submitBtn.classList.add("btn-primary");
                }, 2200);
            }
        });
    }
});
