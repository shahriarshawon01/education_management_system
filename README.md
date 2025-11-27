# Education Management System

## Getting Started

### Installation

Before you begin, please ensure your server meets the requirements specified in the official Laravel installation guide. [Official Documentation](https://laravel.com/docs/9.x/installation)

#### Step-by-Step Installation

1. **Clone the Repository**

   ```bash
   git clone https://github.com/tmssictsoft/education
   ```

2. **Navigate to the Repository Folder**

   ```bash
   cd education
   ```

3. **Install PHP Dependencies**

   ```bash
   composer install
   ```

4. **Set Up the Environment File**

   Copy the example `.env` file and make necessary changes, such as database configuration:

   ```bash
   cp .env.example .env
   ```

5. **Generate the Application Key**

   ```bash
   php artisan key:generate
   ```

6. **Install Node.js Dependencies**

   ```bash
   npm install
   ```

7. **Run Database Migrations**

   Ensure the database connection is correctly set in your `.env` file before running migrations:

   ```bash
   php artisan migrate
   ```

8. **Start Development Servers**

   Run the Laravel development server and watch for frontend changes:

   ```bash
   php artisan serve
   npm run watch
   ```

9. **Access the Application**

   Open your browser and navigate to [http://localhost:8000](http://localhost:8000).

#### TL;DR Command List

```bash
git clone https://github.com/tmssictsoft/education
cd education
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

### Environment Variables

Ensure you configure the correct database connection information in the `.env` file before proceeding with migrations. Refer to the [Laravel Environment Variables Guide](https://laravel.com/docs/9.x/configuration#environment-configuration) for more details.

## Database Seeding

### Populate the Database

To populate the database with seed data, follow these steps:

1. Open the `DatabaseSeeder` file located at:

   ```
   database/seeders/DatabaseSeeder.php
   ```

   Update the property values as needed for your requirements.

2. Run the seeder command:

   ```bash
   php artisan db:seed
   ```

### Refresh Migrations

If you need a clean database before seeding, refresh your migrations by running:

```bash
php artisan migrate:refresh
```

This command will:
- Rollback all migrations.
- Re-run the migrations.

### Notes

- Always back up your database before performing destructive actions.
- Seeding is best performed on a clean database to avoid conflicts.

---

You're now ready to build and manage your Education Management System! If you encounter issues, refer to the Laravel documentation or reach out for support.
