<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class PermissionLabelTable extends Table
{

    public $name = 'PermissionLabel';

    public function initialize(array $config)
    {



        $this->table('permission_label');
        $this->primaryKey('id');

        $this->belongsTo('Manager', [
            'foreignKey' => 'manager_id'
        ]);



    }



    public function validationDefault(Validator $validator)
    {
        $validator = new Validator();




        return $validator;

    }

}
?>