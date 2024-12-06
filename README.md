# Boycott Israeli Tech

## Install the Project

### ### 1. Download the binaries for image optimization

if you are in ubuntu

    ```bash
    sudo apt-get install jpegoptim
    sudo apt-get install optipng
    sudo apt-get install pngquant
    sudo apt-get install webp
    ```

or go to [this link](https://github.com/spatie/image-optimizer?tab=readme-ov-file) for other operating systems.

### 2. clone the repo

### 3. Install dependencies

-   for the backend `composer install`
-   and for the frontend `npm install`

> You can use other like `yarn` or `pnpm` if you prefer

### 4.Generate the key

run `php artisan key:generate`

### 5. Set up the database

-   run `touch database/database.sqlite` to create database file
-   run `php artisan migrate:fresh --seed`
-   run `php artisan import:all`

4. run `npm run dev` to run vite

### 6. run the project

-   for the backend you can use `php artisan serve`

> if you are using valet or herd you can link the site and no need for `php artisan serve`

-   for the frontend run `npm run dev`

> or `yarn dev` or `pnpm install` if you prefer

### 7. run the queue

-   run `php artisan queue:work` to enable queues in the system

> you can run `php artisan queue:listen` if you want queue to be interactive with queue related changes

### 8. if You want real-world data run these commands to import the data

-   run `php artisan import:all`

> This will not give you images because they are heavy

### 9. if You want to download images remove comments from the following commands from `ImportAll` command

```php
    //Artisan::call(AttachCompaniesImagesCommand::class);
    //$progressBar->advance();

    //Artisan::call(AttachPeopleImagesCommand::class);
    //$progressBar->advance();

    //Artisan::call(CleanNotesFromImagesUrlCommand::class);
    //$progressBar->advance();
```

> This will download some of the companies and people images to be as close as possible to the online system
