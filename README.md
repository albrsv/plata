# Plata

A financial dashboard application built with **Laravel 11**, **Vue.js 3**, **Vite**, and **MySQL**.  
This app manages users, their balances, and transactions.

---

## Features

- **User, Balance, Transaction Models**
- **Automatic Balance Creation:** When a user is created, balances are generated automatically.
- **Transaction processing is handled asynchronously in a queue using jobs**
- **API-first:** All backend responses are JSON.
- **Authentication is cookie-based (session via Laravel Sanctum SPA mode).**
- **Vue 3 SPA:** Responsive dashboard with sorting, searching, and authentication.
- **Dockerized:** Runs easily with [Laravel Sail](https://laravel.com/docs/11.x/sail).

---

## Getting Started

### 1. Clone the Repository

```bash
git clone https://github.com/albrsv/plata.git
cd plata
```

### 2. Copy Environment Files

Copy environment files for both the backend (Laravel) and frontend (Vue):

```bash
# In the project root (for Laravel)
cp .env.example .env

# In the frontend directory (for Vue)
cd frontend
cp .env.example .env
cd ..

# Edit `.env` as needed (database, url, etc).
```

### 3. Install Dependencies

#### Backend (Laravel)
```bash
composer install
```

#### Frontend (Vue)
```bash
cd frontend
npm install
cd ..
```

### 4. Start the Application (with Sail)

```bash
./vendor/bin/sail up -d
```

### 5. Run Migrations & Seeders

```bash
./vendor/bin/sail artisan migrate --seed
```

### 6. Build Frontend

```bash
cd frontend
npm run build
```

Or for development:

```bash
cd frontend
npm run dev
```

---

## Artisan Commands

- **Create User:**
  ```bash
  ./vendor/bin/sail artisan app:create-user
  ```
  This will create a new user and automatically generate balances for them.

- **Create Transaction:**
  ```bash
  ./vendor/bin/sail artisan app:create-transaction
  ```
  This will create a transaction for a user.

---

## Factories & Seeders

- The app uses a `UserFactory` for seeding users and their balances.
- Run `artisan db:seed` to populate the database with test data.

---

## Tech Stack

- **Backend:** Laravel 11, MySQL
- **Frontend:** Vue.js 3, Vite, Pinia, Bootstrap 5
- **Dev Tools:** Laravel Sail (Docker), artisan commands, factories