<?php

namespace App\Controller;

use Cake\Core\Configure;

use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\View\Helper;
use PHPMailer\PHPMailer\PHPMailer;

include(ROOT . DS . "vendor" . DS  . "PHPMailer/" . DS . "PHPMailerAutoload.php");

class HomesController extends AppController
{



	public function beforeFilter(Event $event)
	{
		$this->loadModel('Contacts');
		parent::beforeFilter($event);
		$this->loadComponent('Cookie');
		$this->loadComponent('Email');
		$this->Auth->allow(['index', 'privacy', 'about', 'pricing', 'product', 'faq', 'contactus', 'refundpolicy', 'terms', 'contact']);
	}


	public function index()
	{
		$this->viewBuilder()->layout('front');
	}
	public function about()
	{
		$this->viewBuilder()->layout('front');
	}

	// public function contactus()
	// {
	// 	$this->viewBuilder()->layout('front');
	// }

	public function faq()
	{
		$this->viewBuilder()->layout('front');
	}

	public function pricing()
	{
		$this->viewBuilder()->layout('front');
	}

	public function privacy()
	{
		$this->viewBuilder()->layout('front');
	}
	public function product()
	{
		$this->viewBuilder()->layout('front');
	}

	public function refundpolicy()
	{
		$this->viewBuilder()->layout('front');
	}

	public function terms()
	{
		$this->viewBuilder()->layout('front');
	}


	// public function contact()
	// {
	// 	$this->viewBuilder()->layout('front');
	// 	pr($this->request->data); die;
	// 	return $this->redirect(['controller' => 'homes', 'action' => 'index']);
	// }

	public function contactus()
	{
		$this->loadModel('IpRanges');
		$this->loadModel('Demorequest');
		$this->loadModel('Emailtemplate');

		$this->viewBuilder()->layout('front');
		if ($this->request->is('post')) {

			if (!empty($this->request->data['g-recaptcha-response'])) {
				$secret = '6LdSrYQqAAAAAIjRXfojSrLQt7Z7RPr6m1MLeVSl';
				$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $this->request->data['g-recaptcha-response']);
				$responseData = json_decode($verifyResponse);
				if (!empty($responseData->success)) {
					$newpack = $this->Demorequest->newEntity();
					if ($this->request->is(['post', 'put'])) {


						// Validate Email
						if (!filter_var($this->request->data['email'], FILTER_VALIDATE_EMAIL)) {
							$this->Flash->error(__('Please enter a valid email address.'));
							return $this->redirect($this->referer());
						}

						if (!preg_match("/^\+?[0-9-]{10,15}$/", $this->request->data['phone'])) {
							$this->Flash->error(__('Please enter a valid phone number.'));
							return $this->redirect($this->referer());
						}


						$ipRanges = $this->IpRanges->find('all')->toArray();

						$ip = $this->get_client_ip_env();
						$ipLong = ip2long($ip);
						// Check if the IP exists within the range
						foreach ($ipRanges as $ipRange) {
							$rangeStart = ip2long($ipRange->start_ip);
							$rangeEnd = ip2long($ipRange->end_ip);

							// Check if the client's IP falls within this range
							if ($ipLong >= $rangeStart && $ipLong <= $rangeEnd) {

								$this->Flash->error(__('Too many requests from this IP range. Your IP has been blocked.'));
								return $this->redirect(['controller' => 'homes', 'action' => 'index']);
							}
						}


						function sanitizeInput($input)
						{
							// Step 1: Remove URLs
							// $input = preg_replace('/https?:\/\/\S+/', '', $input);
							$input = preg_replace('/[^A-Za-z0-9 ]/', '', $input);
							$input = preg_replace('/https?:\/\/\S+/', '', $input);

							// Step 2: Remove HTML tags
							$input = strip_tags($input);
							// Step 3: Trim whitespace from the input
							return trim($input);
						}
						// Loop through each form field and sanitize
						$requestData = [];
						foreach ($this->request->data as $key => $value) {
							$requestData[$key] = sanitizeInput($value);
						}



						$this->request->data['name'] = htmlspecialchars($requestData['name']);
						$this->request->data['company_name'] = htmlspecialchars($requestData['company_name']);
						$this->request->data['title'] = htmlspecialchars($requestData['title']);
						$this->request->data['message'] = htmlspecialchars($requestData['message']);
						$this->request->data['ip'] = $ip;
						$savepack = $this->Demorequest->patchEntity($newpack, $this->request->data);
						$result = $this->Demorequest->save($savepack);


						$name = $result['name'];
						$school = $result['company_name'];
						$title = $result['title'];
						$email = $result['email'];
						$phone = $result['phone'];
						$day = $result['day'];
						$time = $result['time'];
						$message = $result['message'];
						$year = date('Y');

						$email_temp = $this->Emailtemplate->find('all')->where(['Emailtemplate.id' => 9])->first();
						$to = "vikas@doomshell.com";
						$from = $email;
						$subject = $email_temp['type_name'];
						$formats = $email_temp['body'];
						$year = date('Y');
						$message1 = str_replace(array('{names}', '{name}', '{school}', '{title}', '{email}', '{phone}', '{day}', '{time}', '{comments}', '{year}'), array($name, $name, $school, $title, $email, $phone, $day, $time, $message, $year), $formats);

						$message = stripslashes($message1);

						$headers = "MIME-Version: 1.0\r\n";
						$headers .= "Content-type:text/html; charset=iso-8859-1\r\n";
						$headers .= 'From: Operify Customer Service<' . $from . '>' . "\r\n";
						$cc = "contact@doomshell.com";

						$this->Email->send($to, $subject, $message, $cc);


						$this->Flash->success(__('Thank you for getting in touch! We appreciate you contacting us'));
						return $this->redirect(['controller' => 'homes', 'action' => 'index']);
					}
				} else {
					$this->Flash->error(__('Please provide us a valid captcha'));
					return $this->redirect(['controller' => 'homes', 'action' => 'index']);
				}
			} else {
				$this->Flash->error(__('Please provide us a valid captcha'));
				return $this->redirect(['controller' => 'homes', 'action' => 'index']);
			}
			// $this->redirect($this->referer());
		}
	}
	// this code for check IP Address
	public function get_client_ip_env()
	{
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP')) {
			$ipaddress = getenv('HTTP_CLIENT_IP');
		} else if (getenv('HTTP_X_FORWARDED_FOR')) {
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		} else if (getenv('HTTP_X_FORWARDED')) {
			$ipaddress = getenv('HTTP_X_FORWARDED');
		} else if (getenv('HTTP_FORWARDED_FOR')) {
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		} else if (getenv('HTTP_FORWARDED')) {
			$ipaddress = getenv('HTTP_FORWARDED');
		} else if (getenv('REMOTE_ADDR')) {
			$ipaddress = getenv('REMOTE_ADDR');
		} else {
			$ipaddress = 'UNKNOWN';
		}
		return $ipaddress;
	}
}
