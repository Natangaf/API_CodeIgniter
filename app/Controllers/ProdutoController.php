<?php

namespace App\Controllers;

use App\Models\ProdutoModel;
use CodeIgniter\RESTful\ResourceController;

class ProdutoController extends ResourceController
{
    protected $produtoModel;

    public function __construct()
    {
        $this->produtoModel = new ProdutoModel();
        helper(['validation']);
    }

    public function index()
    {
        $allProducts = $this->produtoModel->findAll();

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Lista de produtos recuperada com sucesso'
            ],
            'retorno' => $allProducts
        ], 200);
    }

    public function show($id = null)
    {
        $produto = $this->produtoModel->find($id);

        if (!$produto) {
            return $this->failNotFound("Produto com ID {$id} não encontrado.");
        }

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Produto recuperado com sucesso'
            ],
            'retorno' => $produto
        ], 200);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        if (!$this->validate($this->produtoModel->validationRules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $insertedId = $this->produtoModel->insert($data);

        if (!$insertedId) {
            return $this->failServerError('Erro ao criar produto');
        }

        return $this->respondCreated([
            'cabecalho' => [
                'status' => 201,
                'mensagem' => 'Produto criado com sucesso'
            ],
            'retorno' => $this->produtoModel->find($insertedId)
        ]);
    }

    public function update($id = null)
    {
        $produto = $this->produtoModel->find($id);
        if (!$produto) {
            return $this->failNotFound("Produto com ID {$id} não encontrado.");
        }

        $data = $this->request->getJSON(true);

        if (!$this->validate($this->produtoModel->validationRules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        if ($this->produtoModel->update($id, $data)) {
            return $this->respond([
                'cabecalho' => [
                    'status' => 200,
                    'mensagem' => 'Produto atualizado com sucesso'
                ],
                'retorno' => $this->produtoModel->find($id)
            ], 200);
        }

        return $this->fail("Erro ao atualizar o produto.", 500);
    }

    public function delete($id = null)
    {
        $produto = $this->produtoModel->find($id);
        if (!$produto) {
            return $this->failNotFound("Produto com ID {$id} não encontrado.");
        }

        if ($this->produtoModel->delete($id)) {
            return $this->respondDeleted([
                'cabecalho' => [
                    'status' => 200,
                    'mensagem' => 'Produto excluído com sucesso'
                ]
            ]);
        }

        return $this->fail("Erro ao excluir o produto.", 500);
    }
}