<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;


class StudentfeependingTable extends Table
{

    public $name = 'Studentfeepending';

    public function initialize(array $config)
    {
        $this->table('studentfee_pending');
        $this->primaryKey('id');
        $this->belongsTo('Students', [
            'foreignKey' => 's_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Studentfees', [
            'foreignKey' => 'r_id',
            'joinType' => 'INNER',
        ]);
    }


    public function validationDefault(Validator $validator)
    {
        $validator = new Validator();
    }
}
