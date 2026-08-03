<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class SalesreturndetilsTable extends Table
{
    public $name = 'Salesreturndetils';
    public function initialize(array $config)
    {

        $this->table('salesreturndetails');
		$this->primaryKey('id');

        $this->belongsTo('Additem', [
			'foreignKey' => 'item_id',
			'joinType' => 'Left'
		]);

        $this->belongsTo('Itemcategory', [
			'foreignKey' => 'category_id',
			'joinType' => 'Left'
		]);
        $this->belongsTo('Salesreturn', [
			'foreignKey' => 'salereturn_id',
			'joinType' => 'Left'
		]);
		$this->belongsTo('Taxmaster', [
			'foreignKey' => 'item_tax',
			'joinType' => 'Left',
		]);
    }
  

}
