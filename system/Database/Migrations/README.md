# BOOSTED MIGRATIONS MANAGER
- A FEW THINGS TO KNOW ABOUT THE BOOSTED MIGRATIONS MANAGER
- All migrations files should be stored under database/migrations directory

# MAKE MIGRATIONS
## The migration names should follow the create_migration_name_table format
- Eg create_users_table. The table names should be in plural form.
- `php manage make:migration create_users_table`
The created migration file will reside unde database/migartions folder
# RUN MIGRATIONS
- Run migrations `php manage migrate`
- Run a specific migration file `php manage migrate --file=migration_file_name`
- Group Migrations into 1 sql file  `php manage migrate:group`    
- Run grouped migration           `php mange migrate:group --run`
- Run Migartion modifications     `php manage migrate:modifiy`
- List Migrations                 `php manage migrate:list`
- Drop Migrations:                `php manage migrate:rollback` (This will drop all the migrations in the database)
- Drop and re-run migrations      `php manage migrate:refresh`
- Show Migration logs/errors     `php manage migrate:log`
- Clear Migration logs/errors     `php manage migrate:log --clear`

# For help
- `php manage --help` or `php manage -h`

# BMM version
- `php manage --version` or `php manage -v`
