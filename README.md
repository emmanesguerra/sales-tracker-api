# Sales Tracker API

**Sales Tracker API** is a Laravel-based backend application that follows the Service-Repository design pattern. This API provides essential features for managing sales records, generating QR codes, uploading CSV data, and more. It supports multi-tenancy using a shared database and shared schema approach, ensuring efficient data management for multiple tenants with a single database.

## Table of Contents
- [Installation](#installation)
- [Dependencies](#dependencies)
- [Tech Stack](#tech-stack)
- [API Routes](#api-routes)
- [Folder Structure](#folder-structure)
- [Architecture](#architecture)
- [Usage](#usage)

## Installation

To get started with the Sales Tracker API, follow these steps:

1. Clone this repository to your local machine:

   ```bash
   git clone https://github.com/emmanesguerra/sales-tracker-api.git
   ```

2. Navigate to the project directory:

   ```bash
   cd sales-tracker-api
   ```

3. Install dependencies using Composer:

   ```bash
   composer install
   ```

4. Set up your environment configuration:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Set up your database configuration:

   - Run migrations:

     ```bash
     php artisan migrate
     ```

6. Run the application locally:

   ```bash
   php artisan serve
   ```

7. The API should now be accessible at `http://localhost:8000`.

## Dependencies

The Sales Tracker API requires the following dependencies:

- PHP ^8.2
- Laravel Framework ^12.0
- Laravel Sanctum for API authentication
- Bacon QR Code for generating QR codes
- PHPSpreadsheet for exporting sales data

**Development Dependencies**:
- FakerPHP for generating fake data
- PHPUnit for unit testing

To install the required dependencies, run:

```bash
composer install
```

## Tech Stack

The following technologies, frameworks, and tools were used to build the **Sales Tracker API**:

### Backend Framework
- **Laravel**: A robust PHP framework used for building web applications. It provides elegant syntax and a rich set of tools for building scalable APIs.

### Authentication
- **Laravel Sanctum**: A simple package for API token authentication in Laravel.

### Database
- **MySQL** (or SQLite, depending on your setup): Used for storing data.
- **Shared Database & Shared Schema**: Multi-tenancy is implemented using a shared database and schema approach, where tenant data is logically separated but stored in the same database.

### QR Code Generation
- **dompdf**: A library for generating PDF documents, used for generating sales reports or receipts.
- **Bacon QR Code**: A library for generating QR codes, used in the API to track sales.

### PDF and Spreadsheet Generation
- **PHPSpreadsheet**: Used for handling spreadsheet files (e.g., CSV, Excel) related to sales data.

### Testing
- **PHPUnit**: A unit testing framework for testing the application’s functionality.
- **FakerPHP**: Used to generate fake data, helpful for testing and database seeding.

### Routing and Middleware
- **Custom Middleware**: Used to handle multi-tenancy and secure routes for different tenants based on subdomains.

### API Development
- **RESTful API**: Built to manage users, sales orders, items, and generate QR codes.

### Version Control
- **Git**: Used for version control to track and manage project changes.

## API Routes

The API includes several routes for authentication, managing users, items, sales orders, and QR code generation.

### Authentication
- `POST /api/register`: Registers a new user.
- `POST /api/login`: Logs a user in.
- `POST /api/logout`: Logs the user out.

### Routes (Protected by Authentication)
- `GET /api/retrieve-token`: Retrieves a token for authenticated users.
- `GET /api/user`: Retrieves the authenticated user.
- `POST /api/upload-csv`: Uploads a CSV file with sales data.
- `GET /api/sales-order`: Lists all sales orders.
- `POST /api/sales-order/generate`: Generates a sales order report.
- `POST /api/qr-code/generate`: Generates a QR code.

### Item Routes (Protected by Authentication)
- `GET /api/items`: Retrieves all items.
- `POST /api/items`: Creates a new item.
- `PUT /api/items/{id}`: Updates an item.
- `DELETE /api/items/{id}`: Deletes an item.

### Tenant Routes
Tenant-specific routes must be accessed using subdomains and are secured by middleware to ensure data isolation.

## Architecture

### Multi-Tenancy with Shared Database and Shared Schema

The Sales Tracker API utilizes a **shared database and shared schema** approach to implement multi-tenancy. This means that all tenants' data is stored in the same database, but each tenant’s data is logically separated using a **tenant identifier**, typically via subdomains. The tenant's identifier is included in the API request headers and URL to ensure that the correct data is accessed.

This approach allows for efficient resource usage while maintaining logical isolation for each tenant's data.

## Folder Structure

Here is the folder structure for the Sales Tracker API project:

```
app/
├── Exceptions/             # Custom exceptions
├── Http/
│   ├── Controllers/       # Controllers for handling API requests
│   │   ├── Api/
│   │   │   ├── AuthController.php
│   │   │   ├── UserController.php
│   │   │   ├── ItemController.php
│   │   │   ├── QRCodeController.php
│   │   │   └── SalesController.php
│   ├── Middleware/        # Custom middleware (e.g., TenantMiddleware)
│   ├── Requests/           # Custom form Requests
├── Models/                 # Eloquent models for interacting with the database
│   ├── Item.php
│   ├── SalesOrder.php
│   └── User.php
├── Repositories/          # Service-Repository pattern implementation
│   ├── ItemRepository.php
│   └── SalesOrderRepository.php
├── Services/              # Business logic services
│   ├── QRCodeService.php
│   └── SalesOrderService.php
routes/
├── api.php                # API routes definition
.env                       # Environment configuration
composer.json              # Composer dependencies and configurations
phpunit.xml                # PHPUnit configuration
```

## Usage

After setting up the environment and installing dependencies, you can begin using the API by making requests to the defined routes. Make sure to include the appropriate authentication tokens in the headers when accessing protected routes. Subdomains are returned after successful login

Example of an authenticated request:

```bash
curl -X GET http://{subdomain}.localhost:8000/api/user -H "Authorization: Bearer <your_token>"
```
