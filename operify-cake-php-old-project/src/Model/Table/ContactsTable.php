<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class ContactsTable extends Table
{

    public $name = 'Contacts';

    public function initialize(array $config)
    {



        $this->table('cms_contacts');
        $this->primaryKey('id');




    }


}
?>