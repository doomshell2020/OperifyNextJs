<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class ParticularpayreceiveTable extends Table
{
	public $name = 'Particularpayreceive';
	public function initialize(array $config)
	{
		$this->table('particular_pay_receive');
		$this->primaryKey('id');

		
	}

	
}

?>
