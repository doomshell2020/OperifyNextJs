<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class BranchrequestdetailTable extends Table
{
    public $name = 'branchrequestdetail';
    public function initialize(array $config)
    {

        $this->table('branchrequestdetail');
		$this->primaryKey('id');

        $this->belongsTo('Additem', [
			'foreignKey' => 'item_id',
			'joinType' => 'Left'
		]);

        $this->belongsTo('Itemcategory', [
			'foreignKey' => 'category_id',
			'joinType' => 'Left'
		]);
        $this->belongsTo('Branchrequest', [
			'foreignKey' => 'branchrequest_id',
			'joinType' => 'Left'
		]);
		$this->belongsTo('Taxmaster', [
			'foreignKey' => 'item_tax',
			'joinType' => 'Left',
		]);
    }
  

}
