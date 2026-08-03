<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class ZoomLiveClassesDetailTable extends Table {

    public $name = 'ZoomLiveClassesDetail';
	  public function initialize(array $config){
		$this->table('zoom_live_classes_detail');
		$this->primaryKey('id');
		
	}
 
}
?>
