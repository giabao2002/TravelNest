document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize date picker if available
    if(document.querySelector('.datepicker')) {
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });
    }
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if(targetId === "#") return;
            
            const targetElement = document.querySelector(targetId);
            if(targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 70,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Counter animation for statistics section
    const counters = document.querySelectorAll('.counter');
    const speed = 200;
    
    function runCounter() {
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText;
            const increment = target / speed;
            
            if (count < target) {
                counter.innerText = Math.ceil(count + increment);
                setTimeout(runCounter, 1);
            } else {
                counter.innerText = target;
            }
        });
    }
    
    // Start counter animation when section is in viewport
    const statsSection = document.querySelector('.stats-section');
    if(statsSection) {
        const observer = new IntersectionObserver((entries) => {
            if(entries[0].isIntersecting) {
                runCounter();
                observer.unobserve(statsSection);
            }
        }, { threshold: 0.5 });
        
        observer.observe(statsSection);
    }
    
    // Testimonial carousel auto-slide if exists
    if(document.querySelector('#testimonialCarousel')) {
        const myCarousel = document.querySelector('#testimonialCarousel');
        const carousel = new bootstrap.Carousel(myCarousel, {
            interval: 5000,
            touch: true
        });
    }
    
    // Back to top button
    const backToTopBtn = document.getElementById('back-to-top');
    if(backToTopBtn) {
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        });
        
        backToTopBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // Mobile menu behavior
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    if(navbarToggler && navbarCollapse) {
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!navbarCollapse.contains(event.target) && !navbarToggler.contains(event.target) && navbarCollapse.classList.contains('show')) {
                navbarToggler.click();
            }
        });
        
        // Close mobile menu when clicking on a nav link
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if(navbarCollapse.classList.contains('show')) {
                    navbarToggler.click();
                }
            });
        });
    }
    
    // Search form submission
    const searchForm = document.querySelector('#search-form');
    if(searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const locationInput = document.querySelector('#search-location').value;
            const dateInput = document.querySelector('#search-date').value;
            
            if(!locationInput && !dateInput) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Vui lòng nhập điểm đến hoặc ngày khởi hành!',
                    confirmButtonColor: '#FF6B6B'
                });
                return;
            }
            
            // Here you would normally handle the search submission
            // For demo purposes, we'll show a success message
            Swal.fire({
                icon: 'success',
                title: 'Đang tìm kiếm...',
                text: 'Đang tìm kiếm tour phù hợp cho bạn!',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                // Redirect to search results page
                window.location.href = 'tours.php?location=' + encodeURIComponent(locationInput) + '&date=' + encodeURIComponent(dateInput);
            });
        });
    }
}); 