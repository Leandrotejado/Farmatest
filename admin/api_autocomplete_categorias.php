<?php
/**
 * API para autocompletado de categorías
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
        SELECT id, nombre, descripcion, tipo_venta
        FROM categorias_medicamentos 
        WHERE activa = 1 
        AND nombre LIKE :query 
        ORDER BY nombre 
        LIMIT :limit
    ");
    
    $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $suggestions = [];
    foreach ($categorias as $categoria) {
        $tipo_icon = '';
        switch ($categoria['tipo_venta']) {
            case 'venta_libre':
                $tipo_icon = '🟢';
                break;
            case 'receta_simple':
                $tipo_icon = '🟡';
                break;
            case 'receta_archivada':
                $tipo_icon = '🔴';
                break;
        }
        
        $suggestions[] = [
            'value' => $categoria['nombre'],
            'data' => [
                'id' => $categoria['id'],
                'nombre' => $categoria['nombre'],
                'descripcion' => $categoria['descripcion'],
                'tipo_venta' => $categoria['tipo_venta'],
                'tipo_icon' => $tipo_icon
            ]
        ];
    }
    
    echo json_encode(['suggestions' => $suggestions]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
