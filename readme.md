# Laravel Messages
This package will allow you to add a full user messaging system into your Laravel application.

## Features
* Multiple conversations per user
* Optionally loop in additional users with each new message
* View the last message for each thread available
* Returns either all messages in the system, all messages associated to the user <!-- or all message associated to the user with new/unread messages -->
* Return the users unread message count easily
* Very flexible usage so you can implement your own access control

## Common uses
* Open threads (everyone can see everything)
* Group messaging (only participants can see their threads)
* One to one messaging (private or direct thread)

## Installation (Laravel 5.x)
```
composer require chrisidakwo/laravel-messages
```

Or place manually in composer.json:

```bash
"require": {
    "chrisidakwo/laravel-messages": "~1.0"
}
```

Run:

```bash
composer update
```

Add the service provider to `config/app.php` under `providers`:

```php
'providers' => [
    ChrisIdakwo\Messages\MessagesServiceProvider::class,
],
```

> **Note**: If you are using Laravel 5.5, this step is unnecessary. Laravel Messages supports [Package Discovery](https://laravel.com/docs/5.5/packages#package-discovery).

Publish config:

```bash
php artisan vendor:publish --provider="ChrisIdakwo\Messages\MessagesServiceProvider" --tag="config"
```
	
Update config file to reference your User Model:

```bash
config/messages.php
```


    
Publish migrations:

```bash
php artisan vendor:publish --provider="ChrisIdakwo\Messages\MessagesServiceProvider" --tag="migrations"
```

Migrate your database:

```bash
php artisan migrate
```

Add the trait to your user model:

```php
use ChrisIdakwo\Messages\Traits\HasMessages;

class User extends Authenticatable {
    use HasMessages;
}
```

## Contributing? 
Please format your code before creating a pull-request. This will format all files as specified in `.php_cs`:

```bash
vendor/bin/php-cs-fixer fix .
```

## Security

If you discover any security related issues, please email [Chris Idakwo](mailto:chris.idakwo@gmail.com) instead of using the issue tracker.

## Credits

- [Chris Idakwo](https://github.com/chrisidakwo)

### Special Thanks
This package used [cmgmyr/laravel-messenger](https://github.com/cmgmyr/laravel-messenger) as a starting point.
