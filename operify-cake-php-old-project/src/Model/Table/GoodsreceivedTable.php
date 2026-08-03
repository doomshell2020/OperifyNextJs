<?php

namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Creating Model for Library cup board module
*/
class GoodsreceivedTable extends Table
{
	public $name = 'Goodsreceived';
	public function initialize(array $config)
	{
		$this->table('st_goodsreceive');
		$this->primaryKey('id');

		
	}

	
}

?>
