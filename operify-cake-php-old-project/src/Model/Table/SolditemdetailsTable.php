<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class SolditemdetailsTable extends Table
{
    public $name = 'Solditemdetails';
   
		

    public function initialize(array $config)
    {

        $this->table('solditemsdetail');
		$this->primaryKey('id');

        $this->belongsTo('Additem', [
			'foreignKey' => 'item_id',
			'joinType' => 'Left'
		]);

        $this->belongsTo('Itemcategory', [
			'foreignKey' => 'category_id',
			'joinType' => 'Left'
		]);

        $this->belongsTo('Solditem', [
			'foreignKey' => 'sold_id',
			'joinType' => 'Left'
		]);

		$this->belongsto('Students', [
			'foreignKey' => 'id',
			'joinType' => 'Left'
		]);

    }
  

}
