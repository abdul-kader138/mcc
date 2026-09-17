# 3D Model Studio

An item library and browser-based 3D customization workspace built with Laravel 13, Filament 3, and Three.js.

## Features

- Filament admin panel with email/password and optional Google sign-in.
- User, role/permission, system settings, activity log, and two-factor authentication support.
- Item management with name, description, cover image, and GLB upload.
- Public model gallery at `/models`.
- Interactive GLB preview with mesh selection, color changes, image textures, orbit/zoom controls, and PNG/JPEG export.
- Sanctum account API for registration, login, profile updates, password changes, 2FA, data export, and account deletion.

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
npm run build
```

Set the database and mail values in `.env`. The seeded administrator uses `ADMIN_EMAIL` and `ADMIN_PASSWORD`.

The admin panel is available at `/`. Create an item, upload a `.glb` model, enable **Visible on public gallery**, and open `/models` to share it.

### Upload size

The application accepts GLB files up to 100 MB. The web PHP configuration is provided in `public/.user.ini` with a 128 MB file limit and a 150 MB POST limit.

When running the local server, use the project launcher so the limits are applied to the PHP process that receives the upload:

```bash
composer run serve
```

After changing PHP-FPM or Apache configuration, restart that service before uploading larger files.
