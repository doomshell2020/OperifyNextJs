<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class LocationsTable extends Table {

    public $name = 'Locations';
	
	public function initialize(array $config)
    {     
		$this->table('locations');
        $this->primaryKey('id');
        
		$this->hasone('Transportfees', [
            'foreignKey' => 'loc_id',
            'joinType' => 'left',
        ]);      
	}
}
?>
