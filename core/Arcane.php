<?php
/**
 * IlaraNet Bénin - Arcane.php (Framework Core)
 * Router, Middleware, Session, Template Engine
 */

namespace IlaraNet\Core;

use IlaraNet\Helpers\Security;

class Arcane
{
    private static ?Arcane $instance = null;
    private array $routes = [];
    private array $middleware = [];
    private string $basePath;
    private array $config;

    private function __construct()
    {
        $this->basePath = dirname(__DIR__);
        $this->config = require $this->basePath . '/config/app.php';
        $this->initSession();
        $this->initTimezone();
    }

    public static function getInstance(): Arcane
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.use_strict_mode', '1');
            ini_set('session.gc_maxlifetime', $this->config['security']['session_timeout']);
            session_start();
        }
    }

    private function initTimezone(): void
    {
        date_default_timezone_set($this->config['app']['timezone']);
    }

    /**
     * Enregistrement des routes
     */
    public function get(string $path, callable|array $handler): self
    {
        $this->routes['GET'][$path] = $handler;
        return $this;
    }

    public function post(string $path, callable|array $handler): self
    {
        $this->routes['POST'][$path] = $handler;
        return $this;
    }

    public function put(string $path, callable|array $handler): self
    {
        $this->routes['PUT'][$path] = $handler;
        return $this;
    }

    public function delete(string $path, callable|array $handler): self
    {
        $this->routes['DELETE'][$path] = $handler;
        return $this;
    }

    /**
     * Middleware registration
     */
    public function middleware(string $name, callable $callback): self
    {
        $this->middleware[$name] = $callback;
        return $this;
    }

    /**
     * Exécution du router
     */
    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove base path if exists
        $baseUri = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if ($baseUri && strpos($uri, $baseUri) === 0) {
            $uri = substr($uri, strlen($baseUri));
        }
        $uri = $uri ?: '/';

        // Find matching route
        $handler = null;
        $params = [];

        foreach ($this->routes[$method] ?? [] as $route => $routeHandler) {
            if ($this->matchRoute($route, $uri, $params)) {
                $handler = $routeHandler;
                break;
            }
        }

        if ($handler === null) {
            http_response_code(404);
            echo $this->renderView('errors/404');
            return;
        }

        // Execute handler
        try {
            if (is_callable($handler)) {
                call_user_func_array($handler, $params);
            } elseif (is_array($handler)) {
                [$controller, $action] = $handler;
                if (class_exists($controller)) {
                    $instance = new $controller();
                    call_user_func_array([$instance, $action], $params);
                } else {
                    throw new \Exception("Controller not found: {$controller}");
                }
            }
        } catch (\Exception $e) {
            error_log("Erreur: " . $e->getMessage());
            if ($this->config['app']['debug']) {
                echo "<pre>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
            } else {
                http_response_code(500);
                echo $this->renderView('errors/500');
            }
        }
    }

    private function matchRoute(string $route, string $uri, array &$params): bool
    {
        // Exact match
        if ($route === $uri) {
            return true;
        }

        // Parameterized routes
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $route);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return true;
        }

        return false;
    }

    /**
     * Template rendering
     */
    public function renderView(string $view, array $data = []): string
    {
        extract($data);
        
        $viewPath = $this->basePath . "/templates/{$view}.php";
        
        if (!file_exists($viewPath)) {
            throw new \Exception("Vue non trouvée: {$view}");
        }

        ob_start();
        include $viewPath;
        return ob_get_clean();
    }

    public function renderLayout(string $layout, string $content, array $data = []): string
    {
        extract($data);
        
        $layoutPath = $this->basePath . "/templates/layouts/{$layout}.php";
        
        if (!file_exists($layoutPath)) {
            throw new \Exception("Layout non trouvé: {$layout}");
        }

        ob_start();
        include $layoutPath;
        return ob_get_clean();
    }

    public function view(string $view, array $data = [], string $layout = 'main'): void
    {
        $content = $this->renderView($view, $data);
        echo $this->renderLayout($layout, $content, $data);
    }

    /**
     * Redirect helper
     */
    public function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * JSON response
     */
    public function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * CSRF Token generation
     */
    public function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * CSRF verification
     */
    public function verifyCsrf(string $token): bool
    {
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    /**
     * Flash messages
     */
    public function flash(string $key, mixed $value = null): mixed
    {
        if ($value === null) {
            $flash = $_SESSION['_flash'][$key] ?? null;
            unset($_SESSION['_flash'][$key]);
            return $flash;
        }
        
        $_SESSION['_flash'][$key] = $value;
        return null;
    }

    /**
     * Auth helpers
     */
    public function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public function isAuthenticated(): bool
    {
        return isset($_SESSION['user']);
    }

    public function hasRole(string $role): bool
    {
        $user = $this->user();
        return $user && in_array($role, $user['roles'] ?? []);
    }

    /**
     * Get config value
     */
    public function config(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->config;
        
        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }
        
        return $value;
    }
}

// Helper functions
if (!function_exists('arcane')) {
    function arcane(): Arcane
    {
        return Arcane::getInstance();
    }
}

if (!function_exists('view')) {
    function view(string $view, array $data = []): string
    {
        return arcane()->renderView($view, $data);
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): void
    {
        arcane()->redirect($url);
    }
}

if (!function_exists('json_response')) {
    function json_response(array $data, int $status = 200): void
    {
        arcane()->json($data, $status);
    }
}

if (!function_exists('auth_user')) {
    function auth_user(): ?array
    {
        return arcane()->user();
    }
}

if (!function_exists('is_authenticated')) {
    function is_authenticated(): bool
    {
        return arcane()->isAuthenticated();
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return arcane()->csrfToken();
    }
}

if (!function_exists('flash')) {
    function flash(string $key, mixed $value = null): mixed
    {
        return arcane()->flash($key, $value);
    }
}
