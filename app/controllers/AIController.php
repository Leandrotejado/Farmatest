<?php
require_once 'BaseController.php';
require_once '../services/AIService.php';

/**
 * Controlador para funcionalidades de IA
 */
class AIController extends BaseController {
    private $aiService;
    
    public function __construct() {
        $this->aiService = new AIService();
    }
    
    /**
     * Obtener recomendaciones de medicamentos
     */
    public function recommendations() {
        $this->requireAuth();
        
        $medicamentoId = $_GET['medicamento_id'] ?? null;
        $topN = $_GET['top_n'] ?? 5;
        
        if (!$medicamentoId) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'ID de medicamento requerido'
            ], 400);
        }
        
        $result = $this->aiService->getRecommendations($medicamentoId, $topN);
        
        if ($result['success']) {
            $this->jsonResponse([
                'success' => true,
                'data' => $result['data'],
                'message' => 'Recomendaciones generadas exitosamente'
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error generando recomendaciones: ' . $result['error']
            ], 500);
        }
    }
    
    /**
     * Predecir demanda de stock
     */
    public function predictDemand() {
        $this->requireRole('admin');
        
        $medicamentoId = $_GET['medicamento_id'] ?? null;
        $daysAhead = $_GET['days_ahead'] ?? 30;
        
        if (!$medicamentoId) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'ID de medicamento requerido'
            ], 400);
        }
        
        $result = $this->aiService->predictDemand($medicamentoId, $daysAhead);
        
        if ($result['success']) {
            $this->jsonResponse([
                'success' => true,
                'data' => $result['data'],
                'message' => 'Predicción generada exitosamente'
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error generando predicción: ' . $result['error']
            ], 500);
        }
    }
    
    /**
     * Detectar anomalías
     */
    public function anomalies() {
        $this->requireRole('admin');
        
        $result = $this->aiService->detectAnomalies();
        
        if ($result['success']) {
            $this->jsonResponse([
                'success' => true,
                'data' => $result['data'],
                'message' => 'Anomalías detectadas exitosamente'
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error detectando anomalías: ' . $result['error']
            ], 500);
        }
    }
    
    /**
     * Generar insights
     */
    public function insights() {
        $this->requireAuth();
        
        $result = $this->aiService->generateInsights();
        
        if ($result['success']) {
            $this->jsonResponse([
                'success' => true,
                'data' => $result['data'],
                'message' => 'Insights generados exitosamente'
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error generando insights: ' . $result['error']
            ], 500);
        }
    }
    
    /**
     * Verificar estado del servicio de IA
     */
    public function status() {
        $this->requireAuth();
        
        $pythonAvailable = $this->aiService->isPythonAvailable();
        $dependenciesOk = $this->aiService->checkDependencies();
        
        $status = [
            'python_available' => $pythonAvailable,
            'dependencies_ok' => $dependenciesOk,
            'ai_service_ready' => $pythonAvailable && $dependenciesOk
        ];
        
        $this->jsonResponse([
            'success' => true,
            'data' => $status,
            'message' => 'Estado del servicio de IA verificado'
        ]);
    }
    
    /**
     * Dashboard de IA
     */
    public function dashboard() {
        $this->requireRole('admin');
        
        // Obtener datos para el dashboard
        $anomalies = $this->aiService->detectAnomalies();
        $insights = $this->aiService->generateInsights();
        $status = [
            'python_available' => $this->aiService->isPythonAvailable(),
            'dependencies_ok' => $this->aiService->checkDependencies()
        ];
        
        $this->render('ai/dashboard', [
            'anomalies' => $anomalies['success'] ? $anomalies['data'] : [],
            'insights' => $insights['success'] ? $insights['data'] : [],
            'status' => $status
        ]);
    }
}

