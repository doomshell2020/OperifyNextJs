<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class EventsTable extends Table {

    public $name = 'Events';
	
	
	
	
	
	 public function initialize(array $config)
    {
       
       
       
			  $this->table('events');
        $this->primaryKey('id');
        	 $this->belongsTo('Eventtypes', [
            'foreignKey' => 'eventt',
            'joinType' => 'INNER',
        ]);  
        
	}
    

}
?>
