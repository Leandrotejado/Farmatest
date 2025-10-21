<?php
/**
 * Página de Inicio Mejorada - Con información de stock por farmacia
 */

require_once 'config/db.php';

// Obtener farmacias con información de stock
$stmt = $pdo->query("
    SELECT 
        f.*,
        COUNT(mf.id) as medicamentos_disponibles,
        SUM(mf.stock) as total_stock,
        AVG(mf.stock) as promedio_stock
    FROM farmacias f
    LEFT JOIN medicamentos_farmacias mf ON f.id = mf.farmacia_id
    GROUP BY f.id
    ORDER BY f.nombre
");
$farmacias = $stmt->fetchAll();

// Obtener medicamentos más buscados
$stmt = $pdo->query("
    SELECT 
        m.nombre,
        COUNT(mf.farmacia_id) as farmacias_disponible,
        SUM(mf.stock) as total_stock
    FROM medicamentos m
    JOIN medicamentos_farmacias mf ON m.id = mf.medicamento_id
    GROUP BY m.id, m.nombre
    ORDER BY farmacias_disponible DESC, total_stock DESC
    LIMIT 6
");
$medicamentos_populares = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FarmaXpress - Encuentra Medicamentos por Farmacia</title>
    <link rel="stylesheet" href="public/css/base.css">
    <style>
        .hero-section { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            padding: 80px 0; 
            text-align: center; 
        }
        .hero-content { max-width: 800px; margin: 0 auto; padding: 0 20px; }
        .hero-title { font-size: 3em; font-weight: bold; margin-bottom: 20px; }
        .hero-subtitle { font-size: 1.3em; margin-bottom: 30px; opacity: 0.9; }
        .search-box { 
            background: white; 
            padding: 30px; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2); 
            margin-top: 30px; 
        }
        .search-form { display: grid; grid-template-columns: 1fr auto; gap: 15px; }
        .search-input { 
            padding: 15px; 
            border: 2px solid #e5e7eb; 
            border-radius: 10px; 
            font-size: 16px; 
        }
        .search-btn { 
            background: #3b82f6; 
            color: white; 
            padding: 15px 30px; 
            border: none; 
            border-radius: 10px; 
            font-size: 16px; 
            cursor: pointer; 
        }
        .features-section { padding: 80px 0; background: #f9fafb; }
        .features-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 30px; 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 0 20px; 
        }
        .feature-card { 
            background: white; 
            padding: 40px; 
            border-radius: 15px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); 
            text-align: center; 
        }
        .feature-icon { font-size: 3em; margin-bottom: 20px; }
        .feature-title { font-size: 1.5em; font-weight: bold; margin-bottom: 15px; color: #1f2937; }
        .feature-description { color: #6b7280; line-height: 1.6; }
        .pharmacies-section { padding: 80px 0; }
        .pharmacies-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); 
            gap: 30px; 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 0 20px; 
        }
        .pharmacy-card { 
            background: white; 
            padding: 30px; 
            border-radius: 15px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); 
            border-left: 5px solid #3b82f6; 
        }
        .pharmacy-name { font-size: 1.3em; font-weight: bold; margin-bottom: 10px; color: #1f2937; }
        .pharmacy-info { color: #6b7280; margin-bottom: 15px; }
        .pharmacy-stats { 
            display: flex; 
            justify-content: space-between; 
            background: #f3f4f6; 
            padding: 15px; 
            border-radius: 10px; 
            margin-top: 15px; 
        }
        .stat-item { text-align: center; }
        .stat-number { font-size: 1.5em; font-weight: bold; color: #3b82f6; }
        .stat-label { font-size: 0.9em; color: #6b7280; }
        .medicamentos-section { padding: 80px 0; background: #f9fafb; }
        .medicamentos-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 20px; 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 0 20px; 
        }
        .medicamento-card { 
            background: white; 
            padding: 25px; 
            border-radius: 15px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); 
            text-align: center; 
        }
        .medicamento-name { font-weight: bold; margin-bottom: 10px; color: #1f2937; }
        .medicamento-stats { color: #6b7280; font-size: 0.9em; }
        .cta-section { 
            background: #3b82f6; 
            color: white; 
            padding: 80px 0; 
            text-align: center; 
        }
        .cta-content { max-width: 600px; margin: 0 auto; padding: 0 20px; }
        .cta-title { font-size: 2.5em; font-weight: bold; margin-bottom: 20px; }
        .cta-description { font-size: 1.2em; margin-bottom: 30px; opacity: 0.9; }
        .cta-buttons { display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; }
        .btn { 
            padding: 15px 30px; 
            border-radius: 10px; 
            text-decoration: none; 
            font-weight: bold; 
            transition: all 0.3s; 
        }
        .btn-primary { background: white; color: #3b82f6; }
        .btn-primary:hover { background: #f3f4f6; }
        .btn-secondary { background: transparent; color: white; border: 2px solid white; }
        .btn-secondary:hover { background: white; color: #3b82f6; }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">🏥 FarmaXpress</h1>
            <p class="hero-subtitle">Encuentra medicamentos disponibles en farmacias cercanas con stock en tiempo real</p>
            
            <div class="search-box">
                <form action="public/medicamentos_farmacias.php" method="GET" class="search-form">
                    <input type="text" name="medicamento" placeholder="Buscar medicamento..." class="search-input">
                    <button type="submit" class="search-btn">🔍 Buscar</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📍</div>
                <h3 class="feature-title">Stock en Tiempo Real</h3>
                <p class="feature-description">Consulta la disponibilidad actual de medicamentos en cada farmacia con información actualizada constantemente.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3 class="feature-title">Compara Precios</h3>
                <p class="feature-description">Encuentra los mejores precios comparando entre diferentes farmacias y aprovecha las ofertas especiales.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🏥</div>
                <h3 class="feature-title">Información Completa</h3>
                <p class="feature-description">Accede a horarios, obras sociales, ubicación y contacto de cada farmacia desde un solo lugar.</p>
            </div>
        </div>
    </section>

    <!-- Pharmacies Section -->
    <section class="pharmacies-section">
        <div style="text-align: center; margin-bottom: 50px;">
            <h2 style="font-size: 2.5em; font-weight: bold; color: #1f2937; margin-bottom: 20px;">🏥 Nuestras Farmacias</h2>
            <p style="font-size: 1.2em; color: #6b7280;">Cada farmacia mantiene su propio inventario actualizado</p>
        </div>
        
        <div class="pharmacies-grid">
            <?php foreach ($farmacias as $farmacia): ?>
                <div class="pharmacy-card">
                    <h3 class="pharmacy-name"><?php echo $farmacia['nombre']; ?></h3>
                    <div class="pharmacy-info">
                        <p>📍 <?php echo $farmacia['direccion']; ?></p>
                        <p>📞 <?php echo $farmacia['telefono']; ?></p>
                        <p>🕒 <?php echo $farmacia['horario']; ?></p>
                    </div>
                    
                    <div class="pharmacy-stats">
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $farmacia['medicamentos_disponibles']; ?></div>
                            <div class="stat-label">Medicamentos</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $farmacia['total_stock'] ?? 0; ?></div>
                            <div class="stat-label">Total Stock</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo round($farmacia['promedio_stock'] ?? 0); ?></div>
                            <div class="stat-label">Promedio</div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Medicamentos Populares -->
    <section class="medicamentos-section">
        <div style="text-align: center; margin-bottom: 50px;">
            <h2 style="font-size: 2.5em; font-weight: bold; color: #1f2937; margin-bottom: 20px;">💊 Medicamentos Más Disponibles</h2>
            <p style="font-size: 1.2em; color: #6b7280;">Los medicamentos con mayor disponibilidad en nuestras farmacias</p>
        </div>
        
        <div class="medicamentos-grid">
            <?php foreach ($medicamentos_populares as $medicamento): ?>
                <div class="medicamento-card">
                    <h4 class="medicamento-name"><?php echo $medicamento['nombre']; ?></h4>
                    <div class="medicamento-stats">
                        <p>🏥 <?php echo $medicamento['farmacias_disponible']; ?> farmacias</p>
                        <p>📦 <?php echo $medicamento['total_stock']; ?> unidades</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-content">
            <h2 class="cta-title">¿Necesitas un Medicamento?</h2>
            <p class="cta-description">Busca en tiempo real la disponibilidad y precios en todas nuestras farmacias</p>
            <div class="cta-buttons">
                <a href="public/medicamentos_farmacias.php" class="btn btn-primary">🔍 Buscar Medicamentos</a>
                <a href="public/index.php#map" class="btn btn-secondary">🗺️ Ver Mapa</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer style="background: #1f2937; color: white; padding: 40px 0; text-align: center;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <h3 style="margin-bottom: 20px;">🏥 FarmaXpress</h3>
            <p style="color: #9ca3af; margin-bottom: 20px;">Encuentra medicamentos disponibles en farmacias cercanas</p>
            <div style="display: flex; justify-content: center; gap: 30px; margin-bottom: 20px; flex-wrap: wrap;">
                <a href="public/medicamentos_farmacias.php" style="color: #9ca3af; text-decoration: none;">Stock por Farmacia</a>
                <a href="public/index.php#map" style="color: #9ca3af; text-decoration: none;">Mapa</a>
                <a href="public/index.php#pharmacies" style="color: #9ca3af; text-decoration: none;">Farmacias</a>
            </div>
            <p style="color: #6b7280; font-size: 0.9em;">&copy; 2024 FarmaXpress. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
