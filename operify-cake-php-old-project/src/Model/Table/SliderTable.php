<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class SliderTable extends Table {

    public $name = 'Slider';
	
	

    public function initialize(array $config)
    {
			  $this->table('slider');
        $this->primaryKey('id');
			 
	}

}
?>