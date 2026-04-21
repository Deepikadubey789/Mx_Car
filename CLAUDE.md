# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Mxcar is a car rental marketplace web application (Turo clone) built on the Botble CMS platform using Laravel 12.53.0 and PHP 8.2/8.3. The application enables car owners to list vehicles and customers to book rentals with features like pricing protection, telematics integration, and chat support.

## Build and Development Commands

### PHP/Laravel Commands
```bash
# Development server
php artisan serve

# Run migrations
php artisan migrate
php artisan migrate:refresh --seed

# Run tests
php artisan test
vendor/bin/phpunit
vendor/bin/phpunit --filter TestName

# Code style (Laravel Pint)
vendor/bin/pint
vendor/bin/pint --test

# Static analysis (Larastan/PHPStan)
vendor/bin/phpstan analyse --memory-limit=1G

# Refactoring (Rector)
vendor/bin/rector process --dry-run

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Frontend Asset Commands
```bash
# Development
npm run dev

# Watch for changes
npm run watch

# Production build
npm run prod

# Build specific theme/plugin assets
npm run dev --theme=carento
npm run dev --plugin=car-rentals
```

### Car Rental Specific Commands
```bash
# Generate pricing recommendations based on demand signals
php artisan car-rentals:generate-demand-pricing-recommendations

# Apply pending recommendations
php artisan car-rentals:auto-apply-recommendations

# Send customer notifications
php artisan car-rentals:send-trip-reminders
php artisan car-rentals:send-return-alerts

# Update exchange rates
php artisan car-rentals:update-exchange-rates

# Calculate vendor quality scores
php artisan car-rentals:calculate-vendor-quality

# Seed test data
php artisan car-rentals:seed-currencies
php artisan car-rentals:seed-demand-signals
```

## Architecture Overview

### Botble CMS Platform Structure

The application uses the Botble CMS platform architecture with the following key directories:

- **`platform/core/`** - Core CMS modules providing base functionality:
  - `acl/` - Authentication, authorization, roles/permissions
  - `base/` - Base classes and utilities
  - `dashboard/` - Admin dashboard
  - `media/` - File/media management
  - `setting/` - System settings
  - `table/` - Data table components

- **`platform/plugins/`** - Feature plugins:
  - `car-rentals/` - Main business logic for car rental functionality
  - `payment/`, `stripe/`, `paypal/`, `razorpay/` - Payment integrations
  - `blog/`, `faq/`, `contact/` - Content management
  - `location/` - Geographic/location services

- **`platform/themes/`** - Frontend themes:
  - `carento/` - Active theme for the car rental marketplace

- **`platform/packages/`** - Shared packages

### Plugin Architecture (car-rentals)

The car-rentals plugin follows the Botble plugin structure:

```
platform/plugins/car-rentals/
├── src/
│   ├── Models/         # Eloquent models (Car, Booking, Vendor, etc.)
│   ├── Http/
│   │   ├── Controllers/  # Admin and frontend controllers
│   │   └── Requests/     # Form request validation
│   ├── Services/     # Business logic services
│   ├── Repositories/ # Data access layer
│   ├── Events/         # Domain events
│   ├── Listeners/      # Event listeners
│   ├── Jobs/           # Queueable jobs
│   ├── Notifications/  # Email/push notifications
│   ├── Observers/      # Model observers
│   ├── Tables/         # Data table definitions
│   ├── Forms/          # Form builders
│   └── PanelSections/  # Admin panel sections
├── database/
│   └── migrations/     # Plugin-specific migrations
├── resources/
│   ├── lang/           # Translations
│   └── views/          # Blade templates
├── routes/
│   └── *.php           # Route definitions
├── helpers/            # Helper functions
├── config/             # Configuration files
└── public/             # Plugin assets
```

### Application Structure

- **`app/`** - Standard Laravel application directory:
  - `Http/Controllers/Admin/` - Admin-specific controllers (e.g., ChatSettingController)
  - `Http/Controllers/Api/` - API controllers (e.g., ChatController)
  - `Models/` - Custom models (User, ChatConversation, ChatMessage, ChatSetting)

- **`database/migrations/`** - Core application migrations (car rentals migrations use `cr_` prefix)

- **`routes/`** - Route definitions:
  - `web.php` - Web routes (includes chat API routes that bypass Botble API middleware)
  - `api.php` - API routes (currently minimal due to web-based chat API)

### Key Features

**Dynamic Pricing Engine**: The system includes demand-based pricing with:
- Demand signals (views, bookings, seasonal trends)
- Auto-pricing recommendations
- Protection plans and insurance

**Telematics Integration**: Vehicle tracking and data collection support.

**Chat System**: Customer support chat with:
- AI-powered responses via OpenAI integration
- Chat history and suggested questions
- Configurable prompts and settings

**KYC (Know Your Customer)**: Identity verification system for vendors.

**Multi-vendor Support**: Quality scoring, badges, and vendor management.

## Testing

- **Test Database**: `mxcar_web_testing` (configure in `phpunit.xml`)
- **Test Structure**:
  - `tests/Unit/` - Unit tests
  - `tests/Feature/` - Feature/integration tests
- **Test Environment**: `testing` with array/session drivers, sync queue

## Important Patterns

### Working with Plugins
- Plugins can be activated/deactivated: `php artisan cms:plugin:activate car-rentals`
- Plugin assets are published via: `php artisan cms:publish:assets`
- Plugin migrations run automatically when active

### Database Migrations
- Core migrations in `database/migrations/`
- Plugin migrations in `platform/plugins/{plugin}/database/migrations/`
- Car rentals tables use `cr_` prefix (e.g., `cr_cars`, `cr_bookings`)

### Frontend Assets
- Laravel Mix manages compilation
- Theme assets are built separately using `--theme={name}` flag
- Plugin assets defined in each plugin's `webpack.mix.js`

### Admin Panel
- URL: `/admin` (configurable via `ADMIN_DIR` env var)
- Built on Botble's table and form builder components
- Panel sections registered in plugin service providers

## Environment Variables

Key configuration in `.env`:
- `ADMIN_DIR` - Admin URL path (default: `admin`)
- `CMS_ENABLE_INSTALLER` - Enable CMS installer
- `DB_STRICT=false` - Required for Botble compatibility
- `APP_URL` - Must match actual domain
