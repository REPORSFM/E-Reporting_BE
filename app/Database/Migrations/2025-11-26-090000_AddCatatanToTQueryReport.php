<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCatatanToTQueryReport extends Migration
{
    public function up()
    {
        $this->forge->addColumn('TQueryReport', [
            'Catatan' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'Parameter'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('TQueryReport', 'Catatan');
    }
}
