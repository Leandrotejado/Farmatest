<?php
/**
 * API para autocompletado de medicamentos
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
        SELECT m.id, m.nombre, m.precio, m.stock, c.nombre as categoria_nombre, p.nombre as proveedor_nombre
        FROM medicamentos m
        LEFT JOIN categorias_medicamentos c ON m.id_categoria = c.id
        LEFT JOIN proveedores p ON m.id_proveedor = p.id
        WHERE m.nombre LIKE :query 
        ORDER BY m.nombre 
        LIMIT :limit
    ");
    
    $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $medicamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $suggestions = [];
    foreach ($medicamentos as $medicamento) {
        $suggestions[] = [
            'value' => $medicamento['nombre'],
            'data' => [
                'id' => $medicamento['id'],
                'nombre' => $medicamento['nombre'],
                'precio' => $medicamento['precio'],
                'stock' => $medicamento['stock'],
                'categoria' => $medicamento['categoria_nombre'],
                'proveedor' => $medicamento['proveedor_nombre']
            ]
        ];
    }
    
    echo json_encode(['suggestions' => $suggestions]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
