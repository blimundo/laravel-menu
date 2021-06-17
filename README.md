# Menu management for Laravel applications

Menu management in Laravel made simple and painless. This package does not provide any UI, focusing only on functionality to store and display the menu.

Items can be displayed in different languages thanks to the [spatie/laravel-translatable](https://github.com/spatie/laravel-translatable) package.

## Requirements

This package requires Laravel 8 or higher, PHP 8 or higher, and a database that supports json fields and MySQL compatible functions.

## Installation

You can install the package via composer:
```
composer require blimundo/laravel-menu
```
The package will automatically register itself.

After install you can create the menus table by running the migrations:
```
php artisan migrate
```

A MenuGenerator alias will be available to application.

## Example

```php
use Blimundo\Menu\Builder;

Builder::add('Home')->order(1)->icon('mdi mdi-home')->url('HomeController@show')->create();

Builder::add(['en'  =>  'Settings',  'pt'  =>  'Configurações'])
	->order(9)
	->icon('mdi mdi-cog')
	->items(function  ()  {
		Builder::add(['en'  =>  'Roles',  'pt'  =>  'Funções'])
		->order(1)
		->icon('mdi mdi-account-group')
		->url('role.index')
		->gates('can_view_roles')
		->create();

		Builder::add(['en'  =>  'Users',  'pt'  =>  'Utilizadores'])
		->order(2)
		->icon('mdi mdi-account')
		->url('user.index')
		->gates('can_view_users')
		->create();
	});

dd(MenuGenerator::generate());

/* Result:

array:2 [
  "Home" => array:5 [
    "icon" => "mdi mdi-home"
    "label" => "Home"
    "link" => "localhost:8000/home"
    "level" => 1
    "has_items" => false
  ]
  "Settings" => array:6 [
    "icon" => "mdi mdi-cog"
    "label" => "Settings"
    "link" => "#"
    "level" => 1
    "has_items" => true
    "items" => array:2 [
      "Roles" => array:5 [
        "icon" => "mdi mdi-account-group"
        "label" => "Roles"
        "link" => "localhost:8000/role"
        "level" => 2
        "has_items" => false
      ]
      "Users" => array:5 [
        "icon" => "mdi mdi-account"
        "label" => "Users"
        "link" => "localhost:8000/user"
        "level" => 2
        "has_items" => false
      ]
    ]
  ]
*/
```

## Persisting the menu in the database

To create and persist the menu in the database, you can use the Builder class. In the example below, two menu entries are created:

```php
use Blimundo\Menu\Builder;

Builder::add('Home')->create();
Builder::add('Help')->create();
```
#### Translations

To define multiple languages, just pass an array to the add() method, where the key is the language code. Internally the package uses [spatie/laravel-translatable](https://github.com/spatie/laravel-translatable) to manage translations.

```php
Builder::add(['en' => 'Help', 'pt' => 'Ajuda'])->create();
```

#### Url

You have 3 options to generate urls: 

 - route url (call Laravel route() helper - will rise exception in runtime if route doesn't exist) 
 - action url (call Laravel action() helper - will rise exeption in runtime if controller or method doesn't exist)
 - static url

```php
Builder::add('Google')->url('https://google.com')->create();
Builder::add('Add User')->route('users.create')->create();
Builder::add('Import User')->action('\App\Http\Controllers\UserController@import')->create();
```
#### Icon

To associate an icon to the menu just call the icon() function

```php
Builder::add('Help')->icon('mdi mdi-help')->create();
```


#### Order

To set the item order just call the order() function.

```php
Builder::add('Help')->order(2)->create();
```

Items with the same order (or that have no defined order) are sorted alphabetically according to the current language. 

#### Gates

Sometimes we want the menu to appear only if the user has certain permissions. This package makes this a breeze. Just call the gates() function with the name of the gate. When generating the menu, the package tests the gate and if it passes the menu is included.

```php
Builder::add('Add User')->gates('can_add_user')->create();
```

If you need to test more than one gate, just pass a list. The menu is only displayed if all gates return true.

```php
Builder::add('Add User')->gates('can_add_user', 'can_import_user')->create();
```

#### Submenu

To create a submenu call the items() function, passing a callback. All menus created within the callback will be associated with the menu.

```php
Builder::add('Settings')->items(function  ()  {
	Builder::add('Roles')->create();
	Builder::add('Users')->create();
});
```

## License

The Laravel Menu is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
