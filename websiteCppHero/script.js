let isEnglish = true;

function toggleLanguage() {
    isEnglish = !isEnglish;
    const langBtn = document.getElementById('lang-switch');
    langBtn.textContent = isEnglish ? 'RO' : 'EN';
    
    const elements = document.querySelectorAll('.translate');
    elements.forEach(element => {
        const text = isEnglish ? element.getAttribute('data-en') : element.getAttribute('data-ro');
        element.textContent = text;
    });
}

// Contact modal functionality
const modal = document.getElementById('contact-modal');
const contactBtn = document.querySelector('.contact-btn');
const closeBtn = document.querySelector('.close-modal');

contactBtn.addEventListener('click', () => {
    modal.style.display = 'block';
});

closeBtn.addEventListener('click', () => {
    modal.style.display = 'none';
});

window.addEventListener('click', (e) => {
    if (e.target === modal) {
        modal.style.display = 'none';
    }
});

// Contact form submission
const contactForm = document.getElementById('contact-form');
contactForm.addEventListener('submit', (e) => {
    e.preventDefault();
    alert('Thank you for your message! We will get back to you soon.');
    modal.style.display = 'none';
    contactForm.reset();
});

// Add smooth scrolling for better user experience
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Simple animation for download button
const downloadBtn = document.querySelector('.download-btn');
if (downloadBtn) {
    downloadBtn.addEventListener('click', () => {
        alert('Download starting... (Demo only)');
    });
}

// Animate features on scroll
const featureCards = document.querySelectorAll('.feature-card');
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, { threshold: 0.1 });

featureCards.forEach(card => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';
    card.style.transition = 'opacity 0.5s, transform 0.5s';
    observer.observe(card);
});