<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class CopperstockTable extends Table {

    public $name = 'Copperstock';
    
    public function initialize(array $config)
    {
			  $this->table('copper_stock');
       
              $this->belongsTo('Additem', [
                'foreignKey' => 'product_id',
                'joinType' => 'INNER',
            ]);
	}

}
?>
