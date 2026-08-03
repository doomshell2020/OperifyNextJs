<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class PermissionModulesTable extends Table {

    public $name = 'PermissionModules';
    
    			  public function initialize(array $config)
    {
       
       
       
			  $this->table('permission_module');
        $this->primaryKey('id');
			
      
      
	}
	
	

  

}
?>
