<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\PedidoModel;
use App\Models\ProdutoModel;

class PedidoController extends ResourceController
{
    protected $pedidoModel;
    protected $produtoModel;

    public function __construct()
    {
        $this->pedidoModel = new PedidoModel();
        $this->produtoModel = new ProdutoModel();
        helper(['form', 'url']);
    }

    // Listar todos os pedidos
    public function index()
    {
        $pedidos = $this->pedidoModel->findAll();
        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Lista de pedidos recuperada com sucesso'
            ],
            'retorno' => $pedidos
        ]);
    }

    // Mostrar um pedido específico
    public function show($id = null)
    {
        $pedido = $this->pedidoModel->find($id);
        if (!$pedido) {
            return $this->failNotFound("Pedido ID {$id} não encontrado");
        }

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Pedido recuperado com sucesso'
            ],
            'retorno' => $pedido
        ]);
    }

    // Criar novo pedido
    public function create()
    {
        $data = $this->request->getJSON(true);

        // Validação
        if (!$this->validate($this->pedidoModel->validationRules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Verificar estoque
        $produto = $this->produtoModel->find($data['produto_id']);
        if (!$produto || $produto['estoque'] < $data['quantidade']) {
            return $this->fail("Estoque insuficiente para o produto ID {$data['produto_id']}");
        }

        // Calcular total
        $data['total'] = $produto['preco'] * $data['quantidade'];

        // Criar pedido
        $pedidoId = $this->pedidoModel->insert($data);
        if (!$pedidoId) {
            return $this->failServerError('Erro ao criar pedido');
        }

        // Atualizar estoque
        $this->produtoModel->update($data['produto_id'], [
            'estoque' => $produto['estoque'] - $data['quantidade']
        ]);

        return $this->respondCreated([
            'cabecalho' => [
                'status' => 201,
                'mensagem' => 'Pedido criado com sucesso'
            ],
            'retorno' => $this->pedidoModel->find($pedidoId)
        ]);
    }
    // Atualizar pedido (PUT - completo)
    public function update($id = null)
    {
        $pedido = $this->pedidoModel->find($id);
        if (!$pedido) {
            return $this->failNotFound("Pedido ID {$id} não encontrado");
        }

        $data = $this->request->getJSON(true);

        // Validação
        if (!$this->validate($this->pedidoModel->validationRules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Verificar alterações críticas
        $mudouProduto = ($data['produto_id'] != $pedido['produto_id']);
        $mudouQuantidade = ($data['quantidade'] != $pedido['quantidade']);

        if ($mudouProduto || $mudouQuantidade) {
            // Buscar produto original
            $produtoOriginal = $this->produtoModel->find($pedido['produto_id']);

            // Restaurar estoque original
            $this->produtoModel->update($pedido['produto_id'], [
                'estoque' => $produtoOriginal['estoque'] + $pedido['quantidade']
            ]);

            // Buscar novo produto
            $novoProduto = $this->produtoModel->find($data['produto_id']);
            if (!$novoProduto || $novoProduto['estoque'] < $data['quantidade']) {
                return $this->fail("Estoque insuficiente para o novo produto");
            }

            // Atualizar novo estoque
            $this->produtoModel->update($data['produto_id'], [
                'estoque' => $novoProduto['estoque'] - $data['quantidade']
            ]);

            // Recalcular total
            $data['total'] = $novoProduto['preco'] * $data['quantidade'];
        }

        // Atualizar pedido
        if ($this->pedidoModel->update($id, $data)) {
            return $this->respond([
                'cabecalho' => [
                    'status' => 200,
                    'mensagem' => 'Pedido atualizado com sucesso'
                ],
                'retorno' => $this->pedidoModel->find($id)
            ]);
        }

        return $this->failServerError('Erro ao atualizar pedido');
    }

    // Atualizar parcial (PATCH - status)
    public function patch($id = null)
    {
        $pedido = $this->pedidoModel->find($id);
        if (!$pedido) {
            return $this->failNotFound("Pedido ID {$id} não encontrado");
        }

        $data = $this->request->getJSON(true);

        // Validar apenas status
        if (!isset($data['status'])) {
            return $this->failValidationErrors(['status' => 'Campo status é obrigatório']);
        }

        $novoStatus = $data['status'];
        $statusAnterior = $pedido['status'];

        // Controle de estoque para cancelamento
        if ($novoStatus === 'Cancelado' && $statusAnterior !== 'Cancelado') {
            $produto = $this->produtoModel->find($pedido['produto_id']);
            $this->produtoModel->update($pedido['produto_id'], [
                'estoque' => $produto['estoque'] + $pedido['quantidade']
            ]);
        }

        // Reverter cancelamento
        if ($statusAnterior === 'Cancelado' && $novoStatus !== 'Cancelado') {
            $produto = $this->produtoModel->find($pedido['produto_id']);
            if ($produto['estoque'] < $pedido['quantidade']) {
                return $this->fail("Estoque insuficiente para reativar o pedido");
            }
            $this->produtoModel->update($pedido['produto_id'], [
                'estoque' => $produto['estoque'] - $pedido['quantidade']
            ]);
        }

        // Atualizar status
        if ($this->pedidoModel->update($id, ['status' => $novoStatus])) {
            return $this->respond([
                'cabecalho' => [
                    'status' => 200,
                    'mensagem' => 'Status do pedido atualizado'
                ],
                'retorno' => $this->pedidoModel->find($id)
            ]);
        }

        return $this->failServerError('Erro ao atualizar status');
    }

    // Excluir pedido
    public function delete($id = null)
    {
        $pedido = $this->pedidoModel->find($id);
        if (!$pedido) {
            return $this->failNotFound("Pedido ID {$id} não encontrado");
        }

        // Restaurar estoque
        $produto = $this->produtoModel->find($pedido['produto_id']);
        $this->produtoModel->update($pedido['produto_id'], [
            'estoque' => $produto['estoque'] + $pedido['quantidade']
        ]);

        // Excluir pedido
        $this->pedidoModel->delete($id);

        return $this->respondDeleted([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Pedido excluído e estoque restaurado'
            ]
        ]);
    }
}