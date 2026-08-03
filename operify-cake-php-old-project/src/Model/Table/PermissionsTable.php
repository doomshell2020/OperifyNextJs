<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class PermissionsTable extends Table {

    public $name = 'Permissions';
    
    			  public function initialize(array $config)
    {
       
       
       
			  $this->table('permissions');
        $this->primaryKey('id');
			
      
      
	}
	
	

   

}
?>
