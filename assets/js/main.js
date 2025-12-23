// Main JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Format currency
    formatCurrency();
});

// Format currency display
function formatCurrency() {
    const elements = document.querySelectorAll('[data-currency]');
    elements.forEach(el => {
        const value = parseFloat(el.dataset.currency);
        el.textContent = new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(value);
    });
}

// Add to cart with confirmation
function addToCart(productId, productName) {
    if (confirm(`Thêm "${productName}" vào giỏ hàng?`)) {
        document.getElementById('addToCartForm').submit();
    }
}

// Confirm delete
function confirmDelete(message = 'Bạn chắc chắn muốn xóa?') {
    return confirm(message);
}

// Show loading spinner
function showLoading() {
    const spinner = document.createElement('div');
    spinner.className = 'spinner position-fixed top-50 start-50 translate-middle';
    spinner.id = 'loadingSpinner';
    document.body.appendChild(spinner);
}

// Hide loading spinner
function hideLoading() {
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) {
        spinner.remove();
    }
}

// Validate form
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
    }
    form.classList.add('was-validated');
}

// Format date to Vietnamese format
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN');
}

// Format time
function formatTime(timeString) {
    const time = new Date(`2000-01-01 ${timeString}`);
    return time.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
}
