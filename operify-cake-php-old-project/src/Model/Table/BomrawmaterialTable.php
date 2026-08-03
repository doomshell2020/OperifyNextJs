<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class BomrawmaterialTable extends Table {

    public $name = 'Bomrawmaterial';
	
    public function initialize(array $config)
	{
		$this->table('bom_rawmaterial');
		$this->primaryKey('id');

		$this->belongsTo('Additem', [
            'foreignKey' => 'product_id',
            'joinType' => 'LEFT',
        ]);
	}
}
