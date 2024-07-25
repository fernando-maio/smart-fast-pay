# Laravel Payment API

This project is a Laravel-based API for handling user authentication and payment processing. It follows best practices, including SOLID principles, and utilizes Swagger for API documentation.

## Installation

Follow these steps to set up the project on your local machine.

### Prerequisites

- PHP >= 8.2
- Composer
- Docker
- MySQL
- Git

### Clone the Repository

```sh
git clone git@github.com:fernando-maio/smart-fast-pay.git
cd smart-fast-pay
cp .env.example .env
```

### Running composer

```sh
composer install
```

### Setup the .env file

```sh
php artisan key:generate
```

### Setting up JWT

```sh
php artisan jwt:secret

# Check if the JWT_SECRET was created in your .env file
```

### Migrations ans Seeds

```sh
php artisan migrate --seed
```

### Run app:

```sh
php artisan serve
```


## API Documentation

This project uses Swagger for API documentation. You can view the API documentation at `http://your-app-url/api/documentation`.

### Setting up Swagger
1. Install Swagger using Composer.
```sh
composer require "darkaonline/l5-swagger"
```

2. Publish the Swagger configuration.
```sh
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

3. Generate the Swagger documentation.
```sh
php artisan l5-swagger:generate
```


## Project Structure

### Key Directories and Files

* app/Http/Controllers/: Contains the controllers for handling HTTP requests.
    - AuthController.php: Handles user authentication (register, login, logout, refresh token, get user info).
    - PaymentController.php: Manages payment-related actions (list, show, create payments).

* app/Http/Requests/: Custom request validation classes.
    - RegisterRequest.php: Validates registration data.

* app/Http/Resources/: Contains resources for transforming models into JSON.
    - PaymentResource.php: Transforms Payment model for API responses.

* app/Services/: Contains service classes that encapsulate business logic.
    - PaymentService.php: Handles payment processing logic.

* app/Interfaces/: Contains interfaces for service classes.
    - PaymentServiceInterface.php: Interface for the PaymentService.

* app/Exceptions/: Custom exception classes.
    - InvalidPaymentMethodException.php: Thrown when an invalid payment method is provided.
    - PaymentProcessingException.php: Thrown when there is an error in processing a payment.

* app/Models/: Eloquent models for database tables.
    - Payment.php: Model for the payments table.
    - PaymentMethod.php: Model for the payment_methods table.
    - User.php: Model for the users table.

* app/Enums/: Contains enumerations for standardized values.
    - PaymentStatus.php: Enum for payment statuses (pending, paid, expired, failed).
    - PaymentMethodSlug.php: Enum for payment method slugs (pix, boleto, bank_transfer).

* database/migrations/: Database migration files.
    - create_payments_table.php: Migration for creating the payments table.
    - create_payment_methods_table.php: Migration for creating the payment_methods table.
    - add_balance_to_users_table.php: Migration for adding the balance column to the users table.

* database/seeders/: Seeders for populating the database with initial data.
    - PaymentMethodSeeder.php: Seeds the payment_methods table with initial data.
    - UserSeeder.php: Seeds the users table with an initial merchant.

* config/payment_fees.php: Configuration file for payment method fees.


## Tests

* tests/Unit/: Contains unit tests for individual methods and functionalities.
    - PaymentServiceTest.php: Tests for PaymentService methods (createPayment, getAllPayments, getPaymentById).
    - AuthControllerTest.php: Tests for authentication methods (register, login, me, logout, refresh).

### Description of Tests

* PaymentServiceTest.php
    - testCreatePayment: Validates successful payment creation.
    - testCreatePaymentInvalidMethod: Validates handling of invalid payment methods.
    - testGetAllPayments: Validates retrieval of all payments for a merchant.
    - testGetPaymentById: Validates retrieval of a specific payment by ID.

* AuthControllerTest.php
    - testRegister: Validates user registration.
    - testLogin: Validates user login.
    - testMe: Validates retrieval of authenticated user information.
    - testLogout: Validates user logout.
    - testRefresh: Validates token refresh.

### Running the Tests

```sh
php artisan test
```