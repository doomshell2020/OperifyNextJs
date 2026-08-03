<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class EventtypesTable extends Table {

    public $name = 'Eventtypes';
	
	
	
	
	
	 public function initialize(array $config)
    {
       
       
       
			  $this->table('eventtypes');
        $this->primaryKey('id');
        
	}
  

}
?>
