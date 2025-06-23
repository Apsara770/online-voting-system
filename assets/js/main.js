document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const mobileMenu = document.getElementById('mobile-menu');
    const mainNav = document.querySelector('.main-nav ul');

    if (mobileMenu && mainNav) {
        mobileMenu.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
            mainNav.classList.toggle('active');
        });

        // Close mobile menu when a nav link is clicked
        mainNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if (mainNav.classList.contains('active')) { // Only close if mobile menu is open
                    mobileMenu.classList.remove('active');
                    mainNav.classList.remove('active');
                }
            });
        });
    }

    // FAQ Accordion
    const faqItems = document.querySelectorAll('.faq-item h3'); // Select the H3 as the clickable header

    faqItems.forEach(item => {
        item.addEventListener('click', function() {
            // Toggle 'active' class on the parent .faq-item
            const parentFaqItem = this.closest('.faq-item');
            parentFaqItem.classList.toggle('active');
        });
    });
});