<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class PurchaseorderDetailsTable extends Table {

    public $name = 'PurchaseorderDetails';
	
	public function initialize(array $config)
    {       
        $this->table('st_purchaseorderDetails');
		$this->primaryKey('id');

        $this->belongsTo('Additem', [
			'foreignKey' => 'item_id',
			'joinType' => 'INNER'
		]);

        $this->belongsTo('Taxmaster', [
			'foreignKey' => 'tax_id',
			'joinType' => 'Left'
		]);
          
          
    }

  

}
?>
