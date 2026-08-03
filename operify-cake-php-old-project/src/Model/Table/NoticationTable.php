<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class NoticationTable extends Table {

    public $name = 'Notication';
	
	

    public function initialize(array $config)
    {
			  $this->table('notification');
        $this->primaryKey('id');
			 
	}

}
?>