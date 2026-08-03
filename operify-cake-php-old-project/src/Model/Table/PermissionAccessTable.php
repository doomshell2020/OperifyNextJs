<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;


class PermissionAccessTable extends Table
{

    public $name = 'PermissionAccess';

    public function initialize(array $config)
    {



        $this->table('permission_access');
        $this->primaryKey('id');

        $this->hasOne('PermissionLabel', [
            'foreignKey' => 'p_lable_id',
            'joinType' => 'Left',
        ]);

    }



    public function validationDefault(Validator $validator)
    {
        $validator = new Validator();




        return $validator;
    }
}
