<?php
/**
 * Consulta de Medicamentos por Farmacia - Vista Pública
 */

require_once '../config/db.php';

// Obtener parámetros de búsqueda
$farmacia_id = $_GET['farmacia_id'] ?? '';
$medicamento_busqueda = $_GET['medicamento'] ?? '';
$categoria_id = $_GET['categoria_id'] ?? '';

// Construir consulta
$where_conditions = [];
$params = [];

if ($farmacia_id) {
    $where_conditions[] = "f.id = ?";
    $params[] = $farmacia_id;
}

if ($medicamento_busqueda) {
    $where_conditions[] = "m.nombre LIKE ?";
    $params[] = "%$medicamento_busqueda%";
}

if ($categoria_id) {
    $where_conditions[] = "m.id_categoria = ?";
    $params[] = $categoria_id;
}

$where_clause = $where_conditions ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Obtener medicamentos disponibles
$sql = "
    SELECT 
        mf.id,
        m.nombre as medicamento,
        m.descripcion,
        f.nombre as farmacia,
        f.direccion,
        f.telefono,
        f.horario,
        f.obras_sociales,
        mf.stock,
        mf.precio_especial,
        m.precio as precio_estandar,
        c.nombre as categoria,
        p.nombre as proveedor
    FROM medicamentos_farmacias mf
    JOIN medicamentos m ON mf.medicamento_id = m.id
    JOIN farmacias f ON mf.farmacia_id = f.id
    JOIN categorias c ON m.id_categoria = c.id
    JOIN proveedores p ON m.id_proveedor = p.id
    $where_clause
    ORDER BY f.nombre, m.nombre
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$medicamentos_disponibles = $stmt->fetchAll();

// Obtener farmacias para el filtro
$stmt = $pdo->query("SELECT * FROM farmacias ORDER BY nombre");
$farmacias = $stmt->fetchAll();

// Obtener categorías para el filtro
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nombre");
$categorias = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicamentos por Farmacia - FarmaXpress</title>
    <link rel="stylesheet" href="../public/css/base.css">
    <style>
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .search-section { background: white; padding: 30px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .search-form { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 5px; font-weight: bold; color: #374151; }
        .form-group input, .form-group select { padding: 10px; border: 1px solid #d1d5db; border-radius: 5px; }
        .btn { background: #3b82f6; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #2563eb; }
        .btn-secondary { background: #6b7280; }
        .btn-secondary:hover { background: #4b5563; }
        .results-section { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .results-header { background: #f9fafb; padding: 20px; border-bottom: 1px solid #e5e7eb; }
        .results-header h2 { margin: 0; color: #1f2937; }
        .results-count { color: #6b7280; margin-top: 5px; }
        .medicamento-card { padding: 20px; border-bottom: 1px solid #e5e7eb; }
        .medicamento-card:last-child { border-bottom: none; }
        .medicamento-header { display: flex; justify-content: between; align-items: start; margin-bottom: 15px; }
        .medicamento-name { font-size: 1.2em; font-weight: bold; color: #1f2937; margin: 0; }
        .farmacia-info { background: #f3f4f6; padding: 15px; border-radius: 8px; margin-top: 15px; }
        .farmacia-name { font-weight: bold; color: #3b82f6; margin-bottom: 5px; }
        .farmacia-details { color: #6b7280; font-size: 0.9em; }
        .price-info { display: flex; gap: 15px; margin-top: 10px; }
        .price { font-weight: bold; }
        .price-special { color: #059669; }
        .price-standard { color: #6b7280; text-decoration: line-through; }
        .stock-badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .stock-high { background: #d1fae5; color: #065f46; }
        .stock-medium { background: #fef3c7; color: #92400e; }
        .stock-low { background: #fee2e2; color: #991b1b; }
        .no-results { text-align: center; padding: 40px; color: #6b7280; }
        .category-badge { background: #e0e7ff; color: #3730a3; padding: 2px 8px; border-radius: 4px; font-size: 12px; margin-left: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="search-section">
            <h1>🔍 Buscar Medicamentos por Farmacia</h1>
            <p>Encuentra qué medicamentos están disponibles en cada farmacia y sus precios.</p>
            
            <form method="GET" class="search-form">
                <div class="form-group">
                    <label for="farmacia_id">Farmacia:</label>
                    <select name="farmacia_id" id="farmacia_id">
                        <option value="">Todas las farmacias</option>
                        <?php foreach ($farmacias as $farmacia): ?>
                            <option value="<?php echo $farmacia['id']; ?>" <?php echo $farmacia_id == $farmacia['id'] ? 'selected' : ''; ?>>
                                <?php echo $farmacia['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="medicamento">Medicamento:</label>
                    <input type="text" name="medicamento" id="medicamento" placeholder="Nombre del medicamento..." value="<?php echo htmlspecialchars($medicamento_busqueda); ?>">
                </div>
                
                <div class="form-group">
                    <label for="categoria_id">Categoría:</label>
                    <select name="categoria_id" id="categoria_id">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['id']; ?>" <?php echo $categoria_id == $categoria['id'] ? 'selected' : ''; ?>>
                                <?php echo $categoria['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group" style="display: flex; align-items: end; gap: 10px;">
                    <button type="submit" class="btn">🔍 Buscar</button>
                    <a href="?" class="btn btn-secondary">🔄 Limpiar</a>
                </div>
            </form>
        </div>

        <div class="results-section">
            <div class="results-header">
                <h2>📋 Resultados de Búsqueda</h2>
                <div class="results-count">
                    <?php echo count($medicamentos_disponibles); ?> medicamento(s) encontrado(s)
                </div>
            </div>

            <?php if (empty($medicamentos_disponibles)): ?>
                <div class="no-results">
                    <h3>😔 No se encontraron medicamentos</h3>
                    <p>Intenta ajustar los filtros de búsqueda o contacta directamente con las farmacias.</p>
                </div>
            <?php else: ?>
                <?php foreach ($medicamentos_disponibles as $medicamento): ?>
                    <div class="medicamento-card">
                        <div class="medicamento-header">
                            <h3 class="medicamento-name">
                                <?php echo $medicamento['medicamento']; ?>
                                <span class="category-badge"><?php echo $medicamento['categoria']; ?></span>
                            </h3>
                            <span class="stock-badge <?php 
                                echo $medicamento['stock'] > 50 ? 'stock-high' : 
                                    ($medicamento['stock'] > 20 ? 'stock-medium' : 'stock-low'); 
                            ?>">
                                <?php echo $medicamento['stock']; ?> unidades
                            </span>
                        </div>
                        
                        <?php if ($medicamento['descripcion']): ?>
                            <p style="color: #6b7280; margin: 10px 0;"><?php echo $medicamento['descripcion']; ?></p>
                        <?php endif; ?>
                        
                        <div class="price-info">
                            <?php if ($medicamento['precio_especial']): ?>
                                <span class="price price-special">$<?php echo number_format($medicamento['precio_especial'], 2); ?></span>
                                <span class="price-standard">$<?php echo number_format($medicamento['precio_estandar'], 2); ?></span>
                            <?php else: ?>
                                <span class="price">$<?php echo number_format($medicamento['precio_estandar'], 2); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="farmacia-info">
                            <div class="farmacia-name">🏥 <?php echo $medicamento['farmacia']; ?></div>
                            <div class="farmacia-details">
                                📍 <?php echo $medicamento['direccion']; ?><br>
                                📞 <?php echo $medicamento['telefono']; ?><br>
                                🕒 <?php echo $medicamento['horario']; ?><br>
                                <?php if ($medicamento['obras_sociales']): ?>
                                    🏥 Obras Sociales: <?php echo $medicamento['obras_sociales']; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
