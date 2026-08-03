<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

class SliderController extends AppController
{
    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Slider');
        $slider_data = $this->Slider->find('all')->toarray();
        $this->set('slider_data', $slider_data);

        if ($this->request->is('post')) {
            $create_data = $this->Slider->newEntity();
            $tmp_name = $this->request->data['image']['tmp_name'];
            $image_name = $this->request->data['image']['name'];
            $pext = pathinfo($image_name, PATHINFO_EXTENSION);
            $imagenewname = md5(time($filename)) . '.' . $pext;
            //pr($imagenewname); die;
            $dest = "sliderimages/";
            $newfile = $dest . $imagenewname;
            //pr($tmp_name);
            //pr($newfile); die;
            if (move_uploaded_file($tmp_name, $newfile)) {
                //pr($newfile); die;
                $this->request->data['image'] = $imagenewname;
            }
            $entity = $this->Slider->patchEntity($create_data, $this->request->data);

            if ($this->Slider->save($entity)) {
                $this->Flash->success(__('Slider added successfully'));

                return $this->redirect(['controller' => 'Slider', 'action' => 'index']);
            } else {
                $this->Flash->error(__('Slider not added'));

                return $this->redirect(['controller' => 'Slider', 'action' => 'index']);
            }

        }

    }
    public function status($id, $status)
    {
        $this->autoRender = false;
        $this->loadModel('Slider');

        $det = $this->Slider->get($id);
        //pr($det); die;
        $this->request->data['status'] = $status;

        $entity = $this->Slider->patchEntity($det, $this->request->data);
        if ($this->Slider->save($entity)) {
            $this->Flash->success(__('Sldier edited successfully'));

            return $this->redirect(['controller' => 'Slider', 'action' => 'index']);
        } else {
            $this->Flash->error(__('Slider not edited'));

            return $this->redirect(['controller' => 'Slider', 'action' => 'index']);
        }

    }
    public function edit($id)
    {
        $this->viewBuilder()->layout('admin');

        $this->loadmodel('Slider');
        $slider_det = $this->Slider->get($id);
        //pr($album_det); die;
        $this->set('slider_det', $slider_det);
        //pr($this->request->data); die;
        if ($this->request->is(['post', 'put'])) {
            //pr($this->request->data); die;
            $det = $this->Slider->get($id);
            //pr($det); die;

            if (isset($this->request->data['image']['name']) && !empty($this->request->data['image']['name'])) {

                $tmp_name = $this->request->data['image']['tmp_name'];
                $image_name = $this->request->data['image']['name'];
                $pext = pathinfo($image_name, PATHINFO_EXTENSION);
                $imagenewname = md5(time($filename)) . '.' . $pext;
                //pr($imagenewname); die;
                $dest = "sliderimages/";
                $newfile = $dest . $imagenewname;
                //pr($tmp_name);
                //pr($newfile); die;
                if (move_uploaded_file($tmp_name, $newfile)) {
                    unlink("sliderimages/" . $det['image']);
                    //pr($newfile); die;
                    $this->request->data['image'] = $imagenewname;
                }} else {
                $this->request->data['image'] = $slider_det['image'];
            }
            $entity = $this->Slider->patchEntity($det, $this->request->data);
            if ($this->Slider->save($entity)) {
                $this->Flash->success(__('Slider edited successfully'));

                return $this->redirect(['controller' => 'Slider', 'action' => 'index']);
            } else {
                $this->Flash->error(__('Slider not added'));

                return $this->redirect(['controller' => 'Slider', 'action' => 'index']);
            }
        }

    }

    public function delete($id)
    {
        $this->loadModel('Slider');
        $this->autoRender = false;
        $res = $this->Slider->get($id);
        //pr($res); die;
        unlink("sliderimages/" . $res['image']);
        if ($this->Slider->delete($res)) {

            $this->Flash->success('Slider deleted successfully');
            return $this->redirect(['controller' => 'Slider', 'action' => 'index']);
        } else {
            $this->Flash->error('Slider not  delete successfully');
            return $this->redirect(['controller' => 'Slider', 'action' => 'index']);
        }
    }

}