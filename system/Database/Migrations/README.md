# BOOSTED MIGRATIONS MANAGER
- A FEW THINGS TO KNOW ABOUT THE BOOSTED MIGRATIONS MANAGER
- All migrations files should be stored under database/migrations directory. These are sql files that you export before submitting your changes.

# MIGRATE MIGRATIONS
- Run all migrations `php manage.php -migrate`
- Migrate a specific migration `php manage.php -m migration_name` (The migration name can be either the whole name of  the file or its unique id between the database name and the timestamp)
- Group Migrations `php manage.php -m group` Sometimes migration files may pile up, use this command to group them into a single file. All the existing migration files will be cleared.
- Clear Migrations `php manage.php -m clear` If you want to clear all migration, use this command. Warning: This command clears all migrations in the database.

# MAKE MIGRATIONS
- Make migrations for the entire database `php manage.php -export`
- Make migrations for a specific table `php manage.php -e table_name` This command will require you to specify if the supplied table name is database name or table name single single migrations are reserved for databases not in use.
- Make migrations for multiple tables `php manage.php -e table1,table3,table3...` Separate table names with commas without spaces between the table names.

# For help
- `php manage.php -help`

# BMM version
- `php manage.php -version`
