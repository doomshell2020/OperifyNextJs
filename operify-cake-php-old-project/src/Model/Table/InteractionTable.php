<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class InteractionTable extends Table {

    public $name = 'Interaction';
    
    public function initialize(array $config)
    {
			  $this->table('prospect_interactions');
        $this->primaryKey('id');
         $this->belongsTo('Enquires', [
            'foreignKey' => 'enquiry_id',
            'joinType' => 'INNER',
        ]);  
		
	}
	
	

 

}
?>
