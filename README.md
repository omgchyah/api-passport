# Laravel API with Passport, MySQL, Postman, and Docker or Composer 🚀🚀

This is a Laravel 11 API using PHP, MySQL, and Laravel Passport for authentication. It provides user registration, login, dice games for players, and an admin-specific player ranking system.

You can run this project either with Docker (using Laravel Sail) or without Docker using Composer and your local PHP setup.

# Requirements 🔧

# 🐳 Docker-Based Setup:

    Docker
        Docker Compose

# 🎼 Composer-Based Setup:

    PHP 8.1 or higher
    Composer
    MySQL
  

### 🛠️ Running the Application with Composer 🎼(Without Docker) 

    This option uses PHP, Composer, and MySQL directly on your local machine.

# Step 1: Clone the Repository

    git clone -b develop https://github.com/omgchyah/api-passport.git
    cd api-passport

# Step 2: Install Composer Dependencies

    Make sure Composer is installed on your system, then run:

    composer install

# Step 3: Set Up Environment Variables

    Copy the .env.example to .env and update the environment variables to match your local setup, particularly for MySQL.

    cp .env.example .env

    In the .env file, make sure the following variables are set correctly:

    env

    # Database Configuration
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1               # Use localhost or the IP address of your MySQL server
    DB_PORT=3306                    # Default MySQL port
    DB_DATABASE=your_database_name   # Replace with the name of your database
    DB_USERNAME=your_database_user   # Replace with your MySQL username
    DB_PASSWORD=your_database_password  # Replace with your MySQL password

# Step 4: Generate Application Key

    Run the following command to generate the application key:

    php artisan key:generate

# Step 5: Set Up the Database

    Create a MySQL database for your project. Once the database is set up, run the migrations:

    php artisan migrate

# Step 6: Install Passport

    Run the following command to install Laravel Passport:

    php artisan passport:install

# Step 7: Serve the Application

    You can serve the application locally using PHP's built-in server:

    php artisan serve

    Your application will now be available at http://localhost:8000.

### 🐳 Running the Application with Docker

    This option uses Docker and Laravel Sail to manage the environment.

# Step 1: Clone the Repository

    git clone -b develop https://github.com/omgchyah/api-passport.git
    cd api-passport

# Step 2: Set Up the Environment

    Copy the .env.example file to .env and update the environment variables as needed:

    cp .env.example .env

# Step 3: Install Dependencies

    Run the following command to install Composer dependencies within Docker:

    ./vendor/bin/sail composer install

# Step 4: Start Docker Containers

    Start the Docker containers with Laravel Sail:

    ./vendor/bin/sail up -d

# Step 5: Run Migrations

    ./vendor/bin/sail artisan migrate

# Step 6: Install Passport

    ./vendor/bin/sail artisan passport:install

### 📫 Using Postman to Test the API

    Postman is a great tool to interact with and test your API. Here's how you can set up Postman:

1. Set Up Postman for Authentication

    For any request that requires authentication, you must include the Bearer token in the Authorization header. To get the token:

        Register a new user using the /api/players endpoint.
        Log in using /api/login and retrieve the token from the response.

    In Postman, set the Authorization tab to Bearer Token, and paste the token received from the login response.

2. Use the Accept: application/json Header

    #For all requests, make sure to include the Accept: application/json header in Postman. This ensures the API returns JSON responses.

3. Example Postman Request

        Method: POST
        URL: http://localhost:8000/api/login
        Headers:
            Accept: application/json
        Body (raw, JSON format):

    json
    {
    "email": "your_email@example.com",
    "password": "your_password"
    }

    You will get a response with an authentication token. Use this token for authenticated requests.

 ### 🛣️ API Endpoints

# 🔑 Here are the key API routes for the Laravel Passport-based authentication system:

    Authentication

# Register a new player: POST /api/players

        Example body:

        json
    {
      "username": "TestUser",
      "email": "test@example.com",
      "password": "password"
    }

# Login and get a token: POST /api/login

    Example body:

    json

        {
          "email": "test@example.com",
          "password": "password"
        }

# Logout and revoke token: GET /api/logout

# 🔒 Protected Player Routes (With Bearer Token)

    These routes require a valid bearer token in the Authorization header.

# Update player's name: PATCH /api/players/{id}
        Example body:

        json

        {
          "username": "NewUsername"
        }

# Get current player's profile: GET /api/players/profile

# Throw dice: POST /api/players/{id}/games
        Simulates a dice throw for the specified player.

# Get player's games: GET /api/players/{id}/games
        Returns a list of dice games for the specified player.

# Delete player's games: DELETE /api/players/{id}/games
        Deletes all game history for the specified player.

# 👑 Admin Routes (Admin Only) 👑

These routes are only accessible to users with the admin role.

# Get all players: GET /api/players
# Get player ranking: GET /api/players/ranking
# Get the player with the most losses: GET /api/players/ranking/loser
# Get the player with the most wins: GET /api/players/ranking/winner

 # ✅ Running Tests ✅

You can run the feature and unit tests for the API in two ways depending on your setup:

### 🐳 With Docker (Using Laravel Sail)

./vendor/bin/sail artisan test

### 🎼 Without Docker (Using Composer)

php artisan test