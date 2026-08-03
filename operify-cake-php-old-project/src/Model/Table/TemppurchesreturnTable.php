<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class TemppurchesreturnTable extends Table
{

    public function initialize(array $config)
    {

        $this->table('temp_purchesreturn');
        $this->primaryKey('id');

        $this->belongsTo('Additem', [
			'foreignKey' => 'item_id',
			'joinType' => 'Left'
		]);


        $this->belongsTo('Itemcategory', [
			'foreignKey' => 'category_id',
			'joinType' => 'Left'
		]);
    }

}