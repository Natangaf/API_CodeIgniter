<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use CodeIgniter\RESTful\ResourceController;

class ClienteController extends ResourceController
{
    protected $clienteModel; 

    public function __construct()
    {
        $this->clienteModel = new ClienteModel();
        helper(['validation']);
    }
    public function index()
    {
        // Buscar todos os clientes no banco
        $allClients = $this->clienteModel->findAll();

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Lista de clientes recuperada com sucesso'
            ],
            'retorno' => $allClients
        ], 200);
    }

    public function show($id = null)
    {
        // Buscar cliente pelo ID
        $cliente = $this->clienteModel->find($id);
    
        // Se não encontrar, retorna erro 404
        if (!$cliente) {
            return $this->failNotFound("Cliente com ID {$id} não encontrado.");
        }
    
        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Cliente recuperado com sucesso'
            ],
            'retorno' => $cliente
        ], 200);
    }
    

    public function create()
    {
        // Obtém dados como array associativo
        $data = $this->request->getJSON(true);

        // Validação usando as regras do model
        if (!$this->validate($this->clienteModel->validationRules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Insere no banco
        $insertedId = $this->clienteModel->insert($data);

        if (!$insertedId) {
            return $this->failServerError('Erro ao criar cliente');
        }

        return $this->respondCreated([
            'cabecalho' => [
                'status' => 201,
                'mensagem' => 'Cliente criado com sucesso'
            ],
            'retorno' => $this->clienteModel->find($insertedId)
        ]);
    }

    public function update($id = null)
    {
        $cliente = $this->clienteModel->find($id);
        if (!$cliente) {
            return $this->failNotFound("Cliente com ID {$id} não encontrado.");
        }
    
        $data = $this->request->getJSON(true);
    
        if (!$this->validate($this->clienteModel->validationRules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
    
        if ($this->clienteModel->update($id, $data)) {
            return $this->respond([
                'cabecalho' => [
                    'status' => 200,
                    'mensagem' => 'Cliente atualizado com sucesso'
                ],
                'retorno' => $this->clienteModel->find($id)
            ], 200);
        }
    
        return $this->fail("Erro ao atualizar o cliente.", 500);
    }
    

    public function delete($id = null)
    {
        $cliente = $this->clienteModel->find($id);
        if (!$cliente) {
            return $this->failNotFound("Cliente com ID {$id} não encontrado.");
        }
    
        if ($this->clienteModel->delete($id)) {
            return $this->respondDeleted([
                'cabecalho' => [
                    'status' => 200,
                    'mensagem' => 'Cliente excluído com sucesso'
                ]
            ]);
        }
    
        return $this->fail("Erro ao excluir o cliente.", 500);
    }
    
}
