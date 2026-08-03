<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;


class DeviceTable extends Table
{

    public $name = 'Device';

    public function initialize(array $config)
    {
        $this->table('users_device');
    }

}
?>