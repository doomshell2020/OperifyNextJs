<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\View;

use Cake\View\View;
use Cake\ORM\TableRegistry;


/**
 * Application View
 *
 * Your application’s default view class
 *
 * @link http://book.cakephp.org/3.0/en/views.html#the-app-view
 */
class AppView extends View
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading helpers.
     *
     * e.g. `$this->loadHelper('Html');`
     *
     * @return void
     */
    public function initialize()
    {
		 parent::initialize();
        $this->loadHelper('Comman');
    }
    public function findpaymentmode($id = null)
    {
        $articles = TableRegistry::get('Ledger');

        return $articles->find('all')->select(['name'])->where(['id' => $id])->first();

    }
    public function finddeptmode($id = null)
    {
        $articles = TableRegistry::get('p_departments');

        return $articles->find('all')->select(['name'])->where(['id' => $id])->first();

    }
    public function finddesgmode($id = null)
    {
        $articles = TableRegistry::get('p_designations');

        return $articles->find('all')->select(['name'])->where(['id' => $id])->first();

    }
    public  function payroll(){ 
        $articles = TableRegistry::get('payroll');
        return $articles->find('all')->first();
    }
    public function classteachers($class_id=null,$section_id=null,$t_type=null)
    {
        $articles = TableRegistry::get('Classteachers');
       $class=$articles->find('all')->contain(['Employees'])->where(['Classteachers.class_id'=>$class_id,'Classteachers.section_id'=>$section_id,'Classteachers.teacher_type'=>$t_type])->first();
       
       $teacher=$class['employee']['fname'].' '.$class['employee']['middlename'].' '.$class['employee']['lname'].'-'.$class['employee']['id'];
       return $teacher; 
    }

}
