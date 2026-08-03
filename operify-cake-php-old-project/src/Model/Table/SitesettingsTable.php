<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Auth\DefaultPasswordHasher;

class SitesettingsTable extends Table {

    public $name = 'Sitesettings'; 
	public function initialize(array $config)
	{

        $this->table('sitesettings');
		$this->primaryKey('id');

        $this->HasOne('SitesettingsDetails', [
			'foreignKey' => 'sitesettings_id',
			'joinType' => 'Left'
		]);

    }
}
?>
