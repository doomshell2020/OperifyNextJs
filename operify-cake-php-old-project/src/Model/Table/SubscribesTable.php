<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;



class SubscribesTable extends Table {

    public $name = 'Subscribes';
	 public function validationDefault(Validator $validator)
    		{
			$validator = new Validator();
			$validator
				->add(
				'email', 
				['unique' => [
				    'rule' => 'validateUnique', 
				    'provider' => 'table', 
				    'message' => 'Email ID should be unique']
				]
			    );
			
			 return $validator;
		}
	
}
?>
