# Rinku-Backend AI Instructions

## Architecture Overview
- **Type:** Custom PHP backend framework.
- **Entry Point:** `index.php` handles CORS, suppresses display errors, and delegates to `server\classes\request`.
- **Routing:** `server\classes\request` parses JSON input. `endPoint` property determines the module class to load (e.g., `{"endPoint": "login"}` -> `modules/login.php`).
- **Autoloading:** No global PSR-4 autoloader. `request.php` manually requires module files. `lib/autoload.php` is only for ReCaptcha.
- **Modules:** Business logic is encapsulated in "modules" located in `modules/`.
- **Database:** Raw SQL via PDO singleton in `classes/database.php`.
- **Namespace:** Root namespace is `server\`.

## Module Development Pattern
To create a new feature, create a class in `modules/` extending `server\abstracts\module`.

### Structure
```php
class myFeature extends \server\abstracts\module {
    public function __construct(){
        parent::__construct();
        // Initialize response templates if needed
    }

    public function process(){
        // 1. Validate inputs ($this->inputs)
        // 2. Perform DB operations ($this->database)
        // 3. Set $this->response
        return $this;
    }

    public function getResponse(){
        echo json_encode($this->response);
    }
}
```

### Key Conventions
- **Request Format:** POST requests must be JSON with `{"endPoint": "moduleName", "data": {...}}`.
- **Input:** Access request data via `$this->inputs`.
- **Output:** Use `$this->respSuccessTemplate` and `$this->repFailTemplate` for consistent JSON structure.
    - Success: `["response" => "success", "content" => [...]]`
    - Error: `["response" => "error", "errors" => ["errMsg" => "..."]]`
- **Database:** Use `$this->database->prepare(...)` for queries. **Always** use prepared statements.
- **Config:** Constants are in `interfaces/config.php` (implemented by `database` and `module` classes). Ensure these match your environment.

## Security & Auth
- **Authentication:** Session-based (`$_SESSION["userId"]`).
- **Authorization:** `server\classes\request` checks auth. Public modules (like `login`, `download`) must be listed in `$modulesWithoutAuthorization` in `request.php`.
- **CORS:** Managed in `index.php`.
- **Validation:** Validate all inputs in the `process()` method before using them.
- **Error Handling:** Do not use `die()` or `exit()` inside modules; set `$this->response` to an error state instead.

## Database & SQL
- **Connection:** `server\classes\database::getConnection()` (available as `$this->database` in modules).
- **Schema:** Defined in `Docker/mysql/dbinit/tables.sql`.
- **Style:** Write raw SQL. No ORM is used.

## Docker & Environment
- **Run:** `docker-compose up` starts the stack (PHP, MySQL, Adminer).
- **DB Access:** Adminer available at `http://localhost:8080`.
- **Logs:** Check `docker-compose` output or standard PHP error logs.
- **Persistence:** MySQL data persists in `Docker/.mysql`.

## Security & Auth
- **Authentication:** Session-based (`$_SESSION["userId"]`).
- **Authorization:** `server\classes\request` checks auth. Public modules (like `login`, `download`) must be listed in `$modulesWithoutAuthorization` in `request.php`.
- **CORS:** Managed in `index.php`.
- **Validation:** Validate all inputs in the `process()` method before using them.
- **Error Handling:** Do not use `die()` or `exit()` inside modules; set `$this->response` to an error state instead.
