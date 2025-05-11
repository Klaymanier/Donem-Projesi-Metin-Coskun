// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileToggle = document.querySelector('.mobile-toggle');
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.main-content').classList.toggle('active');
        });
    }

    // Aktif menü öğesini ayarla
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    const menuItems = document.querySelectorAll('.menu-item');
    
    menuItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href === currentPage || 
            (currentPage === 'index.html' && href === '#') ||
            (href && currentPage.includes(href))) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });

    // Ürün filtreleme işlemi
    const productSearchInput = document.getElementById('productSearch');
    if (productSearchInput) {
        productSearchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const productItems = document.querySelectorAll('.product-card');
            
            productItems.forEach(item => {
                const productName = item.querySelector('.product-title').textContent.toLowerCase();
                const productCategory = item.querySelector('.product-category').textContent.toLowerCase();
                
                if (productName.includes(searchTerm) || productCategory.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Sipariş filtreleme işlemi
    const orderSearchInput = document.getElementById('orderSearch');
    if (orderSearchInput) {
        orderSearchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const orderRows = document.querySelectorAll('.custom-table tbody tr');
            
            orderRows.forEach(row => {
                const orderId = row.cells[0].textContent.toLowerCase();
                const customerName = row.cells[1].textContent.toLowerCase();
                
                if (orderId.includes(searchTerm) || customerName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Sipariş durumu filtreleme
    const statusFilters = document.querySelectorAll('.status-filter');
    if (statusFilters.length > 0) {
        statusFilters.forEach(filter => {
            filter.addEventListener('click', function() {
                const status = this.getAttribute('data-status');
                const orderRows = document.querySelectorAll('.custom-table tbody tr');
                
                statusFilters.forEach(f => f.classList.remove('active'));
                this.classList.add('active');
                
                orderRows.forEach(row => {
                    if (status === 'all') {
                        row.style.display = '';
                    } else {
                        const orderStatus = row.querySelector('.status').classList[1];
                        if (orderStatus === status) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            });
        });
    }

    // Form gönderimlerini engelle (demo)
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Bu işlem demo sürümünde gerçekleştirilmez.');
        });
    });

    // Ürün silme işlemi onayı
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Bu ürünü silmek istediğinizden emin misiniz?')) {
                alert('Ürün silme işlemi demo sürümünde gerçekleştirilmez.');
            }
        });
    });
});