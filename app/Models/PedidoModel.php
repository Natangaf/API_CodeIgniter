<?php
namespace App\Models;

use CodeIgniter\Model;

class PedidoModel extends Model
{
    protected $table = 'pedidos';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'cliente_id',
        'produto_id',
        'quantidade',
        'status',
        'total'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'cliente_id' => 'required|integer',
        'produto_id' => 'required|integer',
        'quantidade' => 'required|integer|greater_than[0]',
        'status' => 'required|in_list[Em Aberto,Pago,Cancelado]'
    ];

    public function calcularTotal(int $produtoId, int $quantidade)
    {
        $produto = model('ProdutoModel')->find($produtoId);
        return $produto['preco'] * $quantidade;
    }
}