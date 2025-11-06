/**
 * Dashboard Scripts
 *
 * @package Surajx_GII_Theme
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Tab switching
        $('.dashboard-tab').on('click', function() {
            const tab = $(this).data('tab');

            // Update active tab
            $('.dashboard-tab').removeClass('active');
            $(this).addClass('active');

            // Update active panel
            $('.tab-panel').removeClass('active');
            $('#' + tab + '-panel').addClass('active');

            // Load data for the tab
            loadTabData(tab);
        });

        // Load initial data
        loadTabData('products');

        // Add product button
        $(document).on('click', '#add-product-btn', function() {
            showProductModal();
        });

        // Create invoice button
        $(document).on('click', '#create-invoice-btn', function() {
            window.location.href = '/invoice-builder';
        });

        // Add invoice item
        $(document).on('click', '#add-item-btn', function() {
            addInvoiceItem();
        });

        // Remove invoice item
        $(document).on('click', '.remove-item', function() {
            $(this).closest('.item-row').remove();
            calculateInvoiceTotal();
        });

        // Calculate invoice total on input change
        $(document).on('input', 'input[name="quantity[]"], input[name="rate[]"]', function() {
            calculateInvoiceTotal();
        });

        // Submit invoice form
        $(document).on('submit', '#invoice-form', function(e) {
            e.preventDefault();
            submitInvoice();
        });
    });

    /**
     * Load data for a specific tab
     */
    function loadTabData(tab) {
        const panel = $('#' + tab + '-panel');
        const listContainer = panel.find('#' + tab + '-list');

        if (listContainer.find('.spinner').length === 0) {
            return; // Already loaded
        }

        let endpoint = '';
        switch(tab) {
            case 'products':
                endpoint = 'products';
                break;
            case 'invoices':
                endpoint = 'invoices';
                break;
            case 'account':
                endpoint = 'account';
                break;
        }

        $.ajax({
            url: giiData.restUrl + endpoint,
            method: 'GET',
            headers: {
                'X-WP-Nonce': giiData.nonce
            },
            success: function(response) {
                if (tab === 'products') {
                    renderProducts(response);
                } else if (tab === 'invoices') {
                    renderInvoices(response);
                } else if (tab === 'account') {
                    renderAccount(response);
                }
            },
            error: function(xhr) {
                listContainer.html('<div class="alert alert-error">Failed to load data. Please try again.</div>');
            }
        });
    }

    /**
     * Render products list
     */
    function renderProducts(products) {
        const container = $('#products-list');

        if (!products || products.length === 0) {
            container.html('<p>No products found. <a href="#" id="add-product-btn">Add your first product</a></p>');
            return;
        }

        let html = '<table class="data-table"><thead><tr>';
        html += '<th>Product Name</th>';
        html += '<th>SKU</th>';
        html += '<th>HSN/SAC</th>';
        html += '<th>Price</th>';
        html += '<th>Stock</th>';
        html += '<th>Actions</th>';
        html += '</tr></thead><tbody>';

        products.forEach(function(product) {
            html += '<tr>';
            html += '<td>' + escapeHtml(product.name) + '</td>';
            html += '<td>' + escapeHtml(product.sku) + '</td>';
            html += '<td>' + escapeHtml(product.hsn_sac) + '</td>';
            html += '<td>₹' + parseFloat(product.price).toFixed(2) + '</td>';
            html += '<td>' + parseInt(product.stock) + '</td>';
            html += '<td><button class="btn btn-secondary btn-sm edit-product" data-id="' + product.id + '">Edit</button></td>';
            html += '</tr>';
        });

        html += '</tbody></table>';
        container.html(html);
    }

    /**
     * Render invoices list
     */
    function renderInvoices(invoices) {
        const container = $('#invoices-list');

        if (!invoices || invoices.length === 0) {
            container.html('<p>No invoices found. <a href="#" id="create-invoice-btn">Create your first invoice</a></p>');
            return;
        }

        let html = '<table class="data-table"><thead><tr>';
        html += '<th>Invoice #</th>';
        html += '<th>Customer</th>';
        html += '<th>Date</th>';
        html += '<th>Total</th>';
        html += '<th>Status</th>';
        html += '<th>Actions</th>';
        html += '</tr></thead><tbody>';

        invoices.forEach(function(invoice) {
            html += '<tr>';
            html += '<td>' + escapeHtml(invoice.invoice_number) + '</td>';
            html += '<td>' + escapeHtml(invoice.customer_name) + '</td>';
            html += '<td>' + formatDate(invoice.created_at) + '</td>';
            html += '<td>₹' + parseFloat(invoice.total_amount).toFixed(2) + '</td>';
            html += '<td><span class="badge badge-' + invoice.status + '">' + invoice.status + '</span></td>';
            html += '<td><button class="btn btn-secondary btn-sm view-invoice" data-id="' + invoice.id + '">View</button></td>';
            html += '</tr>';
        });

        html += '</tbody></table>';
        container.html(html);
    }

    /**
     * Render account details
     */
    function renderAccount(account) {
        const container = $('#account-details');

        let html = '<div class="account-info">';
        html += '<div class="form-group">';
        html += '<label>Name</label>';
        html += '<input type="text" value="' + escapeHtml(account.name) + '" readonly>';
        html += '</div>';
        html += '<div class="form-group">';
        html += '<label>Email</label>';
        html += '<input type="email" value="' + escapeHtml(account.email) + '" readonly>';
        html += '</div>';
        html += '<div class="form-group">';
        html += '<label>Business Name</label>';
        html += '<input type="text" value="' + escapeHtml(account.business_name || '') + '" id="business-name">';
        html += '</div>';
        html += '<div class="form-group">';
        html += '<label>GST Number</label>';
        html += '<input type="text" value="' + escapeHtml(account.gst_number || '') + '" id="gst-number">';
        html += '</div>';
        html += '<button class="btn btn-primary" id="save-account">Save Changes</button>';
        html += '</div>';

        container.html(html);
    }

    /**
     * Calculate invoice total
     */
    function calculateInvoiceTotal() {
        let subtotal = 0;

        $('.item-row').each(function() {
            const qty = parseFloat($(this).find('input[name="quantity[]"]').val()) || 0;
            const rate = parseFloat($(this).find('input[name="rate[]"]').val()) || 0;
            subtotal += qty * rate;
        });

        const gst = subtotal * 0.18;
        const total = subtotal + gst;

        $('#subtotal').text('₹' + subtotal.toFixed(2));
        $('#gst').text('₹' + gst.toFixed(2));
        $('#total').text('₹' + total.toFixed(2));
    }

    /**
     * Add invoice item row
     */
    function addInvoiceItem() {
        const html = '<div class="item-row">' +
            '<select name="product_id[]" required><option value="">Select Product</option></select>' +
            '<input type="number" name="quantity[]" placeholder="Qty" min="1" required>' +
            '<input type="number" name="rate[]" placeholder="Rate" step="0.01" required>' +
            '<button type="button" class="btn btn-secondary remove-item">Remove</button>' +
            '</div>';

        $('#items-container').append(html);
    }

    /**
     * Submit invoice
     */
    function submitInvoice() {
        const formData = $('#invoice-form').serialize();

        $.ajax({
            url: giiData.restUrl + 'invoices',
            method: 'POST',
            headers: {
                'X-WP-Nonce': giiData.nonce
            },
            data: formData,
            success: function(response) {
                alert('Invoice created successfully!');
                window.location.href = '/account';
            },
            error: function(xhr) {
                alert('Failed to create invoice. Please try again.');
            }
        });
    }

    /**
     * Show product modal
     */
    function showProductModal() {
        // This would open a modal to add a product
        // For now, redirect to a separate page or show inline form
        const name = prompt('Product Name:');
        if (!name) return;

        const sku = prompt('SKU:');
        const hsn = prompt('HSN/SAC Code:');
        const price = prompt('Price:');
        const stock = prompt('Stock Quantity:');

        $.ajax({
            url: giiData.restUrl + 'products',
            method: 'POST',
            headers: {
                'X-WP-Nonce': giiData.nonce
            },
            data: {
                name: name,
                sku: sku,
                hsn_sac: hsn,
                price: price,
                stock: stock
            },
            success: function(response) {
                alert('Product added successfully!');
                $('#products-list').html('<div class="spinner"></div>');
                loadTabData('products');
            },
            error: function(xhr) {
                alert('Failed to add product. Please try again.');
            }
        });
    }

    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text ? text.replace(/[&<>"']/g, function(m) { return map[m]; }) : '';
    }

    /**
     * Format date
     */
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-IN', { year: 'numeric', month: 'short', day: 'numeric' });
    }

})(jQuery);
