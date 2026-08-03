<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;


class DemorequestController extends AppController
{


    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Demorequest');

        $demo_req_datas = $this->Demorequest->find('all')->order(['id' => 'DESC']);
        $demoreqData = $this->paginate($demo_req_datas)->toarray();

        $this->set('demoreqData', $demoreqData);
    }


    // delete additem data from index page
    public function delete($id = null)
    {
        $this->loadModel('Demorequest');
        $res = $this->Demorequest->get($id);
        if ($this->Demorequest->delete($res)) {

            $this->Flash->success('Demo request record deleted successfully');
            return $this->redirect(['controller' => 'Demorequest', 'action' => 'index']);
        } else {
            $this->Flash->error('Demo request record not  delete successfully');
            return $this->redirect(['controller' => 'Demorequest', 'action' => 'index']);
        }
    }

    public function userblock($id)
    {
        $this->autoRender = false;
        $this->loadModel('DemoRequest');
        $this->loadModel('IpRanges');

        $help_data = $this->DemoRequest->get($id);

        $ipAddress = $help_data->ip;

        $ipParts = explode('.', $ipAddress);
        if (count($ipParts) === 4) {
            $startIp = "{$ipParts[0]}.{$ipParts[1]}.{$ipParts[2]}.0";
            $endIp = "{$ipParts[0]}.{$ipParts[1]}.{$ipParts[2]}.255";


            $existingRange = $this->IpRanges->find()
                ->where(['start_ip' => $startIp, 'end_ip' => $endIp])
                ->first();

            if (!$existingRange) {

                // Create a new entity with start_ip and end_ip
                $blockIpEntity = $this->IpRanges->newEntity([

                    'start_ip' => $startIp,
                    'end_ip' => $endIp
                ]);

                // Save the entity
                if ($this->IpRanges->save($blockIpEntity)) {

                    $this->DemoRequest->deleteAll(['DemoRequest.ip' => $ipAddress]);

                    $this->Flash->success('IP range saved successfully.');
                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error('Failed to save IP range.');
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $this->DemoRequest->deleteAll(['DemoRequest.ip' => $ipAddress]);
                return $this->redirect(['action' => 'index']);
            }
        } else {
            $this->Flash->error('Invalid IP address format.');
            return $this->redirect(['action' => 'index']);
        }
    }
}
