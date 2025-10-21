<?php
require_once 'BaseController.php';
require_once '../models/MedicamentoModel.php';

/**
 * Controlador para gestión de medicamentos
 */
class MedicamentoController extends BaseController {
    private $medicamentoModel;
    
    public function __construct() {
        $this->medicamentoModel = new MedicamentoModel();
    }
    
    /**
     * Listar todos los medicamentos
     */
    public function index() {
        $this->requireAuth();
        
        $medicamentos = $this->medicamentoModel->getAllWithCategory();
        $statistics = $this->medicamentoModel->getStatistics();
        
        $this->render('medicamentos/index', [
            'medicamentos' => $medicamentos,
            'statistics' => $statistics
        ]);
    }
    
    /**
     * Mostrar formulario de creación
     */
    public function create() {
        $this->requireRole('admin');
        
        $this->render('medicamentos/create');
    }
    
    /**
     * Guardar nuevo medicamento
     */
    public function store() {
        $this->requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $this->validateRequired($_POST, ['nombre', 'descripcion', 'precio', 'stock', 'categoria_id']);
        
        $data = [
            'nombre' => $_POST['nombre'],
            'descripcion' => $_POST['descripcion'],
            'precio' => $_POST['precio'],
            'stock' => $_POST['stock'],
            'categoria_id' => $_POST['categoria_id'],
            'fecha_vencimiento' => $_POST['fecha_vencimiento'] ?? null,
            'proveedor' => $_POST['proveedor'] ?? null
        ];
        
        if ($this->medicamentoModel->create($data)) {
            $this->jsonResponse([
                'success' => true,
                'message' => 'Medicamento creado exitosamente',
                'redirect' => '/admin/medicamentos.php'
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al crear medicamento'
            ], 500);
        }
    }
    
    /**
     * Mostrar medicamento específico
     */
    public function show($id) {
        $this->requireAuth();
        
        $medicamento = $this->medicamentoModel->getById($id);
        
        if (!$medicamento) {
            $this->redirect('/404.php');
        }
        
        $this->render('medicamentos/show', ['medicamento' => $medicamento]);
    }
    
    /**
     * Mostrar formulario de edición
     */
    public function edit($id) {
        $this->requireRole('admin');
        
        $medicamento = $this->medicamentoModel->getById($id);
        
        if (!$medicamento) {
            $this->redirect('/404.php');
        }
        
        $this->render('medicamentos/edit', ['medicamento' => $medicamento]);
    }
    
    /**
     * Actualizar medicamento
     */
    public function update($id) {
        $this->requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $this->validateRequired($_POST, ['nombre', 'descripcion', 'precio', 'stock', 'categoria_id']);
        
        $data = [
            'nombre' => $_POST['nombre'],
            'descripcion' => $_POST['descripcion'],
            'precio' => $_POST['precio'],
            'stock' => $_POST['stock'],
            'categoria_id' => $_POST['categoria_id'],
            'fecha_vencimiento' => $_POST['fecha_vencimiento'] ?? null,
            'proveedor' => $_POST['proveedor'] ?? null
        ];
        
        if ($this->medicamentoModel->update($id, $data)) {
            $this->jsonResponse([
                'success' => true,
                'message' => 'Medicamento actualizado exitosamente',
                'redirect' => '/admin/medicamentos.php'
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al actualizar medicamento'
            ], 500);
        }
    }
    
    /**
     * Eliminar medicamento
     */
    public function delete($id) {
        $this->requireRole('admin');
        
        if ($this->medicamentoModel->delete($id)) {
            $this->jsonResponse([
                'success' => true,
                'message' => 'Medicamento eliminado exitosamente'
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Error al eliminar medicamento'
            ], 500);
        }
    }
    
    /**
     * Buscar medicamentos
     */
    public function search() {
        $this->requireAuth();
        
        $query = $_GET['q'] ?? '';
        
        if (empty($query)) {
            $medicamentos = $this->medicamentoModel->getAllWithCategory();
        } else {
            $medicamentos = $this->medicamentoModel->searchByName($query);
        }
        
        $this->render('medicamentos/search', [
            'medicamentos' => $medicamentos,
            'query' => $query
        ]);
    }
    
    /**
     * Obtener medicamentos con stock bajo
     */
    public function lowStock() {
        $this->requireRole('admin');
        
        $medicamentos = $this->medicamentoModel->getLowStock();
        
        $this->render('medicamentos/low-stock', ['medicamentos' => $medicamentos]);
    }
    
    /**
     * Obtener medicamentos próximos a vencer
     */
    public function expiringSoon() {
        $this->requireRole('admin');
        
        $medicamentos = $this->medicamentoModel->getExpiringSoon();
        
        $this->render('medicamentos/expiring-soon', ['medicamentos' => $medicamentos]);
    }
}

