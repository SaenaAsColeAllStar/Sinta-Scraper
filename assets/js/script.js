// =============================================
// Sistem Monitoring Publikasi Dosen LPPM
// Custom JavaScript
// =============================================

$(document).ready(function() {
    // Mobile sidebar toggle
    $(document).on('click', '#sidebarToggle', function() {
        $('.sidebar').toggleClass('active');
    });

    // Auto-hide alerts after 4 seconds
    setTimeout(function() {
        $('.alert-dismissible').fadeOut(400);
    }, 4000);

    // Tooltip init
    $('[data-bs-toggle="tooltip"]').tooltip();
});

// Show loading overlay
function showLoading() {
    $('.loading-overlay').addClass('active');
}

// Hide loading overlay
function hideLoading() {
    $('.loading-overlay').removeClass('active');
}

// Show toast notification
function showToast(message, type = 'success') {
    const bgClass = type === 'success' ? 'bg-success' : (type === 'danger' ? 'bg-danger' : 'bg-info');
    const iconClass = type === 'success' ? 'bi-check-circle-fill' : (type === 'danger' ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill');
    
    const toastHtml = `
        <div class="toast show align-items-center text-white ${bgClass} border-0 mb-2" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${iconClass} me-2"></i>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    let container = $('.toast-container');
    if (container.length === 0) {
        $('body').append('<div class="toast-container"></div>');
        container = $('.toast-container');
    }
    
    const $toast = $(toastHtml);
    container.append($toast);
    
    setTimeout(function() {
        $toast.fadeOut(400, function() { $(this).remove(); });
    }, 3500);
}

// AJAX Pagination - generic loader
function loadPagination(url, containerId, page, perPage, dosenId) {
    showLoading();
    $.ajax({
        url: url,
        type: 'GET',
        data: {
            page: page,
            per_page: perPage,
            dosen_id: dosenId
        },
        success: function(response) {
            $('#' + containerId).html(response);
            hideLoading();
        },
        error: function() {
            hideLoading();
            showToast('Gagal memuat data. Silakan coba lagi.', 'danger');
        }
    });
}

// Confirm delete
function confirmDelete(url, itemName) {
    if (confirm('Apakah Anda yakin ingin menghapus ' + itemName + '?')) {
        window.location.href = url;
    }
}

// Format number with separator
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
