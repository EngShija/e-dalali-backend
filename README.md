E-Dalali Backend API (Laravel)
🏠 Project Overview
This repository contains the robust backend API for the E-Dalali property management and listing application. It is built using Laravel (PHP framework) and leverages a MySQL database.

The API provides secure Authentication using Laravel Sanctum (or similar JWT implementation) and the core resources for managing property listings, ensuring role-based access for different user types (owners/Customers).

🛠️ Technology Stack
Component

Technology

Description

Framework

Laravel (PHP)

Provides the MVC structure, routing, and ORM (Eloquent).

Database

MySQL / MariaDB

Relational database for persistent storage of users, owners, and listings.

Authentication

Laravel Sanctum / JWT

Handles secure stateless API token generation for mobile/web frontends.

Data Structure

Eloquent Migrations

Defines the structured database schema for all entities.

🚀 Getting Started
Prerequisites
You must have the following installed on your system:

PHP (v8.1+)

Composer

MySQL/MariaDB Database Server

Laravel Installer (optional)

1. Installation
Clone the repository and install the PHP dependencies via Composer:

git clone [YOUR_REPO_URL] e-dalali-backend
cd e-dalali-backend
composer install

2. Configuration (.env file)
Copy the example environment file and configure your database and security settings.

cp .env.example .env

Key .env Variables to Update:

# Database Settings
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_dalali_db
DB_USERNAME=root
DB_PASSWORD=

# Application Key
# php artisan key:generate
APP_KEY=base64:YOUR_APP_KEY_HERE=

3. Database Setup
Once your database is created and configured in .env, run the migrations to set up the necessary tables:

php artisan migrate

This will create tables including: users, owners, and listings.

4. Running the Server
Start the local Laravel development server:

php artisan serve
# Server running on [http://127.0.0.1:8000](http://127.0.0.1:8000)

🔑 API Endpoints
All endpoints are accessible via the /api route group.

Authentication
These endpoints handle user access and token generation.

Method

Endpoint

Description

Guard/Middleware

POST

/api/register

Creates a new user account.

guest

POST

/api/login

Authenticates user and returns a token.

guest

POST

/api/logout

Invalidates the user's current token.

auth:sanctum

Required Body for Login:

{
    "email": "user@example.com",
    "password": "secretpassword"
}

Property Listings (Resource)
This resource is primarily controlled by the Owner user role and is typically protected by authentication middleware.

Method

Endpoint

Description

Guard/Middleware

GET

/api/listings

Retrieve a list of all available listings.

public access

POST

/api/listings

Create a new listing.

auth:sanctum, role:owner

GET

/api/listings/{id}

Retrieve a single listing detail.

public access

PUT/PATCH

/api/listings/{id}

Update an existing listing.

auth:sanctum, role:owner

DELETE

/api/listings/{id}

Delete a listing.

auth:sanctum, role:owner

Database Schema Highlights
The application uses these main tables:

Table

Purpose

Key Fields

owners

Stores property owner information.

id, name, email

listings

Stores all property details.

id, owner_id (FK), title, price, property_type, status
