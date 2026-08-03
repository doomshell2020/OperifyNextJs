<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class PurchasedetailsTable extends Table {

    public $name = 'Purchasedetails';
	
	public function initialize(array $config)
    {       
     $this->table('purchase_details');
     $this->belongsTo('Product', [
        'foreignKey' => 'product_id',
        'joinType' => 'INNER',
    ]);

    $this->belongsTo('Attributes', [
        'foreignKey' => 'attribute_id',
        'joinType' => 'INNER',
    ]);


              
    }

  

}
?>
