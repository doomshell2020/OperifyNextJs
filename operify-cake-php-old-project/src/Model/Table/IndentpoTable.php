<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class IndentpoTable extends Table {

    public $name = 'Indentpo';
    
    public function initialize(array $config)
    {
			  $this->table('indentpo');
	}
	

}
?>
