<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePedidosTable extends Migration
{
    // app/Database/Migrations/CreatePedidosTable.php

    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true, // Adicione isso
                'auto_increment' => true,
            ],
            'cliente_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true, // Adicione isso
            ],
            'produto_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true, // Adicione isso
            ],
            'quantidade' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
            ],
            'total' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2'
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'Em Aberto'
            ],
            'created_at' => [
                'type' => 'DATETIME'
            ],
            'updated_at' => [
                'type' => 'DATETIME'
            ]
        ]);

        $this->forge->addKey('id', true);

        // Garanta que as tabelas clientes e produtos tenham colunas UNSIGNED
        $this->forge->addForeignKey(
            'cliente_id',
            'clientes',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'produto_id',
            'produtos',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Force o uso do InnoDB
        $this->forge->createTable('pedidos', true, ['ENGINE' => 'InnoDB']);
    }
    public function down()
    {
        $this->forge->dropTable('pedidos');
    }
}