
## Laravel
1. Download the project’s zip then copy and paste volt-dashboard-master folder in your projects folder. Rename the folder to your project’s name
2. Make sure you have Node and Composer locally installed.
3.Run the following command in order to download all the project dependencies. `composer install`
4. In your terminal run `npm install`
5. Copy `.env.example` to `.env` and updated the configurations (mainly the database configuration)
6. In your terminal run `php artisan key:generate`
7. Run `php artisan migrate --seed` to create the database tables and seed the roles and users tables
8. Run `php artisan storage:link` to create the storage symlink (if you are using **Vagrant** with **Homestead** for development, remember to ssh into your virtual machine and run the command from there).




