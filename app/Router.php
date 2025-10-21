<?php
/**
 * Router simple para el patrón MVC
 */
class Router {
    private $routes = [];
    private $middleware = [];
    
    /**
     * Agregar ruta GET
     */
    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
    }
    
    /**
     * Agregar ruta POST
     */
    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
    }
    
    /**
     * Agregar ruta PUT
     */
    public function put($path, $handler) {
        $this->routes['PUT'][$path] = $handler;
    }
    
    /**
     * Agregar ruta DELETE
     */
    public function delete($path, $handler) {
        $this->routes['DELETE'][$path] = $handler;
    }
    
    /**
     * Agregar middleware
     */
    public function middleware($name, $callback) {
        $this->middleware[$name] = $callback;
    }
    
    /**
     * Resolver ruta
     */
    public function resolve() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remover la base del proyecto si existe
        $basePath = '/farmatest';
        if (strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }
        
        // Buscar ruta exacta
        if (isset($this->routes[$method][$path])) {
            $this->executeHandler($this->routes[$method][$path]);
            return;
        }
        
        // Buscar rutas con parámetros
        foreach ($this->routes[$method] as $route => $handler) {
            if ($this->matchRoute($route, $path)) {
                $this->executeHandler($handler, $this->extractParams($route, $path));
                return;
            }
        }
        
        // Ruta no encontrada
        $this->handle404();
    }
    
    /**
     * Verificar si la ruta coincide
     */
    private function matchRoute($route, $path) {
        $routePattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
        $routePattern = '#^' . $routePattern . '$#';
        
        return preg_match($routePattern, $path);
    }
    
    /**
     * Extraer parámetros de la ruta
     */
    private function extractParams($route, $path) {
        $routeParts = explode('/', $route);
        $pathParts = explode('/', $path);
        $params = [];
        
        for ($i = 0; $i < count($routeParts); $i++) {
            if (isset($routeParts[$i]) && strpos($routeParts[$i], '{') === 0) {
                $paramName = trim($routeParts[$i], '{}');
                $params[$paramName] = $pathParts[$i] ?? null;
            }
        }
        
        return $params;
    }
    
    /**
     * Ejecutar handler
     */
    private function executeHandler($handler, $params = []) {
        if (is_string($handler)) {
            // Formato: "Controller@method"
            list($controllerName, $method) = explode('@', $handler);
            
            $controllerClass = $controllerName . 'Controller';
            $controllerFile = __DIR__ . "/controllers/{$controllerClass}.php";
            
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                
                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    
                    if (method_exists($controller, $method)) {
                        call_user_func_array([$controller, $method], $params);
                        return;
                    }
                }
            }
        } elseif (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }
        
        $this->handle404();
    }
    
    /**
     * Manejar error 404
     */
    private function handle404() {
        http_response_code(404);
        
        // Verificar si el archivo de error existe
        $errorFile = __DIR__ . '/views/errors/404.php';
        if (file_exists($errorFile)) {
            include $errorFile;
        } else {
            // Fallback si no existe el archivo de error
            echo '<!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <title>404 - Página No Encontrada</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                    h1 { color: #e53e3e; font-size: 72px; margin-bottom: 20px; }
                    p { font-size: 18px; color: #666; margin-bottom: 30px; }
                    a { color: #667eea; text-decoration: none; font-weight: bold; }
                </style>
            </head>
            <body>
                <h1>404</h1>
                <p>Página no encontrada</p>
                <a href="/farmatest/public/">Volver al inicio</a>
            </body>
            </html>';
        }
        exit();
    }
    
    /**
     * Manejar error 500
     */
    public function handle500($message = 'Error interno del servidor', $file = null, $line = null) {
        http_response_code(500);
        
        // Guardar información del error en sesión
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['error_message'] = $message;
        $_SESSION['error_file'] = $file;
        $_SESSION['error_line'] = $line;
        
        // Verificar si el archivo de error existe
        $errorFile = __DIR__ . '/views/errors/500.php';
        if (file_exists($errorFile)) {
            include $errorFile;
        } else {
            // Fallback si no existe el archivo de error
            echo '<!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <title>500 - Error del Servidor</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                    h1 { color: #e53e3e; font-size: 72px; margin-bottom: 20px; }
                    p { font-size: 18px; color: #666; margin-bottom: 30px; }
                    a { color: #667eea; text-decoration: none; font-weight: bold; }
                </style>
            </head>
            <body>
                <h1>500</h1>
                <p>Error interno del servidor</p>
                <a href="/farmatest/public/">Volver al inicio</a>
            </body>
            </html>';
        }
        exit();
    }
}

