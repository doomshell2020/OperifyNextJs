<?php
namespace App\Model\Table;
use Cake\Validation\Validator;
use Cake\ORM\Table;

class TypeTable extends Table {

	public function initialize(array $config)
	{
		$this->table('type');
		$this->primaryKey('id');  
		         
	}
}
?>
