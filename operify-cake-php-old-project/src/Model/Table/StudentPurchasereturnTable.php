<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class StudentPurchasereturnTable extends Table
{
    public $name = 'StudentPurchasereturn';
    public function initialize(array $config)
    {

        $this->table('st_student_purchasereturn');
		$this->primaryKey('id');

        $this->belongsTo('Additem', [
			'foreignKey' => 'item_id',
			'joinType' => 'Left'
		]);

        $this->belongsTo('Itemcategory', [
			'foreignKey' => 'category_id',
			'joinType' => 'Left'
		]);
		$this->belongsTo('Taxmaster', [
			'foreignKey' => 'item_tax',
			'joinType' => 'Left',
		]);

		$this->belongsTo('Students', [
			'foreignKey' => 'stu_id',
			'joinType' => 'Left',
		]);
    }
  

}
