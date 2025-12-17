# GEMINI.md

## Project Overview

This is a comprehensive business management application built with Laravel 12. It appears to be an ERP-like system designed for a multi-branch company, with a strong focus on inventory, production, and sales. The application includes modules for:

*   **Inventory Management:** Tracking items, purchases, stock levels, and movements.
*   **Production Management:** Managing recipes, daily produce, and production requests.
*   **Sales Management:** A POS system, sales tracking, and customer management.
*   **Employee Management:** Employee records, clock-in/out, leave management, and roles/permissions.
*   **Accounting:** Financial transactions, reporting, and a phased implementation of a full accounting platform.
*   **Analytics and Reporting:** Dashboards and reports for various aspects of the business.

The application uses the TALL stack (Tailwind CSS, Alpine.js, Laravel, and Livewire), with Volt and Flux for streamlined Livewire components. It also uses `spatie/laravel-permission` for role-based access control and `maatwebsite/excel` for data exports.

## Building and Running

### Prerequisites

*   PHP 8.2+
*   Composer
*   Node.js and npm
*   A database (the project seems to be configured for MySQL, but this can be changed in the `.env` file).

### Installation

1.  Clone the repository.
2.  Copy `.env.example` to `.env` and configure your database and other settings.
3.  Run `composer install`.
4.  Run `npm install`.
5.  Run `php artisan key:generate`.
6.  Run `php artisan migrate --seed`.

### Development

To run the development server, you can use the `dev` script defined in `composer.json`:

```bash
composer run dev
```

This will start the Laravel development server, the queue listener, the pail logger, and the Vite development server.

### Testing

To run the tests, you can use the `test` script:

```bash
composer run test
```

## Development Conventions

*   **Livewire:** The application is heavily based on Livewire for its frontend. Most of the application logic is contained within Livewire components.
*   **Volt and Flux:** The project uses Volt and Flux to create single-file Livewire components, which combines the component's class and view into a single file.
*   **TALL Stack:** The UI is built with Tailwind CSS and Alpine.js, following the TALL stack conventions.
*   **Role-Based Access Control:** The application uses `spatie/laravel-permission` for managing roles and permissions. Access to routes and functionality is controlled by middleware and directives provided by this package.
*   **Multi-Branch Architecture:** The application is designed to support multiple branches. The `setBranchContext` middleware and `current_branch_id()` helper function are used to manage the current branch context.
*   **Phased Development:** The accounting module is being developed in phases, with the initial phase focusing on transaction recording and the next phase planned to include a General Ledger and more advanced reporting.
*   **Code Style:** The project uses `laravel/pint` for code style. You can run `vendor/bin/pint` to format your code.
