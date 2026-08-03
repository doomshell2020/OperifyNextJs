<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class QuotationSendTable extends Table
{
    public $name = 'QuotationSend';
    public function initialize(array $config)
    {

        $this->table('st_send_quotations');
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