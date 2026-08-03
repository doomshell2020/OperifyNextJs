<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Student extends Entity
{
    protected function _getFullName()
    {
        if ($this->middlename) {
            return $this->fname . ' ' . $this->middlename . ' ' . $this->lname;
        } else {
            return $this->fname . '  ' . $this->lname;
        }
    }
}