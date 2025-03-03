<?php
namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'tipo',
        'documento',
        'nome'
    ];
    protected $useTimestamps = true; 
    protected $validationRules = [
        'tipo' => 'permit_empty|in_list[PF,PJ]',
        'documento' => 'permit_empty|max_length[14]|is_unique[clientes.documento]',
        'nome' => 'permit_empty|max_length[255]'
    ];

    // Modificado para aceitar tipo como argumento
    public function validateDocumento($documento, $tipo)
    {
        if ($tipo === 'PF' && !validaCPF($documento)) {
            return false;
        } elseif ($tipo === 'PJ' && !validaCNPJ($documento)) {
            return false;
        }
        return true;
    }
    public function getValidationErrors()
    {
        return $this->errors();
    }
}
