<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Auth\DefaultPasswordHasher;

class UsersTable extends Table {

    public $name = 'Users';
    protected function _setPassword($password)
    {
        return (new DefaultPasswordHasher)->hash($password);
    }
    					
	    
	 


}
?>
