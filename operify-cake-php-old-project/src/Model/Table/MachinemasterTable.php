<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class MachinemasterTable extends Table {

    public $name = 'machine_master';
	
	public function initialize(array $config)
    {     
		$this->table('machine_master');
        $this->primaryKey('id');
        
	}
}
?>
