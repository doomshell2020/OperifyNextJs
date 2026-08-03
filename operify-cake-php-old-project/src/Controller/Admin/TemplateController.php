<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use TCPDF;
include '../vendor/tecnickcom/tcpdf/tcpdf.php';
class TemplateController extends AppController
{
	// public function beforeFilter(Event $event)
	// {
	// 	$this->checkpermission_user();
	// }

	public function index()
	{
		$this->viewBuilder()->layout('admin');
		$this->loadModel('Emailtemplate');
		$emailtemplate = $this->Emailtemplate->find('all', [
			'conditions' => ['Emailtemplate.status' => 'Y']
		])->toArray();
		
		$this->set('emailtemplate', $emailtemplate);
	}

	public function status($id, $status)
	{

		$this->loadModel('Emailtemplate');
		if (isset($id) && !empty($id)) {
			$product = $this->Emailtemplate->get($id);
			$product->status = $status;
			if ($this->Emailtemplate->save($product)) {
				$this->Flash->success(__('Template status has been updated.'));
				return $this->redirect(['action' => 'index']);
			}
		}
	}


	public function add()
	{
		$this->viewBuilder()->layout('admin');
		$this->loadModel('Emailtemplate');
		
		if ($this->request->is(['post', 'put'])) {
			// pr($this->request->data);die;
			$this->request->data['type_name'] = implode(",", $this->request->data['type_name']);
			$this->request->data['type_name'] = $this->request->data['type_name'];
			$savepack = $this->Emailtemplate->patchEntity($this->Emailtemplate->newEntity(), $this->request->data);
			$results = $this->Emailtemplate->save($savepack);
			if ($results) {
				$this->Flash->success(__('Template has been added.'));
				return $this->redirect(['action' => 'index']);
			} else {
				$this->Flash->error(__('Template not added'));
				return $this->redirect(['action' => 'index']);
			}
		}
	}


	public function edit($id)
{
	$this->viewBuilder()->layout('admin');
    $this->loadModel('Emailtemplate');  
    $newpack = $this->Emailtemplate->get($id);
    $this->set('newpack', $newpack);

    if ($this->request->is(['post', 'put'])) {
        // $newpack->status = 'N'; 
        // $this->Emailtemplate->save($newpack);

        $this->request->data['type_name'] = implode(",", $this->request->data['type_name']);
		$this->request->data['status']  = 'Y';
        $savepack = $this->Emailtemplate->patchEntity($newpack, $this->request->data);

        if ($this->Emailtemplate->save($savepack)) {
            $this->Flash->success(__('New template has been saved.'));
            return $this->redirect(['action' => 'index']);
        } else {
            $this->Flash->error(__('New template not saved.'));
        }
    }
}


	public function viewtemplate($id)
	{
		$this->loadModel('Emailtemplate');
		$popupdata = $this->Emailtemplate->find('all')->where(['Emailtemplate.id' => $id])->order(['Emailtemplate.id' => DESC])->first();
		// pr($popupdata);die;
		$this->set('popupdata', $popupdata);
	}

	public function delete($id = null)
	{
		$this->loadModel('Emailtemplate');
		if (!empty($id)) {
			$entity = $this->Emailtemplate->get($id);
			try {
				$this->Emailtemplate->delete($entity);
				$this->Flash->Success('Template deleted Successfully');
			} catch (\Exception $e) {
				$this->Flash->error('You cannot delete this recourd because its used in another module');
			}
		}
		return $this->redirect(['action' => 'index']);
	}

	// public function generatePdf($templateId) {
	// 	$this->loadModel('Emailtemplate');
	// 	$emailTemplate = $this->Emailtemplate->get($templateId);
	// 	$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	
		
	// 	$html = $this->prepareHtml($emailTemplate);
	
	// 	$pdf->writeHTMLCell(0, 0, '', '', utf8_encode($html), 0, 1, 0, true, '', true);
	// 	$pdf->Output('Indent_Details.pdf', 'D');
	// }
	
	// private function prepareHtml($template) {
	// 	$this->loadModel('Emailtemplate');
	// 	return $html;
	// }
	
    public function viewpdf($id = null)
    {
		$this->loadModel('Emailtemplate');
        // $this->viewBuilder()->layout('ajax');
        $site_details = $this->Emailtemplate->find('all')->where(['status' => 'Y','id' => $id])->first();
		$sitesetting = $site_details['body'];
		// pr($sitesetting);die;
	
        $this->set(compact(['sitesetting', 'sitesetting']));
        $this->response->type('pdf');
    }
}
