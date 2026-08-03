<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class FinishedprocessTable extends Table {

    public $name = 'Finishedprocess';
    
    public function initialize(array $config)
    {
			  $this->table('finishedproduct_process');
	}
}
?>
