<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class ProductionTable extends Table {

    public $name = 'production';
	
	public function initialize(array $config)
    {     
		$this->table('production');
        $this->primaryKey('id');

        $this->belongsTo('Machinemaster', [
			'foreignKey' => 'machine_id',
			'joinType' => 'INNER',
		]);

		$this->belongsTo('Plannedtype', [
			'foreignKey' => 'planned_type',
			'joinType' => 'left',
		]);
        
	}
}
?>
