<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class OtherfeesTable extends Table {

    public $name = 'Otherfees';
	
    public function initialize(array $config)
    {
			  $this->table('other_feecollection');
        $this->primaryKey('id');
		
	}
	

}
?>

