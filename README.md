# Okina Craft — Custom Apparel & Merchandise E-Commerce Platform

A modern, high-performance e-commerce platform for custom merchandise and personalized apparel, built on the **CodeIgniter 4** PHP framework.

Designed for standard **Shared Hosting (cPanel / Apache / LiteSpeed)** as well as VPS and Cloud deployments, with zero frontend build dependencies.

---

## Key Features

1. **Custom Artwork & Design Upload**:
   - Customers can upload high-resolution artwork (PNG, JPG, WebP) directly on the Product Detail Page.
   - Optional placement / custom text instructions with accordion drawer.
   - Upload preview and file attachment integrated with cart & checkout.

2. **WhatsApp Order & Support Integration**:
   - Dedicated "Send Photo & Print Details on WhatsApp" action button on product pages.
   - Automatic pre-filled WhatsApp message including the product title and page URL.

3. **Tiered Bulk / Volume Pricing (Vistaprint Style)**:
   - Volume discount intervals (10, 50, 100, 200, 500+ units).
   - In-place dynamic price updates: shows price range when Quantity = 1, and total discounted order price with per-unit breakdown when Quantity > 1.
   - Cart and checkout volume discount tier calculations.

4. **Dynamic Color & Size Variants**:
   - Interactive circular color swatches with dual-ring active indicator and hover tooltips.
   - Automatic main image and gallery thumbnail switching based on selected color.
   - Size selector pills (`S`, `M`, `L`, `XL`, `2XL`).
   - Integrated **📏 Size Chart** modal dialog with Okina Craft apparel sizing chart.
   - Automatically adapts per product ("depend on products" — only apparel with colors shows swatches, single items omit).

5. **Tabler Admin Panel**:
   - Full-featured, responsive dashboard powered by Tabler (Bootstrap 5).
   - Multi-option variant manager (Option 1: Color, Option 2: Size) with per-variant SKU, inventory, and pricing.
   - Tiered bulk pricing management with preset interval generator and live storefront preview.
   - Product catalog, collections, orders, contact messages, and store settings.

6. **Zero-Setup Database**:
   - Ships with pre-seeded SQLite database (`writable/database.sqlite`) containing products, collections, settings, and sample color-variant apparel.
   - Ready to switch to MySQL / MariaDB via `.env` with a single configuration toggle.

---

## Architecture & Code Structure

```
OkinaCraft/
├── app/
│   ├── Common.php              # Global helpers (money_format_inr, color_hex_by_name, slugify, store_setting)
│   ├── Config/
│   │   ├── App.php             # baseURL, timezone, indexPage
│   │   ├── Database.php        # SQLite3 default, MySQLi ready
│   │   ├── Filters.php         # AdminAuth filter alias, CSRF, security headers
│   │   ├── Routes.php          # Storefront, JSON API, and Admin routes
│   │   └── Security.php        # CSRF cookie/session & token settings
│   ├── Controllers/
│   │   ├── StoreController.php # Home, Catalog, PDP, Search, Cart, Checkout, Policies, Tracking
│   │   ├── ApiController.php   # AJAX Predictive Search & Cart endpoints
│   │   └── AdminController.php # Dashboard, Products, Collections, Orders, Messages, Settings
│   ├── Database/
│   │   ├── Migrations/         # 2026_01_01_000001_CreateStoreTables.php
│   │   └── Seeds/              # StoreSeeder.php
│   ├── Filters/
│   │   └── AdminAuth.php       # Protected /admin route filter
│   ├── Libraries/
│   │   ├── AuthService.php     # Session auth, brute-force rate limiter, password hashing
│   │   ├── CartService.php     # Cart state, bulk tiers, shipping thresholds
│   │   └── CatalogService.php  # Auto-bootstrapper, queries, filters, sorting, hydrators
│   ├── Models/                 # Admin, Setting, Policy, Collection, Product, Variant, Image, Order, OrderItem
│   └── Views/
│       ├── layout.php          # Main storefront layout (header, search, cart, footer)
│       ├── store/              # home, collection, product, search, cart, checkout, order_success
│       └── admin/              # layout, login, dashboard, products, product_form, collections, orders, settings
├── public/                     # Web Root (Point DocumentRoot here)
│   ├── .htaccess               # Apache / LiteSpeed rewrite rules
│   ├── index.php               # Front Controller
│   ├── assets/
│   │   ├── css/                # theme.css, mask-blobs.css, admin.css
│   │   ├── js/                 # storefront.js (predictive search, variants, bulk pricing, cart)
│   │   └── media/              # logos, banners, product mockups, size-chart.jpg
│   └── uploads/                # Customer custom design uploads & product image uploads
└── writable/
    └── database.sqlite         # Pre-seeded SQLite database (zero-setup)
```

---

## Quick Start (Local Development)

### 1. Requirements
- PHP 8.1+ with extensions: `sqlite3`, `pdo_sqlite`, `curl`, `mbstring`, `fileinfo`, `openssl`
- Composer (optional, vendor files are bundled)

### 2. Run the Development Server
```bash
php spark serve --port 8080
```
Open [http://localhost:8080](http://localhost:8080) in your browser.

### 3. Admin Panel Access
- **URL**: [http://localhost:8080/admin/login](http://localhost:8080/admin/login)
- **Email**: `admin@okinacraft.com`
- **Password**: `OkinaCraft@2026!`
*(Credentials can be changed in Admin → Settings)*

---

## Deployment (cPanel / Shared Hosting)

1. Upload files to your server (e.g. `public_html/` or a subdirectory).
2. Point your domain Document Root to the `public/` directory.
3. Ensure `writable/` and `public/uploads/` have write permissions (`0755` or `0775`).
4. Copy `env` to `.env` and set `app.baseURL = 'https://yourdomain.com/'` and `CI_ENVIRONMENT = production`.

---

## License
MIT
