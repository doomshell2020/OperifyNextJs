<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class StudentPurchasereturnDetailsTable extends Table
{
    public $name = 'StudentPurchasereturnDetails';
    public function initialize(array $config)
    {

        $this->table('st_student_purchasereturn_details');
		$this->primaryKey('id');

        $this->belongsTo('Additem', [
			'foreignKey' => 'item_id',
			'joinType' => 'Left'
		]);

        $this->belongsTo('Itemcategory', [
			'foreignKey' => 'category_id',
			'joinType' => 'Left'
		]);
        $this->belongsTo('StudentPurchasereturn', [
			'foreignKey' => 'stupurchasereturn_id',
			'joinType' => 'Left'
		]);
		$this->belongsTo('Taxmaster', [
			'foreignKey' => 'item_tax',
			'joinType' => 'Left',
		]);
    }
  

}