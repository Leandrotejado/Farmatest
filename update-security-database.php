<?php
require 'config/db.php';

try {
    // Agregar columna password_salt si no existe
    $pdo->exec('ALTER TABLE usuarios ADD COLUMN password_salt VARCHAR(64) DEFAULT NULL');
    echo "✅ Columna 'password_salt' agregada\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "✅ Columna 'password_salt' ya existe\n";
    } else {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

try {
    // Agregar columna login_attempts si no existe
    $pdo->exec('ALTER TABLE usuarios ADD COLUMN login_attempts INT DEFAULT 0');
    echo "✅ Columna 'login_attempts' agregada\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "✅ Columna 'login_attempts' ya existe\n";
    } else {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

try {
    // Agregar columna last_login si no existe
    $pdo->exec('ALTER TABLE usuarios ADD COLUMN last_login TIMESTAMP NULL DEFAULT NULL');
    echo "✅ Columna 'last_login' agregada\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "✅ Columna 'last_login' ya existe\n";
    } else {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

try {
    // Agregar columna two_factor_enabled si no existe
    $pdo->exec('ALTER TABLE usuarios ADD COLUMN two_factor_enabled TINYINT(1) DEFAULT 0');
    echo "✅ Columna 'two_factor_enabled' agregada\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "✅ Columna 'two_factor_enabled' ya existe\n";
    } else {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

try {
    // Agregar columna two_factor_secret si no existe
    $pdo->exec('ALTER TABLE usuarios ADD COLUMN two_factor_secret VARCHAR(32) DEFAULT NULL');
    echo "✅ Columna 'two_factor_secret' agregada\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "✅ Columna 'two_factor_secret' ya existe\n";
    } else {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

try {
    // Crear tabla de logs de seguridad
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS security_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            event VARCHAR(100) NOT NULL,
            details TEXT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            level ENUM("INFO", "WARNING", "ERROR", "SECURITY") DEFAULT "INFO",
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE SET NULL
        )
    ');
    echo "✅ Tabla 'security_logs' creada\n";
} catch (Exception $e) {
    echo "❌ Error creando tabla security_logs: " . $e->getMessage() . "\n";
}

try {
    // Crear tabla de sesiones seguras
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS secure_sessions (
            id VARCHAR(128) PRIMARY KEY,
            user_id INT NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            expires_at TIMESTAMP NOT NULL,
            FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE
        )
    ');
    echo "✅ Tabla 'secure_sessions' creada\n";
} catch (Exception $e) {
    echo "❌ Error creando tabla secure_sessions: " . $e->getMessage() . "\n";
}

echo "\n🎉 Base de datos actualizada con características de seguridad avanzada\n";
?>
