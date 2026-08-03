<?php

namespace App\Controller\Admin;
use App\Controller\AppController;
use Cake\Core\Configure; 
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Datasource\ConnectionManager;
use Cake\View\Helper;
class StudentsController extends AppController
{	public $helpers = ['CakeJs.Js'];
		public function initialize(){	
		//load all models
		$this->loadModel('Sections');
		$this->loadModel('Classes');
	$this->loadModel('Houses');
	

	
		parent::initialize();
	}
	
	
	// show all data in listing with pagination
	public function index(){ 
		$this->viewBuilder()->layout('admin');
		//show all data in listing page


$sections = $this->Sections->find('all')->select(['id', 'title'])->where(['status' => 1])->order(['title' => 'ASC'])->toArray();



$classes = $this->Classes->find('all')->select(['id', 'title'])->where(['status' => 1])->order(['id' => 'ASC'])->toArray();





$houses = $this->Houses->find('all')->select(['id', 'name'])->where(['status' => 1])->order(['id' => 'ASC'])->toArray();

$this->set(compact('sections','classes','houses'));


		$student_data = $this->Students->find('all')->contain(['Classes','Sections'])->toarray();
		$this->set('students',$student_data);
	}
	// create view functionality
	public function view($id){    
		$this->viewBuilder()->layout('admin');
		//get all data particular id
		$students = $this->Students->get($id);
		$this->set(compact('students'));
	}
	// create delete functionality
	public function delete($id){
	    	$contact = $this->Students->get($id);
		//delete particular entry
	    if ($this->Students->delete($contact)) {
		$this->Flash->success(__('The student with id: {0} has been deleted.', h($id)));
		return $this->redirect(['action' => 'index']);
	    }
	}
	
		public function studentdetail(){
	    	
	    
	}
//add students
	public function add($id=null){ 
		$this->viewBuilder()->layout('admin');
		if(isset($id) && !empty($id)){
			$students = $this->Students->get($id);
		}else{
			
$studentsid = $this->Students->find('all')->order(['id' => 'DESC'])->toArray();




$sections = $this->Sections->find('all')->select(['id', 'title'])->where(['status' => 1])->order(['title' => 'ASC'])->toArray();



$classes = $this->Classes->find('all')->select(['id', 'title'])->where(['status' => 1])->order(['id' => 'ASC'])->toArray();





$houses = $this->Houses->find('all')->select(['id', 'name'])->where(['status' => 1])->order(['id' => 'ASC'])->toArray();


$this->set(compact('studentsid','sections','classes','houses'));
			$students = $this->Students->newEntity();
			$this->request->data['status'] = '1';
		}
		if ($this->request->is(['post', 'put'])) {
 		
				// save all data in database
				 $students = $this->Students->patchEntity($students, $this->request->data);
				if ($this->Students->save($students)) {
					$this->Flash->success(__('Your student has been saved.'));
					return $this->redirect(['action' => 'index']);	
				  }else{    //check validation error
					if($students->errors()){
					         $error_msg = [];
						foreach( $students->errors() as $errors){
						    if(is_array($errors)){
							foreach($errors as $error){
							    $error_msg[]    =   $error;
							}
						    }else{
							$error_msg[]    =   $errors;
						    }
						}
					if(!empty($error_msg)){
					    $this->Flash->error(
						__("Please fix the following error(s): ".implode("\n \r", $error_msg))
					    );
					}
				    }
				}
                 }
		$this->set('students', $students);
	}



// search functionality
public function search(){ 
	
		//show all data in listing page
//connection
  $conn = ConnectionManager::get('default');

$year=$this->request->data['acedmicyear']; 
$class=$this->request->data['class_id'];
$admission=$this->request->data['admissionyear'];
$section=$this->request->data['section_id']; 
$enroll=$this->request->data['enroll'];
$fname=$this->request->data['fname']; 

$detail="SELECT Students.id,, Classes.title, Sections.title FROM `students` Students LEFT JOIN classes Classes ON Students.`class_id` = Classes.id LEFT JOIN sections Sections ON Students.`section_id` = Sections.id    WHERE  1=1 ";

 $cond = ' ';
if(!empty($year))
{
 
$cond.=" AND Students.acedmicyear LIKE '".$year."%' ";

}
 
 
if(!empty($class))
{
 
    $cond.=" AND Students.class_id LIKE '".$class."%' ";
        

}

if(!empty($admission))
{
 
    $cond.=" AND Students.admissionyear LIKE '".$admission."%' ";
        

}


  
if(!empty($section))
{
 
    $cond.=" AND Students.section_id LIKE '".$section."%' ";
        

}
  
if(!empty($enroll))
{
 
    $cond.=" AND Students.enroll LIKE '".$enroll."%' ";
        

}

if(!empty($fname))
{
 
    $cond.=" AND UPPER(Students.fname) LIKE '".strtoupper($fname)."%' ";
        

}

 $detail = $detail.$cond;
   $SQL = $detail." ORDER BY Students.id ASC";  
 echo $SQL;
$results = $conn->execute( $SQL )->fetchAll('assoc');
pr($results);
	$this->set('students', $results);

	}

	//status update functionality
	public function status($id,$status){

		$statusquery = $this->Subjects->find('all')->where(['Subjects.status' => 1])->count();
		if(isset($id) && !empty($id)){
		if($status == 1 ){
			
				$status = 0;
			//status update
				$classes = $this->Subjects->get($id);
				$classes->status = $status;
				if ($this->Subjects->save($classes)) {
					$this->Flash->success(__('Your subject status has been updated.'));
					return $this->redirect(['action' => 'index']);	
				}
		}else{
			if($statusquery < 8 ){
				$status = 1;
			//status update
			$classes = $this->Subjects->get($id);
			$classes->status = $status;
			if ($this->Subjects->save($classes)) {
				$this->Flash->success(__('Your subject status has been updated.'));
				return $this->redirect(['action' => 'index']);	
			}
			
			}else{
				$this->Flash->error(__('8 Entries all ready activate. Please deactivate one of activate'));
				return $this->redirect(['action' => 'index']);	
	 		}
		}

	}
		
	}
	
	
	
	
	
	
	

		
}
