<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class BomfinishedproductTable extends Table {

    public $name = 'Bomfinishedproduct';
	
    public function initialize(array $config)
	{
		$this->table('bom_finisedproduct');
		$this->primaryKey('id');

		$this->belongsTo('Additem', [
            'foreignKey' => 'product_id',
            'joinType' => 'LEFT',
        ]);
		
	}

}