<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class ItemlocationTable extends Table
{
	public $name = 'Itemlocation';
	public function initialize(array $config)
	{
		$this->table('st_itemlocation');
		$this->primaryKey('id');
	}

	
}

?>
