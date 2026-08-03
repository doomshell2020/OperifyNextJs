<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class MaintenanceTable extends Table
{

  public $name = 'Maintenance';

  public function initialize(array $config)
  {
    $this->table('maintenance');
    $this->primaryKey('id');

    $this->belongsTo('Machinemaster', [
      'foreignKey' => 'machine_id',
      'joinType' => 'LEFT',
    ]);


  }


}
?>