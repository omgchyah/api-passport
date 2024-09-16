Laravel API with Passport, MySQL, and Docker or Composer

This is a Laravel 11 API using PHP, MySQL, and Laravel Passport for authentication. It provides user registration, login, dice games for players, and an admin-specific player ranking system.

You can run this project either with Docker (using Laravel Sail) or without Docker using Composer and your local PHP setup.

Requirements

Docker-Based Setup

    Docker
    Docker Compose

Composer-Based Setup

    PHP 8.1 or higher
    Composer
    MySQL

# Running the Application with Composer (Without Docker) #

This option uses PHP, Composer, and MySQL directly on your local machine.

Step 1: Clone the Repository

git clone -b develop https://github.com/omgchyah/api-passport.git
cd api-passport

Step 2: Install Composer Dependencies

Make sure Composer is installed on your system, then run:

composer install

Step 3: Set Up Environment Variables

Copy the .env.example to .env and update the environment variables to match your local setup, particularly for MySQL.

cp .env.example .env

In the .env file, make sure the following variables are set correctly:

env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

Step 4: Generate Application Key

Run the following command to generate the application key:

php artisan key:generate

Step 5: Set Up the Database

Create a MySQL database for your project. Once the database is set up, run the migrations:

php artisan migrate

Step 6: Install Passport

Run the following command to install Laravel Passport:

php artisan passport:install

Step 7: Serve the Application

You can serve the application locally using PHP's built-in server:

php artisan serve

Your application will now be available at http://localhost:8000.

# Running the Application with Docker #

This option uses Docker and Laravel Sail to manage the environment.

Step 1: Clone the Repository

git clone -b develop https://github.com/omgchyah/api-passport.git
cd api-passport

Step 2: Set Up the Environment

Copy the .env.example file to .env and update the environment variables as needed:

cp .env.example .env

Step 3: Install Dependencies

Run the following command to install Composer dependencies within Docker:

./vendor/bin/sail composer install

Step 4: Start Docker Containers

Start the Docker containers with Laravel Sail:

./vendor/bin/sail up -d

Step 5: Run Migrations

./vendor/bin/sail artisan migrate

Step 6: Install Passport

./vendor/bin/sail artisan passport:install