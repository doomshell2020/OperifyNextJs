<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class ZoomUsersTable extends Table {

    public $name = 'ZoomUsers';
	  public function initialize(array $config){
		$this->table('zoom_users');
		$this->primaryKey('id');
		
		$this->belongsTo('Classes', [
	  'className' => 'Classes',
	  'foreignKey' => 'class_id',
	  'propertyName' => 'Classes',
	   'joinType' => 'INNER',
	  
   ]);
   
   $this->belongsTo('Sections', [
	   'className' => 'Sections',
		'foreignKey' => 'section_id',
		'propertyName' => 'Sections',
		 'joinType' => 'INNER',
	  
   ]);
	}
 
}
?>
