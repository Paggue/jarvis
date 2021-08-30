### References
 - https://laravelpackage.com/
 - https://packages.tools/testbench/

### Install
- php artisan jarvis:install
- php artisan jarvis:seed

#### Config
- php artisan vendor:publish --provider="Lara\Jarvis\Providers\JarvisServiceProvider" --tag="jarvis-config"

#### Migrations
- php artisan vendor:publish --provider="Lara\Jarvis\Providers\JarvisServiceProvider" --tag="jarvis-migrations"

#### Seeders
- php artisan vendor:publish --provider="Lara\Jarvis\Providers\JarvisServiceProvider" --tag="jarvis-seed"

### PIX payload
https://github.com/fabiosiqueira12/pix-qrcode-estatico

###SETUP
 1. Add "lara/jarvis": "dev-main" to required dependencies in *composer.json*
 2. Add "repositories": [ { "type": "path", "url": "./packages/Lara/jarvis" } ] at the end of your *composer.json*

####Obs.: Inside the project, use "./vendor/bin/testbench" instead of the artisan command
