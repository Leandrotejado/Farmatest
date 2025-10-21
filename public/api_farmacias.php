<?php
/**
 * API para obtener farmacias desde la base de datos
 */

header('Content-Type: application/json');
require '../config/db.php';

try {
    // Verificar que la tabla farmacias existe
    $tables_check = $pdo->query("SHOW TABLES LIKE 'farmacias'")->rowCount();
    
    if ($tables_check == 0) {
        throw new Exception("La tabla farmacias no existe. Ejecuta setup_farmacias.php");
    }
    
    // Obtener farmacias activas
    $stmt = $pdo->query("
        SELECT 
            id,
            nombre as name,
            latitud as lat,
            longitud as lng,
            direccion as address,
            telefono as phone,
            CASE 
                WHEN de_turno = 1 THEN 'abierta'
                ELSE 'cerrada'
            END as status,
            distrito,
            servicios,
            horario_apertura,
            horario_cierre
        FROM farmacias 
        WHERE activa = 1 
        ORDER BY nombre
    ");
    
    $farmacias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Agregar información adicional para compatibilidad
    foreach ($farmacias as &$farmacia) {
        $farmacia['id'] = (int)$farmacia['id'];
        $farmacia['lat'] = (float)$farmacia['lat'];
        $farmacia['lng'] = (float)$farmacia['lng'];
        $farmacia['distance'] = 0; // Se calculará en el frontend
    }
    
    echo json_encode([
        'success' => true,
        'farmacias' => $farmacias,
        'total' => count($farmacias)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'farmacias' => []
    ]);
}
?>
