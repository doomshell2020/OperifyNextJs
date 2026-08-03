<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class LanguageTable extends Table {

    public $name = 'Language';
    
    public function initialize(array $config)
    {
			  $this->table('language');
        $this->primaryKey('id');
		
	}
	
	

 

}
?>
