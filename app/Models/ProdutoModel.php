<?php namespace App\Models;

use CodeIgniter\Model;

class ProdutoModel extends Model {
    protected $table = 'produtos';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nome', 
        'descricao', 
        'preco', 
        'estoque'
    ];
    protected $useTimestamps = true;
    protected $validationRules = [
        'nome' => 'permit_empty|max_length[255]',
        'descricao' => 'max_length[500]', 
        'preco' => 'permit_empty|decimal',
        'estoque' => 'permit_empty|integer'
    ];
}