<?php
namespace App\Model\Table;
use Cake\Validation\Validator;
use Cake\ORM\Table;

class EmailtemplateTable extends Table {

	public function initialize(array $config)
	{
		$this->table('template');
		$this->primaryKey('id');  
	         
	}
}
?>
