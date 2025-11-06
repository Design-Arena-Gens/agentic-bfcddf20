# Surajx GII Theme

Modern WordPress theme for GST Invoice Inventory SaaS platform.

## Features

- Responsive design
- Built-in authentication pages (Login, Register, Forgot Password)
- Dashboard with 3 tabs: Products, Invoices, Account
- Google Sign-In integration
- REST API integration with GII SaaS plugin
- Multi-language support (English, Hindi)
- GST-compliant invoice builder
- Clean, modern UI

## Installation

1. Download the theme ZIP file
2. Log in to WordPress admin
3. Navigate to Appearance → Themes → Add New
4. Click "Upload Theme" and select the ZIP file
5. Click "Install Now"
6. Activate the theme

## Setup

### Required Pages

Create the following pages and assign the appropriate templates:

1. **Account** - Template: Account Dashboard
2. **Login** - Template: Login Page
3. **Register** - Template: Register Page
4. **Pricing** - Template: Pricing Page
5. **Forgot Password** - Template: Forgot Password Page

### Menu Setup

1. Go to Appearance → Menus
2. Create a new menu called "Primary Menu"
3. Add links to: Home, Pricing, Account (for logged-in users), Login/Register (for logged-out users)
4. Assign to "Primary Menu" location

### Google Sign-In Configuration

1. Install and activate the "GST Invoice Inventory SaaS" plugin
2. Configure Google OAuth credentials in plugin settings
3. The theme will automatically display Google Sign-In buttons

### Shortcodes

The theme includes the following shortcodes:

- `[gii_customer_dashboard]` - Display customer dashboard with Products, Invoices, and Account tabs
- `[gii_invoice_builder]` - Display GST invoice creation form
- `[gii_google_signin]` - Display Google Sign-In button

## Customization

### Styling

The theme uses a clean, modern design with primary color `#2563eb` (blue). To customize:

1. Edit `style.css` for global styles
2. Modify color variables in CSS for consistent branding

### Templates

Available page templates:

- `front-page.php` - Homepage
- `page-pricing.php` - Pricing page
- `page-account.php` - Dashboard (requires login)
- `page-login.php` - Login page
- `page-register.php` - Registration page
- `page-forgot-password.php` - Password reset page
- `index.php` - Blog/archive page

## Requirements

- WordPress 6.0 or higher
- PHP 8.0 or higher
- GST Invoice Inventory SaaS plugin (companion plugin)

## Translation

The theme is translation-ready. To translate:

1. Use Poedit or similar tool
2. Translate the `.pot` file in `/languages` folder
3. Save as `surajx-gii-theme-{locale}.po` and `.mo`

Supported languages:
- English (default)
- Hindi (hi_IN) - Coming soon

## Support

For support and documentation, please visit:
- GitHub: [Your Repository]
- Email: support@example.com

## Changelog

### Version 1.0.0
- Initial release
- Dashboard with Products, Invoices, Account tabs
- Authentication pages
- Google Sign-In integration
- GST invoice builder
- Responsive design
- Translation ready

## License

GNU General Public License v2 or later
http://www.gnu.org/licenses/gpl-2.0.html

## Credits

- Theme by Surajx
- Built for GST Invoice Inventory SaaS platform
