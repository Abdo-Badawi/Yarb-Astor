/**
 * HomeStay Admin Dashboard Custom JavaScript
 */

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Initialize popovers
    $('[data-toggle="popover"]').popover();
    
    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobile-collapse');
    const navbar = document.querySelector('.pcoded-navbar');
    const body = document.body;
    
    // Toggle mobile menu
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            navbar.classList.toggle('mob-menu');
            body.classList.toggle('mob-menu-open');
            
            // Update aria-expanded attribute for accessibility
            const isExpanded = navbar.classList.contains('mob-menu');
            mobileMenuBtn.setAttribute('aria-expanded', isExpanded);
            
            // Log for debugging
            console.log('Mobile menu toggled:', isExpanded);
        });
    }
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        if (navbar.classList.contains('mob-menu') && 
            !navbar.contains(e.target) && 
            !mobileMenuBtn.contains(e.target)) {
            navbar.classList.remove('mob-menu');
            body.classList.remove('mob-menu-open');
            mobileMenuBtn.setAttribute('aria-expanded', 'false');
        }
    });
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth > 992) {
                navbar.classList.remove('mob-menu');
                body.classList.remove('mob-menu-open');
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
            }
        }, 250);
    });
    
    // Add touch support for mobile devices
    let touchStartX = 0;
    let touchEndX = 0;
    
    document.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    }, false);
    
    document.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, false);
    
    function handleSwipe() {
        const swipeThreshold = 50;
        const swipeDistance = touchEndX - touchStartX;
        
        if (Math.abs(swipeDistance) > swipeThreshold) {
            if (swipeDistance > 0 && !navbar.classList.contains('mob-menu')) {
                // Swipe right - open menu
                navbar.classList.add('mob-menu');
                body.classList.add('mob-menu-open');
                mobileMenuBtn.setAttribute('aria-expanded', 'true');
            } else if (swipeDistance < 0 && navbar.classList.contains('mob-menu')) {
                // Swipe left - close menu
                navbar.classList.remove('mob-menu');
                body.classList.remove('mob-menu-open');
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
            }
        }
    }
    
    // User dropdown toggle
    $('#more-details').on('click', function() {
        $('#nav-user-link').slideToggle();
    });
    
    // Search bar toggle
    $('.pop-search').on('click', function() {
        $('.search-bar').slideToggle();
    });
    
    // Close search bar when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.search-bar, .pop-search').length) {
            $('.search-bar').slideUp();
        }
    });
    
    // Notification dropdown toggle
    $('.dropdown-toggle').on('click', function(e) {
        e.preventDefault();
        $(this).next('.dropdown-menu').toggleClass('show');
    });
    
    // Close dropdowns when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown-menu').removeClass('show');
        }
    });
    
    // Form validation
    $('form').on('submit', function(e) {
        var requiredFields = $(this).find('[required]');
        var isValid = true;
        
        requiredFields.each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
    
    // Reset form
    $('.reset-form').on('click', function() {
        var form = $(this).closest('form');
        form[0].reset();
        form.find('.is-invalid').removeClass('is-invalid');
    });
    
    // Delete confirmation
    $('.delete-item').on('click', function(e) {
        e.preventDefault();
        var itemName = $(this).data('name');
        
        if (confirm('Are you sure you want to delete ' + itemName + '? This action cannot be undone.')) {
            // Proceed with deletion
            // This would typically be handled by your backend
            $(this).closest('tr, .card').fadeOut();
        }
    });
    
    // Edit item
    $('.edit-item').on('click', function(e) {
        e.preventDefault();
        var itemId = $(this).data('id');
        var itemData = $(this).data('item');
        
        // Populate form with item data
        var form = $('#editForm');
        form.find('[name="id"]').val(itemId);
        
        // Populate other fields based on the item data
        for (var key in itemData) {
            form.find('[name="' + key + '"]').val(itemData[key]);
        }
        
        // Show the form
        $('#addForm').hide();
        form.show();
        
        // Scroll to form
        $('html, body').animate({
            scrollTop: form.offset().top - 100
        }, 500);
    });
    
    // Filter functionality
    $('.filter-btn').on('click', function() {
        var category = $('#filterCategory').val();
        var status = $('#filterStatus').val();
        var searchTerm = $('#searchInput').val().toLowerCase();
        
        $('.filterable-item').each(function() {
            var itemCategory = $(this).data('category');
            var itemStatus = $(this).data('status');
            var itemText = $(this).text().toLowerCase();
            
            var categoryMatch = category === 'all' || itemCategory === category;
            var statusMatch = status === 'all' || itemStatus === status;
            var searchMatch = searchTerm === '' || itemText.includes(searchTerm);
            
            if (categoryMatch && statusMatch && searchMatch) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Reset filters
    $('.reset-filters').on('click', function() {
        $('#filterCategory, #filterStatus').val('all');
        $('#searchInput').val('');
        $('.filterable-item').show();
    });
    
    // Toggle between add and edit forms
    $('.show-add-form').on('click', function() {
        $('#editForm').hide();
        $('#addForm').show();
    });
    
    // Initialize any charts if needed
    if (typeof initCharts === 'function') {
        initCharts();
    }
    
    // Payment verification functionality
    function verifyPayment(paymentId, action) {
        console.log('Verifying payment:', paymentId, 'Action:', action);
        // Add your verification logic here
    }
    
    // Example function to handle response to verification request
    function respondToRequest(requestId, response) {
        console.log('Responding to request:', requestId, 'Response:', response);
        // Add your response logic here
    }
    
    // Add event listeners to buttons
    const approveButtons = document.querySelectorAll('.btn-primary');
    approveButtons.forEach(button => {
        button.addEventListener('click', function() {
            const paymentId = this.closest('.card').getAttribute('data-payment-id');
            verifyPayment(paymentId, 'approve');
        });
    });
    
    const rejectButtons = document.querySelectorAll('.btn-danger');
    rejectButtons.forEach(button => {
        button.addEventListener('click', function() {
            const paymentId = this.closest('.card').getAttribute('data-payment-id');
            verifyPayment(paymentId, 'reject');
        });
    });
    
    const respondButtons = document.querySelectorAll('.btn-outline-success');
    respondButtons.forEach(button => {
        button.addEventListener('click', function() {
            const requestId = this.closest('.card').getAttribute('data-request-id');
            respondToRequest(requestId, 'respond');
        });
    });

    // Search and Filter Functionality
    const searchForm = document.querySelector('form');
    if (searchForm) {
        const searchButton = searchForm.querySelector('button[type="button"]');
        const resetButton = searchForm.querySelector('button[type="reset"]');
        const verificationCards = document.querySelectorAll('.verification-examples .card');
        
        // Add data attributes to verification cards for filtering
        verificationCards.forEach((card, index) => {
            // Extract data from card content
            const travelerName = card.querySelector('p:nth-child(1)').textContent.split(':')[1].trim();
            const bookingId = card.querySelector('p:nth-child(2)').textContent.split(':')[1].trim();
            const issue = card.querySelector('p:nth-child(3)').textContent.split(':')[1].trim();
            const status = card.querySelector('.badge').textContent.trim();
            
            // Set data attributes for filtering
            card.setAttribute('data-traveler', travelerName);
            card.setAttribute('data-booking', bookingId);
            card.setAttribute('data-issue', issue);
            card.setAttribute('data-status', status);
            
            // Generate a transaction ID for demo purposes
            const transactionId = `TXN-${10000 + index}`;
            card.setAttribute('data-transaction', transactionId);
            
            // Add transaction ID to the card for display
            const issueElement = card.querySelector('p:nth-child(3)');
            const transactionElement = document.createElement('p');
            transactionElement.innerHTML = `<strong>Transaction ID:</strong> ${transactionId}`;
            card.querySelector('.card-body').insertBefore(transactionElement, issueElement);
        });
        
        // Search function
        function performSearch() {
            const transactionId = document.getElementById('transactionId').value.toLowerCase();
            const travelerName = document.getElementById('travelerName').value.toLowerCase();
            const bookingId = document.getElementById('bookingId').value.toLowerCase();
            const paymentStatus = document.getElementById('paymentStatus').value.toLowerCase();
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;
            
            verificationCards.forEach(card => {
                const cardTransactionId = card.getAttribute('data-transaction').toLowerCase();
                const cardTravelerName = card.getAttribute('data-traveler').toLowerCase();
                const cardBookingId = card.getAttribute('data-booking').toLowerCase();
                const cardStatus = card.getAttribute('data-status').toLowerCase();
                
                // Check if card matches all search criteria
                const matchesTransactionId = !transactionId || cardTransactionId.includes(transactionId);
                const matchesTravelerName = !travelerName || cardTravelerName.includes(travelerName);
                const matchesBookingId = !bookingId || cardBookingId.includes(bookingId);
                const matchesStatus = !paymentStatus || cardStatus.includes(paymentStatus);
                
                // Date filtering would require actual date data in the cards
                // For demo purposes, we'll skip date filtering
                
                // Show or hide card based on search results
                if (matchesTransactionId && matchesTravelerName && matchesBookingId && matchesStatus) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Show "no results" message if all cards are hidden
            const visibleCards = Array.from(verificationCards).filter(card => card.style.display !== 'none');
            const noResultsMessage = document.getElementById('noResultsMessage');
            
            if (visibleCards.length === 0) {
                if (!noResultsMessage) {
                    const message = document.createElement('div');
                    message.id = 'noResultsMessage';
                    message.className = 'alert alert-info mt-3';
                    message.innerHTML = '<i class="feather icon-info mr-2"></i> No verification requests match your search criteria.';
                    document.querySelector('.verification-examples').appendChild(message);
                }
            } else if (noResultsMessage) {
                noResultsMessage.remove();
            }
        }
        
        // Add event listeners for search and reset
        if (searchButton) {
            searchButton.addEventListener('click', performSearch);
        }
        
        if (resetButton) {
            resetButton.addEventListener('click', function() {
                // Reset form fields
                searchForm.reset();
                
                // Show all cards
                verificationCards.forEach(card => {
                    card.style.display = 'block';
                });
                
                // Remove "no results" message if it exists
                const noResultsMessage = document.getElementById('noResultsMessage');
                if (noResultsMessage) {
                    noResultsMessage.remove();
                }
            });
        }
        
        // Add event listeners for real-time filtering as user types
        const searchInputs = searchForm.querySelectorAll('input, select');
        searchInputs.forEach(input => {
            input.addEventListener('input', function() {
                // Debounce the search to avoid excessive filtering while typing
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(performSearch, 300);
            });
        });
    }
}); 