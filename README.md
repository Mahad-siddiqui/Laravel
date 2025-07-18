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

##------------------------------------------------------------------------------------------------------------------------------------------------
Perfect! Moving your Todo app from a static array to a **database** with Laravel’s Eloquent ORM:

* Create a database migration for `todos` table
* Create a `Todo` model
* Update your controller to use the database instead of the static array
* Test the new API endpoints

---

# Step 1: Configure Database

1. Open your `.env` file in the project root.
2. Set your database connection details, e.g., for MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Make sure you create the database (`your_database_name`) beforehand using phpMyAdmin or MySQL CLI.

---

# Step 2: Create Migration for Todos Table

Run this command to create a migration:

```bash
php artisan make:migration create_todos_table --create=todos
```

Open the new migration file in `database/migrations/xxxx_xx_xx_create_todos_table.php` and update it like this:

```php
public function up()
{
    Schema::create('todos', function (Blueprint $table) {
        $table->id();
        $table->string('task');
        $table->boolean('completed')->default(false);
        $table->timestamps();
    });
}
```

Run migration to create the table:

```bash
php artisan migrate
```

---

# Step 3: Create Todo Model

Run:

```bash
php artisan make:model Todo
```

Open `app/Models/Todo.php` and ensure it looks like this:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = ['task', 'completed'];
}
```

`$fillable` allows mass assignment for those fields.

---

# Step 4: Update Controller to Use Eloquent

Open your `app/Http/Controllers/TodoController.php`.

Replace the static array logic with database queries:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    // List all todos
    public function index()
    {
        $todos = Todo::all();
        return response()->json($todos);
    }

    // Create a new todo
    public function store(Request $request)
    {
        $request->validate([
            'task' => 'required|string|max:255',
        ]);

        $todo = Todo::create([
            'task' => $request->task,
            'completed' => false,
        ]);

        return response()->json($todo, 201);
    }

    // Show a single todo
    public function show($id)
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return response()->json(['message' => 'Todo not found'], 404);
        }

        return response()->json($todo);
    }

    // Update a todo
    public function update(Request $request, $id)
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return response()->json(['message' => 'Todo not found'], 404);
        }

        $request->validate([
            'task' => 'sometimes|string|max:255',
            'completed' => 'sometimes|boolean',
        ]);

        $todo->update($request->only(['task', 'completed']));

        return response()->json($todo);
    }

    // Delete a todo
    public function destroy($id)
    {
        $todo = Todo::find($id);

        if (!$todo) {
            return response()->json(['message' => 'Todo not found'], 404);
        }

        $todo->delete();

        return response()->json(['message' => 'Todo deleted']);
    }
}
```

---

# Step 5: Test Your API

Start the Laravel server:

```bash
php artisan serve
```

Use Postman or curl to test your CRUD endpoints as before — now the data is stored in the database!

---

# Summary:

| Step         | Command/File               | Purpose                       |
| ------------ | -------------------------- | ----------------------------- |
| Configure DB | `.env`                     | Set DB credentials            |
| Migration    | `make:migration` + migrate | Create `todos` table          |
| Model        | `make:model Todo`          | Represent todos in DB         |
| Controller   | Modify to use Eloquent     | CRUD with DB instead of array |
| Test         | Postman or curl            | Verify everything works       |

---
Here's a **clean, organized step-by-step guide** for creating a **Todo API in Laravel**, starting with a static array and moving to a **MySQL database**:

---

# ✅ Phase 1: Create Laravel Todo API (Static Data)

---

### 🔹 Step 1: Create a Controller for Todos

Run the following command:

```bash
php artisan make:controller TodoController
```

---

### 🔹 Step 2: Write Controller Methods

Open `app/Http/Controllers/TodoController.php` and define methods: `index`, `store`, `show`, `update`, `destroy`.

---

### 🔹 Step 3: Define API Routes

Open `routes/api.php` and add:

```php
use App\Http\Controllers\TodoController;

Route::get('/todos', [TodoController::class, 'index']);          // List all
Route::post('/todos', [TodoController::class, 'store']);         // Create new
Route::get('/todos/{id}', [TodoController::class, 'show']);      // Show one
Route::put('/todos/{id}', [TodoController::class, 'update']);    // Update one
Route::delete('/todos/{id}', [TodoController::class, 'destroy']); // Delete one
```

---

### 🔹 Step 4: Run the Laravel Server

```bash
php artisan serve
```

This will start the server at:
📍 [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

### 🔹 Step 5: Clear Cache (if routes not working)

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

---

### 🔹 Step 6: View Routes (Optional)

```bash
php artisan route:list
```

---

## ✅ Phase 2: Move from Static Array to MySQL Database

---

### 🔹 Step 1: Configure Database

In `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Make sure the database is created using **phpMyAdmin** or **MySQL CLI**.

---

### 🔹 Step 2: Create Migration for `todos` Table

```bash
php artisan make:migration create_todos_table --create=todos
```

Update the migration file:

```php
public function up()
{
    Schema::create('todos', function (Blueprint $table) {
        $table->id();
        $table->string('task');
        $table->boolean('completed')->default(false);
        $table->timestamps();
    });
}
```

Run migration:

```bash
php artisan migrate
```

---

### 🔹 Step 3: Create the `Todo` Model

```bash
php artisan make:model Todo
```

Open `app/Models/Todo.php` and update:

```php
class Todo extends Model
{
    use HasFactory;

    protected $fillable = ['task', 'completed'];
}
```

---

### 🔹 Step 4: Update `TodoController` to Use the Database

Use Eloquent instead of a static array. Here’s a basic example for all CRUD operations:

```php
use App\Models\Todo;
use Illuminate\Http\Request;

public function index() {
    return response()->json(Todo::all());
}

public function store(Request $request) {
    $request->validate([
        'task' => 'required|string|max:255',
    ]);
    $todo = Todo::create(['task' => $request->task, 'completed' => false]);
    return response()->json($todo, 201);
}

public function show($id) {
    $todo = Todo::find($id);
    return $todo ? response()->json($todo) : response()->json(['message' => 'Not found'], 404);
}

public function update(Request $request, $id) {
    $todo = Todo::find($id);
    if (!$todo) return response()->json(['message' => 'Not found'], 404);

    $todo->update($request->only(['task', 'completed']));
    return response()->json($todo);
}

public function destroy($id) {
    $todo = Todo::find($id);
    if (!$todo) return response()->json(['message' => 'Not found'], 404);

    $todo->delete();
    return response()->json(['message' => 'Deleted']);
}
```

---

### 🔹 Step 5: Test Your API

Use **Postman** or **curl** to test:

| Method | URL               | Purpose           |
| ------ | ----------------- | ----------------- |
| GET    | `/api/todos`      | List all todos    |
| POST   | `/api/todos`      | Create new todo   |
| GET    | `/api/todos/{id}` | Get specific todo |
| PUT    | `/api/todos/{id}` | Update todo       |
| DELETE | `/api/todos/{id}` | Delete todo       |

--- 
# 🎓 Final Flow Overview

- .env file      => Connects Laravel to database
- Migration      => Defines DB structure
- php artisan migrate => Applies structure to real DB
- Model (Todo)   => Laravel's code interface for the table
- Controller     => Uses the Model to handle requests (CRUD)
- Routes         => Connects API endpoints to controller actions


