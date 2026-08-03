<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class QuotationDetailsTable extends Table
{
    public $name = 'QuotationDetails';
    public function initialize(array $config)
    {

        $this->table('st_quotations_details');
		$this->primaryKey('id');

        $this->belongsTo('Additem', [
			'foreignKey' => 'item_id',
			'joinType' => 'Left'
		]);

        $this->belongsTo('Itemcategory', [
			'foreignKey' => 'category_id',
			'joinType' => 'Left'
		]);
        $this->belongsTo('Purchasereturn', [
			'foreignKey' => 'purchasereturn_id',
			'joinType' => 'Left'
		]);
		$this->belongsTo('Taxmaster', [
			'foreignKey' => 'item_tax',
			'joinType' => 'Left',
		]);
    }
  

}