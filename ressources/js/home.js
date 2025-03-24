document.addEventListener('DOMContentLoaded', function() {
    // Gestion du défilement fluide pour les liens
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Lazy loading des images
    const lazyImages = document.querySelectorAll('.film-poster img');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                observer.unobserve(img);
            }
        });
    });

    lazyImages.forEach(img => {
        img.classList.add('lazy');
        img.dataset.src = img.src;
        img.src = '';
        imageObserver.observe(img);
    });

    // Animation au défilement pour les sections
    const sections = document.querySelectorAll('.films-section');
    const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });

    sections.forEach(section => {
        section.classList.add('fade-in');
        sectionObserver.observe(section);
    });

    // Gestion de la bannière héro
    const heroBanner = document.querySelector('.hero-banner');
    if (heroBanner) {
        // Parallax effect
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            heroBanner.style.backgroundPositionY = `${scrolled * 0.5}px`;
        });

        // Preload de l'image de fond
        const bgImg = new Image();
        bgImg.src = heroBanner.style.backgroundImage.slice(4, -1).replace(/"/g, "");
    }

    // Gestion des films par genre
    const genreBlocks = document.querySelectorAll('.genre-block');
    genreBlocks.forEach(block => {
        const slider = block.querySelector('.films-slider');
        let isDown = false;
        let startX;
        let scrollLeft;

        slider.addEventListener('mousedown', (e) => {
            isDown = true;
            slider.classList.add('active');
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        });

        slider.addEventListener('mouseleave', () => {
            isDown = false;
            slider.classList.remove('active');
        });

        slider.addEventListener('mouseup', () => {
            isDown = false;
            slider.classList.remove('active');
        });

        slider.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 2;
            slider.scrollLeft = scrollLeft - walk;
        });
    });

    // Gestion des favoris
    document.querySelectorAll('.btn-favorite').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const filmId = this.dataset.filmId;
            
            try {
                const response = await fetch(`${URL}films/watchlist/toggle/${filmId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.classList.toggle('active');
                    this.querySelector('i').classList.toggle('fas');
                    this.querySelector('i').classList.toggle('far');
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        });
    });
});
