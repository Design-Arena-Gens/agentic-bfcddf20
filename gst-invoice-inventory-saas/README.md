# GST Invoice Inventory SaaS Plugin

Complete SaaS solution for GST-compliant invoicing and inventory management for Indian businesses.

## Features

- **Product Management**: Create, update, and manage products with HSN/SAC codes
- **Invoice Generation**: Create GST-compliant invoices with automatic tax calculations
- **GST Compliance**: Support for CGST, SGST, and IGST calculations
- **Google OAuth**: Sign in with Google for easy authentication
- **REST API**: Full REST API for integration with frontend and mobile apps
- **Multi-language**: English and Hindi support (i18n ready)
- **Secure**: Sanitized inputs, escaped outputs, nonce verification
- **OOP Architecture**: Clean, object-oriented code with namespaces

## Installation

1. Download the plugin ZIP file
2. Log in to WordPress admin
3. Navigate to Plugins → Add New → Upload Plugin
4. Select the ZIP file and click "Install Now"
5. Activate the plugin

## Configuration

### Google OAuth Setup

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Enable Google Sign-In API
4. Create OAuth 2.0 credentials
5. Add authorized JavaScript origins:
   - `https://yourdomain.com`
6. Add authorized redirect URIs:
   - `https://yourdomain.com`
7. Copy the Client ID
8. In WordPress admin, go to GII SaaS → Settings
9. Paste the Client ID in "Google Client ID" field
10. Save settings

### Business Settings

1. Go to GII SaaS → Settings
2. Fill in:
   - Business Name
   - GST Number (15 characters)
   - Business Address
   - State (for GST calculations)
3. Save settings

## Database Tables

The plugin creates the following tables:

- `wp_gii_products` - Product catalog
- `wp_gii_invoices` - Invoice records
- `wp_gii_invoice_items` - Invoice line items
- `wp_gii_customers` - Customer details
- `wp_gii_settings` - User-specific settings

## REST API Endpoints

### Authentication

- `POST /wp-json/gii-saas/v1/auth/google` - Google Sign-In

### Products

- `GET /wp-json/gii-saas/v1/products` - Get all products
- `POST /wp-json/gii-saas/v1/products` - Create product
- `GET /wp-json/gii-saas/v1/products/{id}` - Get single product
- `PUT /wp-json/gii-saas/v1/products/{id}` - Update product
- `DELETE /wp-json/gii-saas/v1/products/{id}` - Delete product

### Invoices

- `GET /wp-json/gii-saas/v1/invoices` - Get all invoices
- `POST /wp-json/gii-saas/v1/invoices` - Create invoice
- `GET /wp-json/gii-saas/v1/invoices/{id}` - Get single invoice
- `PUT /wp-json/gii-saas/v1/invoices/{id}` - Update invoice
- `DELETE /wp-json/gii-saas/v1/invoices/{id}` - Delete invoice

### Account

- `GET /wp-json/gii-saas/v1/account` - Get account details
- `PUT /wp-json/gii-saas/v1/account` - Update account details

## Usage Examples

### Creating a Product

```php
$product_data = array(
    'name'        => 'Sample Product',
    'sku'         => 'SKU-001',
    'hsn_sac'     => '1234',
    'price'       => 1000.00,
    'tax_rate'    => 18.00,
    'stock'       => 100,
    'unit'        => 'piece',
);

$product_id = GII_SaaS\Product::create($product_data);
```

### Creating an Invoice

```php
$invoice_data = array(
    'customer_name'  => 'John Doe',
    'customer_gst'   => '27AAPFU0939F1ZV',
    'items'          => array(
        array(
            'product_id'   => 1,
            'product_name' => 'Sample Product',
            'quantity'     => 2,
            'rate'         => 1000.00,
            'tax_rate'     => 18.00,
        ),
    ),
);

$invoice_id = GII_SaaS\Invoice::create($invoice_data);
```

### GST Calculation

```php
// Calculate CGST and SGST (intra-state)
$cgst_sgst = GII_SaaS\GST_Calculator::calculate_cgst_sgst(1000, 18);
// Returns: ['cgst' => 90, 'sgst' => 90, 'total' => 180]

// Calculate IGST (inter-state)
$igst = GII_SaaS\GST_Calculator::calculate_igst(1000, 18);
// Returns: ['igst' => 180, 'total' => 180]

// Validate GST number
$is_valid = GII_SaaS\GST_Calculator::validate_gst_number('27AAPFU0939F1ZV');
// Returns: true or false
```

## Security Features

- **Input Sanitization**: All user inputs are sanitized using WordPress functions
- **Output Escaping**: All outputs are escaped to prevent XSS attacks
- **Nonce Verification**: CSRF protection on all forms and AJAX requests
- **Prepared Statements**: SQL injection prevention with prepared queries
- **Capability Checks**: User capability verification for admin functions

## Internationalization

The plugin is translation-ready. To translate:

1. Use Poedit or similar tool
2. Translate the `.pot` file in `/languages` folder
3. Save as `gst-invoice-inventory-saas-{locale}.po` and `.mo`

Supported languages:
- English (default)
- Hindi (hi_IN) - Coming soon

## Requirements

- WordPress 6.0 or higher
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Surajx GII Theme (companion theme)

## Hooks and Filters

### Actions

```php
// After product created
do_action('gii_saas_product_created', $product_id, $product_data);

// After invoice created
do_action('gii_saas_invoice_created', $invoice_id, $invoice_data);

// After invoice paid
do_action('gii_saas_invoice_paid', $invoice_id);
```

### Filters

```php
// Modify invoice data before creation
apply_filters('gii_saas_invoice_data', $invoice_data);

// Modify product data before creation
apply_filters('gii_saas_product_data', $product_data);

// Modify GST calculation
apply_filters('gii_saas_gst_calculation', $gst_breakdown);
```

## Development

### File Structure

```
gst-invoice-inventory-saas/
├── admin/
│   ├── class-admin.php
│   └── css/
│       └── admin.css
├── includes/
│   ├── class-database.php
│   ├── class-rest-api.php
│   ├── class-google-auth.php
│   ├── class-invoice.php
│   ├── class-product.php
│   └── class-gst-calculator.php
├── languages/
├── gst-invoice-inventory-saas.php
└── README.md
```

### Namespace

All classes use the `GII_SaaS` namespace:

```php
namespace GII_SaaS;

class Product {
    // Class code
}
```

## Support

For support and documentation:
- GitHub: [Your Repository]
- Email: support@example.com
- Documentation: [Your Docs URL]

## Changelog

### Version 1.0.0
- Initial release
- Product management
- Invoice generation
- GST calculations (CGST, SGST, IGST)
- Google OAuth integration
- REST API
- Multi-language support
- Admin dashboard

## License

GNU General Public License v2 or later
http://www.gnu.org/licenses/gpl-2.0.html

## Credits

- Plugin by Surajx
- Built for GST Invoice Inventory SaaS platform
- Compatible with Surajx GII Theme
