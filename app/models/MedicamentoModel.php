<?php
require_once 'BaseModel.php';

/**
 * Modelo para gestión de medicamentos
 */
class MedicamentoModel extends BaseModel {
    protected $table = 'medicamentos';
    
    /**
     * Obtener medicamentos con información de categoría
     */
    public function getAllWithCategory() {
        $stmt = $this->pdo->prepare("
            SELECT m.*, c.nombre as categoria_nombre 
            FROM {$this->table} m 
            LEFT JOIN categorias c ON m.categoria_id = c.id 
            ORDER BY m.nombre
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Buscar medicamentos por nombre
     */
    public function searchByName($name) {
        $stmt = $this->pdo->prepare("
            SELECT m.*, c.nombre as categoria_nombre 
            FROM {$this->table} m 
            LEFT JOIN categorias c ON m.categoria_id = c.id 
            WHERE m.nombre LIKE ? 
            ORDER BY m.nombre
        ");
        $stmt->execute(["%{$name}%"]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener medicamentos con stock bajo
     */
    public function getLowStock($threshold = 10) {
        $stmt = $this->pdo->prepare("
            SELECT m.*, c.nombre as categoria_nombre 
            FROM {$this->table} m 
            LEFT JOIN categorias c ON m.categoria_id = c.id 
            WHERE m.stock <= ? 
            ORDER BY m.stock ASC
        ");
        $stmt->execute([$threshold]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener medicamentos próximos a vencer
     */
    public function getExpiringSoon($days = 30) {
        $stmt = $this->pdo->prepare("
            SELECT m.*, c.nombre as categoria_nombre 
            FROM {$this->table} m 
            LEFT JOIN categorias c ON m.categoria_id = c.id 
            WHERE m.fecha_vencimiento <= DATE_ADD(NOW(), INTERVAL ? DAY) 
            AND m.fecha_vencimiento > NOW()
            ORDER BY m.fecha_vencimiento ASC
        ");
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }
    
    /**
     * Actualizar stock
     */
    public function updateStock($id, $newStock) {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET stock = ? WHERE id = ?");
        return $stmt->execute([$newStock, $id]);
    }
    
    /**
     * Obtener estadísticas de medicamentos
     */
    public function getStatistics() {
        $stats = [];
        
        // Total de medicamentos
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM {$this->table}");
        $stmt->execute();
        $stats['total'] = $stmt->fetch()['total'];
        
        // Stock bajo
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE stock <= 10");
        $stmt->execute();
        $stats['stock_bajo'] = $stmt->fetch()['total'];
        
        // Próximos a vencer
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE fecha_vencimiento <= DATE_ADD(NOW(), INTERVAL 30 DAY)");
        $stmt->execute();
        $stats['por_vencer'] = $stmt->fetch()['total'];
        
        return $stats;
    }
}
