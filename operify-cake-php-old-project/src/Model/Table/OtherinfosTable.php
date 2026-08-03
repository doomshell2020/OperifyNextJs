<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class OtherinfosTable extends Table
{

    public $name = 'Otherinfos';
    public function initialize(array $config)
    {

        $this->table('py_otherinfos');
    }

    /*   public function validationDefault(Validator $validator)
{
$validator = new Validator();

$validator
->requirePresence('title')
->notEmpty('title', 'Please fill title')

->requirePresence('type')
->notEmpty('type', 'Please fill description');

return $validator;

}
 */
}
