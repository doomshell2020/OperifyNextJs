<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;


class LocationsController extends AppController
{
	//initialize component
	public function initialize()
	{
		parent::initialize();
		$this->loadModel('Locations');
		$this->loadModel('Routemaster');

	}

	public function index($id = null)
	{
		$this->viewBuilder()->layout('admin');
		//show data in listing
		$locationss = $this->Locations->find('all')->order(['id' => 'ASC'])->toarray();
		$this->paginate($service_data);
		//pr($locations); die;
		$this->set('locationss', $locationss);
		$this->set('ids', $id);
		if (isset($id) && !empty($id)) {

			$locations = $this->Locations->get($id);
		} else {
			$locations = $this->Locations->newEntity();
		}
		if ($this->request->is(['post', 'put'])) {

			$locations = $this->Locations->patchEntity($locations, $this->request->data);
			//pr($locations); die;
			if ($this->Locations->save($locations)) {
				$this->Flash->success(__('Locations has been saved.'));
				return $this->redirect(['action' => 'index']);
			}
		}
		$this->set('locations', $locations);
	}

	public function add($id = null)
	{
		$this->viewBuilder()->layout('admin');
		if (isset($id) && !empty($id)) {
			//using for edit
			$locations = $this->Locations->get($id);
		} else {
			//using for new entry
			$locations = $this->Locations->newEntity();
			$this->request->data['status'] = '1';
		}
		if ($this->request->is(['post', 'put'])) {

			//	pr($this->request->data); die;

			// save all data in database
			$locations = $this->Locations->patchEntity($locations, $this->request->data);
			//pr($locations); die;
			if ($this->Locations->save($locations)) {
				$this->Flash->success(__('Locations has been saved.'));
				return $this->redirect(['action' => 'index']);
			} else { //pr($classes->errors());
				//validation error
				if ($locations->errors()) {
					$error_msg = [];
					foreach ($locations->errors() as $errors) {
						if (is_array($errors)) {
							foreach ($errors as $error) {
								$error_msg[]    =   $error;
							}
						} else {
							$error_msg[]    =   $errors;
						}
					}
					if (!empty($error_msg)) {
						$this->Flash->error(
							__("Please fix the following error(s): " . implode("\n \r", $error_msg))
						);
					}
				}
			}
		}

		$this->set('locations', $locations);
	}

	public function sort()
	{
		$this->viewBuilder()->layout('admin');
		$id = $this->request->data['id'];
		if (isset($id) && !empty($id)) {
			//using for edit
			$classes = $this->Locations->get($id);
		} else {
			//using for new entry
			$classes = $this->Locations->newEntity();
		}

		if ($this->request->is(['post', 'put'])) {

			//$this->request->data = $this->request->data['sort']; 
			$classes->sort = $this->request->data['sort'];

			if ($this->Locations->save($classes)) {
				echo $classes['sort'];
			} else {
				echo 'wrong';
			}
		}
		die;
	}

	//view functionality
	public function view($id=null)
	{
		$this->viewBuilder()->layout('admin');
		//get data from paricular id
		$classes = $this->Locations->get($id);
		$this->set(compact('classes'));
	}

	//delete functionality
	public function delete($id)
	{
		//$this->request->allowMethod(['post', 'delete']);
		$classes = $this->Locations->get($id);
		//delete pariticular entry
		try {

			if ($this->Locations->delete($classes)) {
				$this->Flash->success(__(' Locations with id: {0} has been deleted.', h($id)));
				return $this->redirect(['action' => 'index']);
			}
		} catch (\PDOException $e) {
			//  $error = 'The item you are trying to delete is associated with other records';
			$this->Flash->error(__('You can not delete this record because it is used in another table.'));
			$this->set('error', $error);
			//$this->Session->setFlash(__(' Lader all ready exists), 'flash/Error');
			return $this->redirect(['action' => 'index']);
		}
	}

	//status update functionality
	public function status($id, $status)
	{

		$statusquery = $this->Locations->find('all')->where(['Locations.status' => 'Y'])->count();
		if (isset($id) && !empty($id)) {
			if ($status == 'Y') {

				$status = 'N';
				//status update
				$classes = $this->Locations->get($id);
				$classes->status = $status;
				if ($this->Locations->save($classes)) {
					$this->Flash->success(__(' Locations status has been updated.'));
					return $this->redirect(['action' => 'index']);
				}
			} else {
				if ($statusquery < 8) {
					$status = 1;
					//status update
					$classes = $this->Locations->get($id);
					$classes->status = $status;
					if ($this->Locations->save($classes)) {
						$this->Flash->success(__('Locations status has been updated.'));
						return $this->redirect(['action' => 'index']);
					}
				} else {
					$this->Flash->error(__('8 Entries all ready activate. Please deactivate one of activate'));
					return $this->redirect(['action' => 'index']);
				}
			}
		}
	}

	public function routemaster($id=null)
	{
		$this->viewBuilder()->layout('admin');
		$locations = $this->Locations->find('list', ['keyField' => 'id', 'valueField' => 'name'])->where(['Locations.status' => 'Y'])->order(['name' => 'ASC'])->toArray('Locations');
		$route = $this->Routemaster->find('all')->where(['status' =>'Y'])->order(['id'=>'DESC'])->toArray();
		// pr($route);die;
		$this->set(compact('locations','route'));

		if (isset($id) && !empty($id)) {
			//using for edit
			$routemaster = $this->Routemaster->get($id);
			$optns = $routemaster->location_id;
			// pr($optns);die;
			$this->set('optns', $optns);
			// pr($routemaster);die;
		} else {
			//using for new entry
			$routemaster = $this->Routemaster->newEntity();
		}
		
		if ($this->request->is(['post', 'put'])) {
			// pr($this->request->data);
			$location = implode(",", $this->request->data['location_id']);
			// pr($location);die;
			$this->request->data['location_id'] = $location;
			$locations = $this->Routemaster->patchEntity($routemaster, $this->request->data);
			//pr($locations); die;
			if ($this->Routemaster->save($locations)) {
				$this->Flash->success(__('Route has been saved.'));
				return $this->redirect(['action' => 'routemaster']);
			} else { 
				$this->Flash->error(__('Something Wrong.'));
				return $this->redirect(['action' => 'routemaster']);
			}
		}
		$this->set(compact('routemaster'));
	}
}
