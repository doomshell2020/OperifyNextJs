<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class ZoomLiveClassesTable extends Table {

    public $name = 'ZoomLiveClasses';
	  public function initialize(array $config){
		$this->table('zoom_live_classes');
		$this->primaryKey('id');

		$this->belongsTo('Classes', [
	  'className' => 'Classes',
	  'foreignKey' => 'class_id',
	  'propertyName' => 'Classes',
	   'joinType' => 'INNER',
	  
   ]);
   
   $this->belongsTo('Subjects', [
	   'className' => 'Subjects',
		'foreignKey' => 'subject_id',
		'propertyName' => 'Subjects',
		 'joinType' => 'INNER',
	  
   ]);
		
   $this->belongsTo('Employees', [
	'className' => 'Employees',
	 'foreignKey' => 'teacher_id',
	 'propertyName' => 'Employees',
	  'joinType' => 'INNER',
   
]);

	}
 
}
?>
