# MedaAds

MedaAds is a lightweight PHP 8.3 + MySQL advertising dashboard. It provides:

- Session-based authentication for administrators.
- A dashboard to manage ad creatives, placements, and activation.
- JSON API endpoints to fetch active ads and track impressions.

## Getting started

1. **Install dependencies**

   ```bash
   composer install
   ```

2. **Configure environment**

   Copy `.env.example` to `.env` (or create your own) and fill in your database credentials:

   ```bash
   cp .env.example .env
   ```

   ```env
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=medaads
   DB_USERNAME=root
   DB_PASSWORD=secret
   DB_CHARSET=utf8mb4
   ```

3. **Prepare the database**

   ```bash
   mysql -u root -p medaads < sql/schema.sql
   ```

   The seed user is `admin@medaads.test` with password `admin123`.

4. **Run the development server**

   ```bash
   php -S 0.0.0.0:8080 -t public
   ```

   or use Wasmer:

   ```bash
   wasmer run .
   ```

5. **Access the app**

   Open [http://localhost:8080](http://localhost:8080) in your browser.

## Project structure

```
MedaAds/
├─ public/
│  ├─ index.php          # Routing entrypoint
│  ├─ login.php          # Login form
│  ├─ logout.php         # Logout handler
│  ├─ dashboard.php      # Ad management UI
│  ├─ assets/style.css   # Bootstrap-based dark theme
│  └─ api/
│     ├─ get_ad.php      # Returns a random active ad as JSON
│     └─ track.php       # Records ad impressions
├─ app/
│  ├─ db.php             # PDO connection using .env config
│  ├─ auth.php           # Authentication helpers
│  └─ helpers.php        # Utility helpers (env, CSRF, etc.)
├─ sql/schema.sql        # Database schema and seed data
├─ Wasmer.toml           # Wasmer PHP 8.3 runtime configuration
└─ composer.json         # Composer metadata
```
