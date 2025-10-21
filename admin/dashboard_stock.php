<?php
/**
 * Dashboard Administrativo - Vista General del Sistema
 */

session_start();
require_once '../config/db.php';

// Verificar autenticación
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Obtener estadísticas del sistema
$stats = [];

// Total de farmacias
$stmt = $pdo->query("SELECT COUNT(*) as total FROM farmacias");
$stats['farmacias'] = $stmt->fetch()['total'];

// Total de medicamentos
$stmt = $pdo->query("SELECT COUNT(*) as total FROM medicamentos");
$stats['medicamentos'] = $stmt->fetch()['total'];

// Total de registros de stock
$stmt = $pdo->query("SELECT COUNT(*) as total FROM medicamentos_farmacias");
$stats['stock_registros'] = $stmt->fetch()['total'];

// Stock bajo (menos de 20 unidades)
$stmt = $pdo->query("SELECT COUNT(*) as total FROM medicamentos_farmacias WHERE stock < 20");
$stats['stock_bajo'] = $stmt->fetch()['total'];

// Stock medio (20-50 unidades)
$stmt = $pdo->query("SELECT COUNT(*) as total FROM medicamentos_farmacias WHERE stock >= 20 AND stock <= 50");
$stats['stock_medio'] = $stmt->fetch()['total'];

// Stock alto (más de 50 unidades)
$stmt = $pdo->query("SELECT COUNT(*) as total FROM medicamentos_farmacias WHERE stock > 50");
$stats['stock_alto'] = $stmt->fetch()['total'];

// Obtener farmacias con menos stock
$stmt = $pdo->query("
    SELECT 
        f.nombre as farmacia,
        COUNT(mf.id) as medicamentos_con_stock,
        SUM(mf.stock) as total_stock,
        COUNT(CASE WHEN mf.stock < 20 THEN 1 END) as stock_bajo
    FROM farmacias f
    LEFT JOIN medicamentos_farmacias mf ON f.id = mf.farmacia_id
    GROUP BY f.id, f.nombre
    ORDER BY stock_bajo DESC, total_stock ASC
");
$farmacias_stock = $stmt->fetchAll();

// Obtener medicamentos más populares
$stmt = $pdo->query("
    SELECT 
        m.nombre as medicamento,
        SUM(mf.stock) as total_stock,
        COUNT(mf.farmacia_id) as farmacias_disponible
    FROM medicamentos m
    JOIN medicamentos_farmacias mf ON m.id = mf.medicamento_id
    GROUP BY m.id, m.nombre
    ORDER BY total_stock DESC
    LIMIT 10
");
$medicamentos_populares = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrativo - FarmaXpress</title>
    <link rel="stylesheet" href="../public/css/base.css">
    <style>
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .dashboard-header { background: white; padding: 30px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 2.5em; font-weight: bold; margin-bottom: 10px; }
        .stat-label { color: #6b7280; font-size: 1.1em; }
        .stat-card.stock-high .stat-number { color: #059669; }
        .stat-card.stock-medium .stat-number { color: #d97706; }
        .stat-card.stock-low .stat-number { color: #dc2626; }
        .stat-card.primary .stat-number { color: #3b82f6; }
        .content-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .content-section { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .content-section h3 { margin-top: 0; color: #1f2937; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        .table th { background: #f9fafb; font-weight: bold; color: #374151; }
        .table tr:hover { background: #f9fafb; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .btn { background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
        .btn:hover { background: #2563eb; }
        .btn-success { background: #059669; }
        .btn-success:hover { background: #047857; }
        .alert { padding: 15px; border-radius: 5px; margin: 15px 0; }
        .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #f59e0b; }
        .quick-actions { display: flex; gap: 15px; margin-top: 20px; flex-wrap: wrap; }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <h1>🏥 Dashboard Administrativo - FarmaXpress</h1>
            <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Administrador'); ?>. Aquí tienes una vista general del sistema.</p>
            
            <div class="quick-actions">
                <a href="medicamentos.php" class="btn">💊 Gestionar Medicamentos</a>
                <a href="categorias_proveedores.php" class="btn">📋 Categorías y Proveedores</a>
                <a href="stock_farmacias.php" class="btn">📦 Gestionar Stock</a>
                <a href="farmacias.php" class="btn">🏥 Gestionar Farmacias</a>
                <a href="../public/medicamentos_farmacias.php" class="btn btn-success">👁️ Ver Vista Pública</a>
            </div>
        </div>

        <!-- Estadísticas principales -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-number"><?php echo $stats['farmacias']; ?></div>
                <div class="stat-label">Farmacias</div>
            </div>
            <div class="stat-card primary">
                <div class="stat-number"><?php echo $stats['medicamentos']; ?></div>
                <div class="stat-label">Medicamentos</div>
            </div>
            <div class="stat-card primary">
                <div class="stat-number"><?php echo $stats['stock_registros']; ?></div>
                <div class="stat-label">Registros de Stock</div>
            </div>
        </div>

        <!-- Estadísticas de stock -->
        <div class="stats-grid">
            <div class="stat-card stock-high">
                <div class="stat-number"><?php echo $stats['stock_alto']; ?></div>
                <div class="stat-label">Stock Alto (>50)</div>
            </div>
            <div class="stat-card stock-medium">
                <div class="stat-number"><?php echo $stats['stock_medio']; ?></div>
                <div class="stat-label">Stock Medio (20-50)</div>
            </div>
            <div class="stat-card stock-low">
                <div class="stat-number"><?php echo $stats['stock_bajo']; ?></div>
                <div class="stat-label">Stock Bajo (<20)</div>
            </div>
        </div>

        <?php if ($stats['stock_bajo'] > 0): ?>
            <div class="alert alert-warning">
                ⚠️ <strong>Atención:</strong> Hay <?php echo $stats['stock_bajo']; ?> medicamento(s) con stock bajo. 
                <a href="stock_farmacias.php" style="color: #92400e; font-weight: bold;">Revisar stock</a>
            </div>
        <?php endif; ?>

        <!-- Contenido principal -->
        <div class="content-grid">
            <!-- Farmacias con menos stock -->
            <div class="content-section">
                <h3>🏥 Farmacias - Estado del Stock</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Farmacia</th>
                            <th>Medicamentos</th>
                            <th>Total Stock</th>
                            <th>Stock Bajo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($farmacias_stock as $farmacia): ?>
                            <tr>
                                <td><strong><?php echo $farmacia['farmacia']; ?></strong></td>
                                <td><?php echo $farmacia['medicamentos_con_stock']; ?></td>
                                <td><?php echo $farmacia['total_stock'] ?? 0; ?></td>
                                <td>
                                    <?php if ($farmacia['stock_bajo'] > 0): ?>
                                        <span class="badge badge-danger"><?php echo $farmacia['stock_bajo']; ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-success">0</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Medicamentos más populares -->
            <div class="content-section">
                <h3>💊 Medicamentos Más Populares</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Medicamento</th>
                            <th>Total Stock</th>
                            <th>Farmacias</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($medicamentos_populares as $medicamento): ?>
                            <tr>
                                <td><strong><?php echo $medicamento['medicamento']; ?></strong></td>
                                <td><?php echo $medicamento['total_stock']; ?></td>
                                <td><?php echo $medicamento['farmacias_disponible']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Acciones rápidas -->
        <div class="content-section" style="margin-top: 30px;">
            <h3>⚡ Acciones Rápidas</h3>
            <div class="quick-actions">
                <a href="medicamentos.php" class="btn">💊 Gestionar Medicamentos</a>
                <a href="categorias_proveedores.php" class="btn">📋 Categorías y Proveedores</a>
                <a href="stock_farmacias.php" class="btn">📦 Gestionar Stock por Farmacia</a>
                <a href="farmacias.php" class="btn">🏥 Administrar Farmacias</a>
                <a href="../public/medicamentos_farmacias.php" class="btn btn-success">👁️ Ver Medicamentos Disponibles</a>
                <a href="../public/index.php" class="btn">🗺️ Ver Mapa de Farmacias</a>
            </div>
        </div>
    </div>
</body>
</html>
