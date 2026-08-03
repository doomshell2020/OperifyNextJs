<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Datasource\ConnectionManager;

class SitesettingsController extends AppController
{

    public function initialize()
    {
        parent::initialize();

        //load all models
        $this->loadModel('Boards');
        $this->loadModel('Users');
        $this->loadModel('SitesettingsDetails');
        $this->loadModel('TemplateCategory');
        $this->loadModel('Template');
    }
    // public function add($id = 1)
    // {
    //     $this->viewBuilder()->layout('admin');
    //     $this->loadModel('Users');
    //     $this->loadModel('SitesettingsDetails');

    //     $user = $this->Users->get($this->Auth->user('id'));
    //     $sitesetting = $this->Sitesettings->find('all')
    //         ->contain(['SitesettingsDetails'])
    //         ->where(['Sitesettings.id' => $id])
    //         ->first();

    //     $this->set('sitesetting', $sitesetting);

    //     if ($this->request->is(['post', 'put'])) {
    //         $conn = ConnectionManager::get('default');

    //         if ($this->request->data['new_password'] && $this->request->data['confirm_pass']) {
    //             if ($this->request->data['new_password'] == $this->request->data['confirm_pass']) {
    //                 $new_pass['password'] = (new DefaultPasswordHasher)->hash($this->request->data['new_password']);
    //                 $user = $this->Users->patchEntity($user, $new_pass);
    //                 $this->Users->save($user);
    //                 $this->Flash->success(__('Your new password has been changed successfully!'));
    //             } else {
    //                 $this->Flash->error(__('Your new password and confirm password do not match, try again.'));
    //                 return $this->redirect(['action' => 'add']);
    //             }
    //         }

    //         $sitesetting = $this->Sitesettings->patchEntity($sitesetting, $this->request->data);

    //         if ($this->Sitesettings->save($sitesetting)) {
    //             // small logo
    //             $filename = $this->request->data['small_logo']['name'];
    //             $item = $this->request->data['small_logo']['tmp_name'];
    //             $ext = end(explode('.', $filename));
    //             $name = md5(time($filename));
    //             $imagename = $name . '.' . $ext;
    //             $dest = 'images/';
    //             $newfile = $dest . $imagename;
    //             // pr($newfile);exit;

    //             if (move_uploaded_file($item, $newfile)) {

    //                 $this->request->data['small_logo'] = $imagename;
    //             }

    //             $filename = $this->request->data['header_logo']['name'];
    //             $item = $this->request->data['header_logo']['tmp_name'];
    //             $ext = end(explode('.', $filename));
    //             $name = md5(time($filename));
    //             $imagename = $name . '.' . $ext;
    //             $dest = 'images/';
    //             $newfile = $dest . $imagename;
    //             // pr($newfile);exit;

    //             if (move_uploaded_file($item, $newfile)) {

    //                 $this->request->data['header_logo'] = $imagename;
    //             }


    //             $data = [
    //                 'sitesettings_id' => $sitesetting->id,
    //                 'address1' => $this->request->data['address1'],
    //                 'address2' => $this->request->data['address2'],
    //                 'phone' => $this->request->data['phone'],
    //                 'fax' => $this->request->data['fax'],
    //                 'email' => $this->request->data['email'],
    //                 'website' => $this->request->data['website'],
    //                 'subtitle1' => $this->request->data['subtitle1'],
    //                 'subtitle2' => $this->request->data['subtitle2'],
    //                 'affiliation_no' => $this->request->data['affiliation_no'],
    //                 'school_code' => $this->request->data['school_code'],
    //                 'company_name' => $this->request->data['cname'],
    //                 'pan_number' => $this->request->data['pan_no'],
    //                 'account_number' => $this->request->data['account_number'],
    //                 'ifsc' => $this->request->data['ifsc'],
    //                 'tin_date' => date('Y-m-d', strtotime($this->request->data['tin_date'])),
    //                 'gst_no' => $this->request->data['gst_no'],
    //                 'address' => $this->request->data['address'],
    //                 'cmobile_no' => $this->request->data['cmobile_no'],
    //             ];

    //             $savedetails = $this->SitesettingsDetails->newEntity($data);

    //             if ($this->SitesettingsDetails->save($savedetails)) {
    //                 $this->Flash->success(__('Your site setting has been updated.'));
    //                 return $this->redirect(['action' => 'add/' . $sitesetting->id]);
    //             }
    //         }
    //     }
    // }

    public function index()
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Roles');
        $this->loadModel('Users');
        // $roles = $this->Roles->find('list')->order(['name' => 'ASC'])->toarray();
        // $this->set('roles', $roles);
        // pr($roles);die;
        $allusers = $this->Users->find('all')->toarray();
        $this->set('allusers', $allusers);

        $ems = $this->Users->find('all')->first();
        $academic_year = $ems['academic_year'];
        $this->set('board', $board);
        $this->set('roles', $roles);

    }


    public function edit($db = null)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Users');
        $connss = ConnectionManager::get('default');
        $query4 = "SELECT * FROM $db.sitesettings s
             JOIN $db.sitesettings_details sd ON sd.sitesettings_id = s.id ";

        //    pr($this->request->data);die;

        $user = $connss->execute($query4)->fetch('assoc');


        $this->set('sitesetting', $user);

        if ($this->request->is(['post', 'put'])) {
            $id = $this->request->data['id'];
            $firstName = $this->request->data['first_name'];
            $lastName = $this->request->data['last_name'];
            $mobile = $this->request->data['mobile'];
            $contactEmail = $this->request->data['contact_email'];
            $facebookUrl = $this->request->data['facebook_url'];
            $twitterUrl = $this->request->data['twitter_url'];
            $siteTitle = $this->request->data['site_title'];
            $siteKeywords = $this->request->data['site_keywords'];
            $siteDescription = $this->request->data['site_description'];
            $googleAnalytics = $this->request->data['google_analytics'];
            $address1 = $this->request->data['address1'];
            $address2 = $this->request->data['address2'];
            $phone = $this->request->data['phone'];
            $fax = $this->request->data['fax'];
            $email = $this->request->data['email'];
            $website = $this->request->data['website'];
            $subtitle1 = $this->request->data['subtitle1'];
            $subtitle2 = $this->request->data['subtitle2'];
            $cname = $this->request->data['cname'];
            $panNo = $this->request->data['pan_no'];
            $gstNo = $this->request->data['gst_no'];
            $tinDate = $this->request->data['tin_date'];
            $accountNumber = $this->request->data['account_number'];
            $ifsc = $this->request->data['ifsc'];
            $address = $this->request->data['address'];
            $cmobileNo = $this->request->data['cmobile_no'];
            $alias = $this->request->data['alias'];

            $small_logo = $sitesetting['small_logo'];
            if (!empty($_FILES['small_logo']['name'])) {
                $filename = $_FILES['small_logo']['name'];
                $item = $_FILES['small_logo']['tmp_name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                $imagename = md5(microtime()) . '.' . $ext;
                $dest = 'images/' . $imagename;
                if (move_uploaded_file($item, $dest)) {
                    $small_logo = $imagename;
                } else {
                    echo "Failed to move small logo image.";
                    exit;
                }
            }

            $header_logo = $sitesetting['header_logo'];
            if (!empty($_FILES['header_logo']['name'])) {
                $filename = $_FILES['header_logo']['name'];
                $item = $_FILES['header_logo']['tmp_name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                $imagename = md5(microtime()) . '.' . $ext;
                $dest = 'images/' . $imagename;
                if (move_uploaded_file($item, $dest)) {
                    $header_logo = $imagename;
                } else {

                    echo "Failed to move header logo image.";
                    exit;
                }
            }

            $query = "UPDATE " . $db . ".sitesettings SET 
                `first_name` = '$firstName', 
                `last_name` = '$last_name', 
                `mobile` = '$mobile', 
                `contact_email` = '$contactEmail', 
                `facebook_url` = '$facebookUrl', 
                `twitter_url` = '$twitterUrl', 
                `site_title` = '$siteTitle', 
                `site_keywords` = '$siteKeywords', 
                `fine_rate` = '$fineRate', 
                `renew_days` = '$renewDays', 
                `site_description` = '$siteDescription', 
                `google_analytics` = '$googleAnalytics'
             
                WHERE `id` = '$id'";
            $results1 = $connss->execute($query);


            $query2 = "UPDATE " . $db . ".sitesettings_details SET 
               
                 `pan_number` = '$panNo', 
                 `header_logo` = '$header_logo', 
                  `small_logo` = '$small_logo', 
                 `address1` = '$address1', 
                 `address2` = '$address2', 
                 `phone` = '$phone', 
                 `fax` = '$fax', 
                 `email` = '$email', 
                 `website` = '$website', 
                 `subtitle1` = '$subtitle1', 
                 `subtitle2` = '$subtitle2', 
                 `gst_no` = '$gstNo', 
                 `tin_date` = '$tinDate' ,
                `account_number` = '$accountNumber', 
                 `ifsc` = '$ifsc', 
                 `ac_holder` = '$cname',
                  `alias` = '$alias',
                  `company_name` = '$cname'

                  WHERE `sitesettings_id` = '$id' and `id` = '1'";

            $results2 = $connss->execute($query2);
        }


    }




    public function add($id = 1)
    {
        $this->viewBuilder()->layout('admin');
        $this->loadModel('Users');
        $this->loadModel('SitesettingsDetails');
        // pr($this->request->data);die;

        $user = $this->Users->get($this->Auth->user('id'));
        $sitesetting = $this->Sitesettings->find('all')->contain(['SitesettingsDetails'])->where(['Sitesettings.id' => $id])->first();

        $small_logo = $sitesetting['sitesettings_detail']['small_logo'];
        $header_logo = $sitesetting['sitesettings_detail']['header_logo'];
        // pr($small_logo);die;
        $this->set('sitesetting', $sitesetting);
        $this->set('user', $user);



        if ($this->request->is(['post', 'put'])) {
            // pr($this->request->data);die;
            // pr($this->request->data);die;

            // $conn = ConnectionManager::get('default');

            // if ($this->request->data['new_password'] && $this->request->data['confirm_pass']) {
            //     if ($this->request->data['new_password'] == $this->request->data['confirm_pass']) {
            //         $new_pass['password'] = (new DefaultPasswordHasher)->hash($this->request->data['new_password']);
            //         $user = $this->Users->patchEntity($user, $new_pass);
            //         $this->Users->save($user);
            //         $this->Flash->success(__('Your new password has been changed successfully!'));
            //     } else {
            //         $this->Flash->error(__('Your new password and confirm password do not match, try again.'));
            //         return $this->redirect(['action' => 'add']);
            //     }
            // }



            $data1 = [
                'id' => 1,
                'first_name' => $this->request->data['first_name'],
                'last_name' => $this->request->data['last_name'],
                'address1' => $this->request->data['address1'],
                'address2' => $this->request->data['address2'],
                'mobile' => $this->request->data['phone'],
                'fax' => $this->request->data['fax'],
                'email' => $this->request->data['email']

            ];
            if (empty($sitesetting)) {
                $sitesetting = $this->Sitesettings->newEntity($data1);

            }

            // $sitesetting = $this->Sitesettings->patchEntity($sitesetting, $this->request->data);
            $sitesetting = $this->Sitesettings->patchEntity($sitesetting, $data1);

            // pr($sitesetting);die;
            $res = $this->Sitesettings->save($sitesetting);

            if ($this->Sitesettings->save($sitesetting)) {
                // small logo
                if (!empty($this->request->data['small_logo']['name'])) {
                    $filename = $this->request->data['small_logo']['name'];
                    $item = $this->request->data['small_logo']['tmp_name'];
                    $ext = end(explode('.', $filename));
                    $name = md5(microtime($filename));
                    $imagename = $name . '.' . $ext;
                    $dest = 'images/';
                    $newfile = $dest . $imagename;

                    if (move_uploaded_file($item, $newfile)) {
                        $this->request->data['small_logo'] = $imagename;
                    } else {
                        $this->Flash->error(__('Failed to move small logo image.'));
                        return $this->redirect(['action' => 'add']);
                    }
                } else {
                    $this->request->data['small_logo'] = $small_logo;
                }


                // header logo
                if (!empty($this->request->data['header_logo']['name'])) {
                    $filename = $this->request->data['header_logo']['name'];
                    $item = $this->request->data['header_logo']['tmp_name'];
                    $ext = end(explode('.', $filename));
                    $name = md5(microtime($filename));
                    $imagename = $name . '.' . $ext;
                    $dest = 'images/';
                    $newfile = $dest . $imagename;

                    if (move_uploaded_file($item, $newfile)) {
                        $this->request->data['header_logo'] = $imagename;
                    } else {
                        $this->Flash->error(__('Failed to move header logo image.'));
                        return $this->redirect(['action' => 'add']);
                    }
                } else {
                    $this->request->data['header_logo'] = $header_logo;
                }

                $data = [
                    'sitesettings_id' => $sitesetting->id,
                    'address1' => $this->request->data['address1'],
                    'address2' => $this->request->data['address2'],
                    'phone' => $this->request->data['phone'],
                    'fax' => $this->request->data['fax'],
                    'email' => $this->request->data['email'],
                    'website' => $this->request->data['website'],
                    'subtitle1' => $this->request->data['subtitle1'],
                    'subtitle2' => $this->request->data['subtitle2'],
                    'affiliation_no' => $this->request->data['affiliation_no'],
                    'school_code' => $this->request->data['school_code'],
                    'company_name' => $this->request->data['cname'],
                    'ac_holder' => $this->request->data['cname'],
                    'pan_number' => $this->request->data['pan_no'],
                    'account_number' => $this->request->data['account_number'],
                    'ifsc' => $this->request->data['ifsc'],
                    'tin_date' => date('Y-m-d', strtotime($this->request->data['tin_date'])),
                    'gst_no' => $this->request->data['gst_no'],
                    'address' => $this->request->data['address'],
                    'cmobile_no' => $this->request->data['cmobile_no'],
                    'header_logo' => $this->request->data['header_logo'],
                    'alias' => $this->request->data['alias'],
                    'small_logo' => $this->request->data['small_logo'],
                    'stock_update' => $this->request->data['stock_update'],
                ];
                
                $savedetails = $this->SitesettingsDetails->find('all')->where(['sitesettings_id' => $sitesetting->id])->first();

                if (empty($savedetails)) {
                    $savedetails = $this->SitesettingsDetails->newEntity($data);

                }
                $savedetails = $this->SitesettingsDetails->patchEntity($savedetails, $data);

                if ($this->SitesettingsDetails->save($savedetails)) {
                    $this->Flash->success(__('Your site setting has been updated.'));
                    return $this->redirect(['action' => 'add/' . $sitesetting->id]);
                } else {
                    $this->Flash->error(__('Failed to save site details.'));
                    return $this->redirect(['action' => 'add/' . $sitesetting['id']]);
                }
            }
        }
    }

}

