# Guestbook test

>This is just a test repo

### Installation

1. Clone this repo:
    `git clone git@github.com:magnus-eriksson/guestbook-test`

2. Copy the default config-file: `cp config.defaults.php config.php`

3. Edit the new `config.php` file with your MySQL/MariaDB credentials.

4. Create the necessary tables using the `tables.sql`-file.

### Web server settings

The sites document root should point to the `public/`-folder.

##### Apache
There's a .htaccess-file included. Make sure you have `mod_rewrite` enabled and that the vhost has `AllowOverride All`.

##### All others
Make sure all requests are sent to the `index.php`-file. Don't forget to pass the query string along with the request though.


### Front end

The front end is built with gulp and all source files can be found in `front_src/`. However, the generated files are included so no need to build anything. I'm just using gulp so I can structure the js-code easier and use sass. Everything is just vanilla js without any libs.


Have fun!
