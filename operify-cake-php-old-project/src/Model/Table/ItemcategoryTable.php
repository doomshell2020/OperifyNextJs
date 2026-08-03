<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class ItemcategoryTable extends Table
{
	public $name = 'Itemcategory';
	public function initialize(array $config)
	{
		$this->table('st_categorymaster');
		$this->primaryKey('id');
	}

	
}

?>
