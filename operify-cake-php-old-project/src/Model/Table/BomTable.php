<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class BomTable extends Table {

    public $name = 'Bom';
    
    public function initialize(array $config)
    {
			  $this->table('bom');
	}
	

}
?>
