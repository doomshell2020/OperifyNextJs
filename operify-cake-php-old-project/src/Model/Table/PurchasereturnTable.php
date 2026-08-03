<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class PurchasereturnTable extends Table
{
    public $name = 'Purchasereturn';
    public function initialize(array $config)
    {

        $this->table('st_purchasereturn');
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

		$this->belongsTo('Vendor', [
			'foreignKey' => 'vendor_id',
			'joinType' => 'Left',
		]);
    }
  

}
