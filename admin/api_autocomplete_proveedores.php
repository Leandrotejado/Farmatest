<?php
/**
 * API para autocompletado de proveedores
 */

header('Content-Type: application/json');
require '../config/db.php';

try {
    $query = $_GET['q'] ?? '';
    $limit = (int)($_GET['limit'] ?? 10);
    
    if (strlen($query) < 2) {
        echo json_encode(['suggestions' => []]);
        exit;
    }
    
    $stmt = $pdo->prepare("
        SELECT id, nombre, contacto, telefono, email, ciudad, provincia
        FROM proveedores 
        WHERE activo = 1 
        AND (nombre LIKE :query OR contacto LIKE :query)
        ORDER BY nombre 
        LIMIT :limit
    ");
    
    $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $suggestions = [];
    foreach ($proveedores as $proveedor) {
        $suggestions[] = [
            'value' => $proveedor['nombre'],
            'data' => [
                'id' => $proveedor['id'],
                'nombre' => $proveedor['nombre'],
                'contacto' => $proveedor['contacto'],
                'telefono' => $proveedor['telefono'],
                'email' => $proveedor['email'],
                'ciudad' => $proveedor['ciudad'],
                'provincia' => $proveedor['provincia']
            ]
        ];
    }
    
    echo json_encode(['suggestions' => $suggestions]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
