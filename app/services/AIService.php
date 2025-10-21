<?php
/**
 * Servicio de integración con Python AI
 * Proporciona una interfaz PHP para las funcionalidades de IA
 */
class AIService {
    private $pythonPath;
    private $scriptPath;
    
    public function __construct() {
        $this->pythonPath = 'python'; // Ajustar según la instalación
        $this->scriptPath = __DIR__ . '/../../python/ai_service.py';
    }
    
    /**
     * Obtener recomendaciones de medicamentos
     */
    public function getRecommendations($medicamentoId, $topN = 5) {
        $command = sprintf(
            '%s %s --type recommendations --medicamento_id %d --top_n %d',
            $this->pythonPath,
            $this->scriptPath,
            $medicamentoId,
            $topN
        );
        
        return $this->executePythonCommand($command);
    }
    
    /**
     * Predecir demanda de stock
     */
    public function predictDemand($medicamentoId, $daysAhead = 30) {
        $command = sprintf(
            '%s %s --type demand_prediction --medicamento_id %d --days_ahead %d',
            $this->pythonPath,
            $this->scriptPath,
            $medicamentoId,
            $daysAhead
        );
        
        return $this->executePythonCommand($command);
    }
    
    /**
     * Detectar anomalías en el inventario
     */
    public function detectAnomalies() {
        $command = sprintf(
            '%s %s --type anomalies',
            $this->pythonPath,
            $this->scriptPath
        );
        
        return $this->executePythonCommand($command);
    }
    
    /**
     * Generar insights del negocio
     */
    public function generateInsights() {
        $command = sprintf(
            '%s %s --type insights',
            $this->pythonPath,
            $this->scriptPath
        );
        
        return $this->executePythonCommand($command);
    }
    
    /**
     * Ejecutar comando de Python
     */
    private function executePythonCommand($command) {
        try {
            // Ejecutar comando y capturar salida
            $output = shell_exec($command . ' 2>&1');
            
            if ($output === null) {
                return [
                    'success' => false,
                    'error' => 'No se pudo ejecutar el comando de Python'
                ];
            }
            
            // Intentar decodificar JSON
            $result = json_decode($output, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'success' => false,
                    'error' => 'Error al decodificar respuesta de Python: ' . $output
                ];
            }
            
            return [
                'success' => true,
                'data' => $result
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Error ejecutando Python: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Verificar si Python está disponible
     */
    public function isPythonAvailable() {
        $command = $this->pythonPath . ' --version 2>&1';
        $output = shell_exec($command);
        
        return $output !== null && strpos($output, 'Python') !== false;
    }
    
    /**
     * Verificar dependencias de Python
     */
    public function checkDependencies() {
        $command = sprintf(
            '%s -c "import pandas, numpy, sklearn; print(\'OK\')" 2>&1',
            $this->pythonPath
        );
        
        $output = shell_exec($command);
        return $output !== null && trim($output) === 'OK';
    }
}

