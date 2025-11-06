const express = require('express');
const path = require('path');
const fs = require('fs');
const archiver = require('archiver');

const app = express();
const PORT = process.env.PORT || 3000;

// Serve static files
app.use(express.static('.'));

// Home page
app.get('/', (req, res) => {
    res.send(`
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WordPress GST Invoice & Inventory SaaS - Theme & Plugin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        header {
            text-align: center;
            color: white;
            margin-bottom: 3rem;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .card h2 {
            color: #2563eb;
            margin-bottom: 1rem;
            font-size: 1.75rem;
        }

        .card h3 {
            color: #4b5563;
            margin: 1.5rem 0 0.75rem;
            font-size: 1.125rem;
        }

        .card ul {
            list-style: none;
            margin-bottom: 1.5rem;
        }

        .card li {
            padding: 0.5rem 0;
            padding-left: 1.5rem;
            position: relative;
        }

        .card li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #10b981;
            font-weight: bold;
        }

        .download-btn {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            text-align: center;
            margin: 0.5rem 0;
            width: 100%;
        }

        .download-btn:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }

        .features {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .features h2 {
            color: #2563eb;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 2rem;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .feature {
            padding: 1rem;
            border-left: 3px solid #2563eb;
            background: #f9fafb;
            border-radius: 0.5rem;
        }

        .feature h4 {
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .feature p {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .tech-stack {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .tech-stack h2 {
            color: #2563eb;
            margin-bottom: 1rem;
            text-align: center;
        }

        .badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: center;
        }

        .badge {
            background: #e0e7ff;
            color: #3730a3;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .setup {
            background: #fef3c7;
            border-radius: 1rem;
            padding: 2rem;
            margin-top: 2rem;
            border: 2px solid #fbbf24;
        }

        .setup h2 {
            color: #92400e;
            margin-bottom: 1rem;
        }

        .setup ol {
            margin-left: 1.5rem;
            color: #78350f;
        }

        .setup li {
            margin: 0.5rem 0;
        }

        footer {
            text-align: center;
            color: white;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.2);
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 1.75rem;
            }

            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🎯 WordPress GST Invoice & Inventory SaaS</h1>
            <p class="subtitle">Complete Theme + Plugin System for Indian Businesses</p>
        </header>

        <div class="grid">
            <div class="card">
                <h2>📦 Surajx GII Theme</h2>
                <p>Modern WordPress theme with authentication and dashboard</p>

                <h3>Features:</h3>
                <ul>
                    <li>Login, Register, Forgot Password pages</li>
                    <li>Dashboard with 3 tabs (Products, Invoices, Account)</li>
                    <li>Google Sign-In integration</li>
                    <li>Responsive design</li>
                    <li>Invoice builder shortcode</li>
                    <li>REST API integration</li>
                </ul>

                <a href="/download/theme" class="download-btn">📥 Download Theme (ZIP)</a>
            </div>

            <div class="card">
                <h2>🔌 GST Invoice Inventory SaaS Plugin</h2>
                <p>Complete backend solution with REST API and GST calculations</p>

                <h3>Features:</h3>
                <ul>
                    <li>Product & Inventory management</li>
                    <li>GST-compliant invoices (CGST, SGST, IGST)</li>
                    <li>Google OAuth authentication</li>
                    <li>Full REST API</li>
                    <li>Admin dashboard</li>
                    <li>Multi-language (EN, HI)</li>
                </ul>

                <a href="/download/plugin" class="download-btn">📥 Download Plugin (ZIP)</a>
            </div>
        </div>

        <div class="features">
            <h2>✨ Key Features</h2>
            <div class="feature-grid">
                <div class="feature">
                    <h4>GST Compliance</h4>
                    <p>Full support for Indian GST with CGST, SGST, and IGST calculations</p>
                </div>
                <div class="feature">
                    <h4>Inventory Management</h4>
                    <p>Track products, manage stock, HSN/SAC codes</p>
                </div>
                <div class="feature">
                    <h4>Invoice Generation</h4>
                    <p>Create professional, GST-compliant invoices</p>
                </div>
                <div class="feature">
                    <h4>Google Sign-In</h4>
                    <p>Easy authentication with Google OAuth</p>
                </div>
                <div class="feature">
                    <h4>REST API</h4>
                    <p>Complete API for mobile and frontend integration</p>
                </div>
                <div class="feature">
                    <h4>Secure & Clean</h4>
                    <p>OOP architecture, sanitized inputs, nonce protection</p>
                </div>
            </div>
        </div>

        <div class="tech-stack">
            <h2>🛠 Technology Stack</h2>
            <div class="badges">
                <span class="badge">WordPress 6+</span>
                <span class="badge">PHP 8.0+</span>
                <span class="badge">MySQL 5.7+</span>
                <span class="badge">REST API</span>
                <span class="badge">Google OAuth</span>
                <span class="badge">jQuery</span>
                <span class="badge">Responsive CSS</span>
                <span class="badge">i18n Ready</span>
            </div>
        </div>

        <div class="setup">
            <h2>🚀 Quick Setup</h2>
            <ol>
                <li>Download both theme and plugin ZIP files</li>
                <li>Install theme: Appearance → Themes → Add New → Upload</li>
                <li>Install plugin: Plugins → Add New → Upload</li>
                <li>Activate both theme and plugin</li>
                <li>Create pages: Account, Login, Register, Pricing, Forgot Password</li>
                <li>Assign page templates from the dropdown</li>
                <li>Configure Google OAuth in plugin settings</li>
                <li>Set up business details (GST number, address, etc.)</li>
                <li>Start using the dashboard!</li>
            </ol>
        </div>

        <footer>
            <p>Built with ❤️ for Indian businesses | Compatible with WP 6+ and PHP 8+</p>
            <p style="margin-top: 1rem; opacity: 0.8;">Complete source code included in both packages</p>
        </footer>
    </div>
</body>
</html>
    `);
});

// Download theme
app.get('/download/theme', (req, res) => {
    const themePath = path.join(__dirname, 'surajx-gii-theme.zip');

    if (!fs.existsSync(themePath)) {
        // Create zip on the fly if it doesn't exist
        const output = fs.createWriteStream(themePath);
        const archive = archiver('zip', { zlib: { level: 9 } });

        output.on('close', () => {
            res.download(themePath, 'surajx-gii-theme.zip');
        });

        archive.on('error', (err) => {
            console.error('Archive error:', err);
            res.status(500).send('Error creating archive');
        });

        archive.pipe(output);
        archive.directory(path.join(__dirname, 'surajx-gii-theme'), 'surajx-gii-theme');
        archive.finalize();
    } else {
        res.download(themePath, 'surajx-gii-theme.zip');
    }
});

// Download plugin
app.get('/download/plugin', (req, res) => {
    const pluginPath = path.join(__dirname, 'gst-invoice-inventory-saas.zip');

    if (!fs.existsSync(pluginPath)) {
        // Create zip on the fly if it doesn't exist
        const output = fs.createWriteStream(pluginPath);
        const archive = archiver('zip', { zlib: { level: 9 } });

        output.on('close', () => {
            res.download(pluginPath, 'gst-invoice-inventory-saas.zip');
        });

        archive.on('error', (err) => {
            console.error('Archive error:', err);
            res.status(500).send('Error creating archive');
        });

        archive.pipe(output);
        archive.directory(path.join(__dirname, 'gst-invoice-inventory-saas'), 'gst-invoice-inventory-saas');
        archive.finalize();
    } else {
        res.download(pluginPath, 'gst-invoice-inventory-saas.zip');
    }
});

app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
    console.log(`Visit http://localhost:${PORT} to view the WordPress packages`);
});
