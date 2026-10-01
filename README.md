Payment System

A Laravel-based payment management system for handling merchants, wallets, pay-ins, payouts, payment processing, and transaction logs.

Features

Merchant management

Automatic wallet creation for merchants

Pay-in initiation

Payout initiation

Unique transaction IDs

Payment status management:

PENDING

SUCCESS

FAILED

Automatic wallet credit on successful pay-in

Automatic wallet debit on successful payout

Insufficient balance handling for payouts

Pending payment retry mechanism

Scheduled payment processing

Duplicate payment processing protection

Payment request and processing logs

Form validation

Feature tests

Bootstrap-based admin dashboard

Tech Stack

PHP 8.2+

Laravel

MySQL

Bootstrap 5

PHPUnit / Laravel Testing

Project Structure

app/
├── Console/
│   └── Commands/
│       └── ProcessPendingPayments.php
├── Enums/
│   └── PaymentStatus.php
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── PayInController.php
│   │   │   └── PayoutController.php
│   │   ├── MerchantController.php
│   │   ├── PayInController.php
│   │   ├── PayoutController.php
│   │   └── PaymentLogController.php
│   └── Requests/
│       ├── StorePayInRequest.php
│       └── StorePayoutRequest.php
├── Models/
│   ├── Merchant.php
│   ├── Wallet.php
│   ├── PayIn.php
│   ├── Payout.php
│   └── PaymentLog.php
└── Services/
    ├── PaymentService.php
    ├── PaymentProcessingService.php
    └── WalletService.php

Database

The application uses the following main tables:

Merchants

Stores merchant information including:

Name

Email

API key

Status

Wallets

Stores the wallet balance for each merchant.

Each merchant has one wallet.

Pay-ins

Stores incoming payment transactions with:

Transaction ID

Merchant

Amount

Customer reference

Status

Request payload

Processed timestamp

Payouts

Stores outgoing payment transactions with the same transaction tracking information.

Payment Logs

Stores payment initiation and processing events for auditing and debugging.

Installation

Clone the repository and navigate to the project directory:

git clone <repository-url>
cd payment-system

Install PHP dependencies:

composer install

Create the environment file:

copy .env.example .env

Generate the application key:

php artisan key:generate

Configure the database in .env:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=payment_system
DB_USERNAME=root
DB_PASSWORD=

Create the database in MySQL, then run:

php artisan migrate

Seed the test merchant:

php artisan db:seed

Start the Laravel development server:

php artisan serve

Application:

http://localhost:8000

Test Merchant

The database seeder creates a sample merchant with:

Name: Test Merchant
Email: test@merchant.com
Status: ACTIVE
Initial Wallet Balance: 10000.00

Web Dashboard

The application provides the following pages:

Dashboard
/merchants
/pay-ins
/payouts
/payment-logs

From the dashboard, merchants can be created and pay-ins/payouts can be initiated through the Bootstrap UI.

API Endpoints

Initiate Pay-in

POST /api/pay-ins

Request:

{
    "merchant_id": 1,
    "amount": 500,
    "customer_reference": "ORDER-1001"
}

Example response:

{
    "message": "Pay-in initiated successfully.",
    "data": {
        "transaction_id": "PI-XXXXXXXX",
        "merchant_id": 1,
        "amount": "500.00",
        "status": "PENDING"
    }
}

Initiate Payout

POST /api/payouts

Request:

{
    "merchant_id": 1,
    "amount": 300,
    "customer_reference": "ORDER-2001"
}

Example response:

{
    "message": "Payout initiated successfully.",
    "data": {
        "transaction_id": "PO-XXXXXXXX",
        "merchant_id": 1,
        "amount": "300.00",
        "status": "PENDING"
    }
}

Payment Processing

New pay-ins and payouts are initially created with:

PENDING

A scheduled command processes pending payments.

The processing result is randomly determined as:

SUCCESS
FAILED
PENDING

If the result is PENDING, the payment remains pending and will be retried during the next scheduled execution.

Wallet Processing

Successful Pay-in

A successful pay-in credits the merchant wallet.

Example:

Wallet Balance: 10000.00
Pay-in:          500.00
New Balance:    10500.00

Successful Payout

A successful payout debits the merchant wallet.

Example:

Wallet Balance: 10500.00
Payout:          300.00
New Balance:    10200.00

If the wallet does not have sufficient balance, the payout is marked as FAILED and the wallet balance remains unchanged.

Duplicate Processing Protection

Payment processing uses database transactions and row-level locking.

Before processing a payment, the payment record is locked and checked to ensure it is still in PENDING status.

Once a payment has reached SUCCESS or FAILED, it cannot be processed again.

This prevents duplicate wallet credit/debit operations.

Scheduler

The payment processing command is:

php artisan payments:process

The scheduler is configured to run every minute.

For local development, run:

php artisan schedule:work

To view the configured schedule:

php artisan schedule:list

Testing

Run the complete test suite:

php artisan test

The current test suite covers:

Pay-in initiation

Pay-in validation

Successful pay-in wallet credit

Pay-in logging

Pay-in retry

Payout initiation

Payout validation

Successful payout wallet debit

Insufficient wallet balance

Duplicate payout protection

Payout logging

Payout retry

Code Design

The application separates responsibilities using:

Form Requests for validation

Controllers for handling HTTP requests

Services for payment and wallet business logic

Eloquent Models for database relationships

Console Commands for scheduled payment processing

Database Transactions for atomic payment operations

Enums for payment statuses

Payment Logs for transaction auditing

This keeps business logic out of controllers and makes the payment processing flow easier to test and maintain.

License

This project was developed as a Laravel payment system assignment.