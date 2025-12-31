<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTQueryReport extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'Kode' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'NamaReport' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'QuerySql' => [
                'type' => 'TEXT',
            ],
            'Parameter' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'IdOrganisasi' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'StAktif' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'CreatedAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'CreatedBy' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'UpdatedAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'UpdatedBy' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
        ]);
        
        $this->forge->addPrimaryKey('Kode');
        $this->forge->createTable('TQueryReport', true);
    }

    public function down()
    {
        $this->forge->dropTable('TQueryReport', true);
    }
}
