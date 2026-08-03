<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class PaymenttermsTable extends Table {

    public $name = 'Paymentterms';
    
    public function initialize(array $config)
    {
		$this->table('st_paymentterms');
        $this->primaryKey('id');
			 
	}   
  

}
?>
