<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class ManagesettingsTable extends Table {

public $name = 'Managesettings';
				
public function initialize(array $config)
    {
       
	 $this->table('managesettings');
     $this->primaryKey('id');
        	
	}

}
?>
