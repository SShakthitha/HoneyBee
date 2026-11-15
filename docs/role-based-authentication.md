# HoneyBee role account provisioning

HoneyBee uses Laravel's existing `users` table for hashed credentials. Staff roles are stored in `staff.role`, and customer accounts are linked through `customers.email`.

The opt-in `HoneyBeeAccountsSeeder` creates or updates the three requested role accounts in a transaction. It preserves existing customer totals and staff contact details, marks these provisioned accounts as email verified, and never displays password values. Passwords are read only from process environment variables; they are not stored in source code.

Set these variables in the deployment environment or in the local `.env` file:

- `HONEYBEE_ADMIN_PASSWORD`
- `HONEYBEE_CUSTOMER_PASSWORD`
- `HONEYBEE_STAFF_PASSWORD`

Then run `php artisan db:seed --class=HoneyBeeAccountsSeeder`. The seeder is intentionally opt-in and is not called by the default `DatabaseSeeder`, so a routine `db:seed` will not reset these account passwords. Remove the variables from the environment after provisioning if they are no longer needed.

The database must have the project's migrations applied first, including the migration that makes `staff.business_id` nullable.
