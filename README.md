## 1. Project Setup

```bash
composer create-project laravel/laravel laravel-app
cd laravel-app
php artisan serve
```

* **Composer** is like `npm` but for PHP.
* `php artisan serve` runs a local PHP dev server (like `node app.js` or `nodemon`).

---

## 2. Routing

File: `routes/web.php` or `routes/api.php`

```php
// routes/api.php

use Illuminate\Support\Facades\Route;

Route::get('/hello', function () {
    return 'Hello from Laravel!';
});
```

* Similar to Express’s `app.get('/hello', ...)`.
* `routes/api.php` is for API routes (stateless, usually JSON).
* `routes/web.php` is for web routes (return views, sessions, etc).

---

## 3. Controllers

Generate a controller:

```bash
php artisan make:controller UserController
```

Create route that uses controller:

```php
use App\Http\Controllers\UserController;

Route::get('/users', [UserController::class, 'index']);
```

In `app/Http/Controllers/UserController.php`:

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(['users' => ['Alice', 'Bob']]);
    }
}
```

* Controller method like Express route handler.
* Laravel’s `Request` object is similar to Express’s `req`.
* Return JSON using `response()->json()`.

---

## 4. Request Validation

Laravel has built-in validation:

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
    ]);
    // $validated contains only valid data
    // Save user logic here
    return response()->json(['message' => 'User created!']);
}
```

* This automatically returns a JSON error response if validation fails.
* Similar to Express middleware like `express-validator`.

---

## 5. Models and Database (Eloquent ORM)

Laravel uses **Eloquent** as ORM.

Generate model + migration:

```bash
php artisan make:model User -m
```

This creates:

* `app/Models/User.php` (model class)
* `database/migrations/xxxx_xx_xx_create_users_table.php` (migration)

Migration example:

```php
public function up()
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamps();
    });
}
```

Run migration:

```bash
php artisan migrate
```

You can then use Eloquent to query:

```php
use App\Models\User;

$users = User::all();
return response()->json($users);
```

* Equivalent to Mongoose or Sequelize models in Express.

---

## 6. Routing Parameters and Dependency Injection

Get route parameter:

```php
Route::get('/users/{id}', [UserController::class, 'show']);
```

Controller method:

```php
public function show($id)
{
    $user = User::find($id);
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }
    return response()->json($user);
}
```

---

## 7. Middleware

Laravel middleware is like Express middleware.

Register middleware in `app/Http/Kernel.php`.

Use middleware in routes:

```php
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
```

You can write custom middleware:

```bash
php artisan make:middleware CheckAge
```

---

## 8. Authentication (Optional)

Laravel offers packages for auth like **Sanctum** or **Passport** for API tokens.

Basic example:

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

Then protect routes with `auth:sanctum` middleware.

---

## 9. Error Handling

Laravel automatically catches exceptions and formats JSON error responses for APIs.

You can customize in `app/Exceptions/Handler.php`.

---

## 10. Testing

Laravel supports PHPUnit and has nice helpers:

```bash
php artisan make:test UserTest
```

Example test:

```php
public function testUserListing()
{
    $response = $this->getJson('/api/users');
    $response->assertStatus(200)
             ->assertJsonStructure(['users']);
}
```

---

# Summary Table: Laravel vs Express

| Concept              | Laravel                                  | Express                                |
| -------------------- | ---------------------------------------- | -------------------------------------- |
| Routing              | `routes/api.php` + Controllers           | `app.get()` / `router.get()`           |
| Controllers          | Classes in `app/Http/Controllers`        | Functions (or classes)                 |
| Middleware           | Classes + registered in Kernel           | Functions registered in app            |
| ORM                  | Eloquent (ActiveRecord style)            | Mongoose, Sequelize, etc.              |
| Validation           | `$request->validate()`                   | Middleware libs like express-validator |
| Server start         | `php artisan serve`                      | `node app.js`                          |
| Dependency Injection | Controller methods with injected Request | Middleware/handler args (req, res)     |
| Config files         | `.env`, `config/*.php`                   | `.env` or config modules               |
| CLI tools            | `php artisan`                            | `npm scripts` or custom CLI            |

---

####---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
