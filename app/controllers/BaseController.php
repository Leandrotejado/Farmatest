<?php
/**
 * Controlador base para todos los controladores del sistema
 */
class BaseController {
    protected $model;
    
    /**
     * Renderizar vista
     */
    protected function render($view, $data = []) {
        // Extraer variables del array $data para que estén disponibles en la vista
        extract($data);
        
        // Incluir la vista
        $viewPath = __DIR__ . "/../views/{$view}.php";
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new Exception("Vista no encontrada: {$view}");
        }
    }
    
    /**
     * Redirigir a otra página
     */
    protected function redirect($url) {
        header("Location: {$url}");
        exit();
    }
    
    /**
     * Verificar si el usuario está autenticado
     */
    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login.php');
        }
    }
    
    /**
     * Verificar rol del usuario
     */
    protected function requireRole($role) {
        $this->requireAuth();
        if ($_SESSION['user_role'] !== $role) {
            $this->redirect('/unauthorized.php');
        }
    }
    
    /**
     * Obtener datos JSON del request
     */
    protected function getJsonData() {
        $input = file_get_contents('php://input');
        return json_decode($input, true);
    }
    
    /**
     * Enviar respuesta JSON
     */
    protected function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    
    /**
     * Validar datos requeridos
     */
    protected function validateRequired($data, $required) {
        $missing = [];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missing[] = $field;
            }
        }
        
        if (!empty($missing)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Campos requeridos faltantes: ' . implode(', ', $missing)
            ], 400);
        }
    }
}

