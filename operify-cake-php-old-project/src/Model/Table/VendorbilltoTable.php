<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class VendorbilltoTable extends Table {

    public $name = 'Vendorbillto';
    
    public function initialize(array $config)
    {
			  $this->table('st_vendorbillto');
        $this->primaryKey('id');
			 $this->belongsTo('States', [
            'foreignKey' => 'state_id',
            'joinType' => 'INNER',
        ]);
       	 $this->belongsTo('Cities', [
            'foreignKey' => 'city_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Vendors', [
            'foreignKey' => 'vendor_id',
            'joinType' => 'INNER',
        ]);
	}   
  

}
?>
