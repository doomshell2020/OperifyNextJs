<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class GatepassTable extends Table
{

    public $name = 'Gatepass';

    public function initialize(array $config)
    {
        $this->table('gatepass');
        $this->primaryKey('id');

        $this->belongsTo('Students', [
            'foreignKey' => 'stud_id',
        ]);

    }

}
