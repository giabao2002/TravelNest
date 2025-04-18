$(document).ready(function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    const popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    $(document).on('click', '#sidebarToggle', function(e) {
        console.log("Sidebar toggle clicked");
        e.preventDefault();
        e.stopPropagation();
        $('#sidebar').toggleClass('collapsed show');
        $('#content-wrapper').toggleClass('expanded');
        $('.navbar-admin').toggleClass('expanded');
        return false;
    });
    
    $('#closeSidebar').on('click', function() {
        $('#sidebar').removeClass('show');
    });
    
    $(document).on('click', function(e) {
        if ($(window).width() < 768) {
            if (!$(e.target).closest('#sidebar').length && !$(e.target).closest('#sidebarToggle').length) {
                $('#sidebar').removeClass('show');
            }
        }
    });
    
    $(window).resize(function() {
        if ($(window).width() >= 768) {
            $('#sidebar').removeClass('show');
        }
    });
    
    const backToTopButton = $('#back-to-top');
    if (backToTopButton.length) {
        $(window).scroll(function() {
            if ($(this).scrollTop() > 300) {
                backToTopButton.addClass('show');
            } else {
                backToTopButton.removeClass('show');
            }
        });
        
        backToTopButton.on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({scrollTop: 0}, 300);
        });
    }
    
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        const itemId = $(this).data('id');
        const itemType = $(this).data('type');
        
        Swal.fire({
            title: 'Xác nhận xóa?',
            text: `Bạn có chắc chắn muốn xóa ${itemType} này không?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Có, xóa ngay!',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `delete.php?type=${itemType}&id=${itemId}`;
            }
        });
    });
    
    $('.status-change').on('change', function() {
        const itemId = $(this).data('id');
        const itemType = $(this).data('type');
        const newStatus = $(this).val();
        
        Swal.fire({
            title: 'Xác nhận thay đổi?',
            text: `Bạn có chắc chắn muốn thay đổi trạng thái của ${itemType} này không?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Có, thay đổi!',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'update_status.php',
                    type: 'POST',
                    data: {
                        id: itemId,
                        type: itemType,
                        status: newStatus
                    },
                    success: function(response) {
                        const data = JSON.parse(response);
                        if (data.success) {
                            Swal.fire({
                                title: 'Thành công!',
                                text: data.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                title: 'Lỗi!',
                                text: data.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Lỗi!',
                            text: 'Đã xảy ra lỗi khi cập nhật trạng thái.',
                            icon: 'error'
                        });
                    }
                });
            } else {
                $(this).val($(this).data('original'));
            }
        });
    }).each(function() {
        $(this).data('original', $(this).val());
    });
    
    function animateCounter(element, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const value = Math.floor(progress * (end - start) + start);
            element.textContent = value.toLocaleString();
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }
    
    document.querySelectorAll('.counter-value').forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'), 10);
        animateCounter(counter, 0, target, 1500);
    });
}); 