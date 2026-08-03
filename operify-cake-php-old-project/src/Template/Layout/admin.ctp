<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = 'CakePHP: the rapid development php framework';
?>
<?php $rolepresent = $this->request->session()->read('Auth.User.role_id');
if($rolepresent == 101){
    echo $this->element('admin/header');
} else{
    echo $this->element('admin/headernew') ;
}
 // echo $this->element('admin/header') ?>
<?php // echo $this->element('admin/headernew') ?>

<?php echo $this->element('admin/menu') ?>
<?php $rolepresent = $this->request->session()->read('Auth.User.role_id');
// pr($rolepresent);die;
// if($rolepresent == 101){
//     echo $this->element('admin/left');
// } else{
//     echo $this->element('admin/leftnew') ;
// }
 //echo $this->element('admin/left') ;
 ?>


<?php //echo $this->Flash->render() ?>

<?= $this->fetch('content') ?>

<?= $this->element('admin/footer') ?>