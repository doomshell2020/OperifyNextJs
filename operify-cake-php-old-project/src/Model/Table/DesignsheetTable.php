<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class DesignsheetTable extends Table {

    public $name = 'Designsheet';
    
    public function initialize(array $config)
    {
			  $this->table('designsheet');
       
	}
	

}
?>
