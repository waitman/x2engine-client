<?php

/*
Copyright (c) 2016 Waitman Gobble <ns@waitman.net>.
All rights reserved.

Redistribution and use in source and binary forms are permitted
provided that the above copyright notice and this paragraph are
duplicated in all such forms and that any documentation,
advertising materials, and other materials related to such
distribution and use acknowledge that the software was developed
by Waitman Gobble. The name of Waitman Gobble may not be used to 
endorse or promote products derived from this software without 
specific prior written permission. THIS SOFTWARE IS PROVIDED ``AS IS'' 
AND WITHOUT ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, WITHOUT 
LIMITATION, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS 
FOR A PARTICULAR PURPOSE.
*/

class x2engineClient {

    private $auth;
    public $page;
    public $reponse;
    public $form_url;

    public function __construct($username=null,$apikey=null,$form_url=null)
    {
                if (!$username||!$apikey||!$form_url)
                {
                        $this->response = 'Error loading, not authorized.';
                } else {
                        $this->auth = base64_encode($username.':'.$apikey);
                        $this->form_url = $form_url;
                        $this->page = '/appInfo.json';
                        $this->qp();
                }

    }

    private function qp($method='GET',$postarray=null)
    {
                if (!$this->auth||!$this->form_url)
                {
                        $this->response = 'Error, not authorized.';
                } else {

				/* at the moment no verify_peer or cn check, todo - needs a ca */

					switch ($method)
					{

						case 'PUT':
						
							if (is_array($postarray)) $postdata = json_encode($postarray);
						
							$opts = array(
                                'http' => array(
                                'method'  => 'PUT',
                                'header'  =>  [
                                        'Content-type: application/json',
                                        'Content-length: '.strlen($postdata),
                                        'Authorization: Basic '.$this->auth
                                ],
                                'content' => $postdata
                                ),
                                'ssl' => array(
                                'verify_peer'   => false,
                                'ciphers' => 'HIGH:!SSLv2:!SSLv3'
                                )

							);
							$ctx  = stream_context_create($opts);
						break;

						case 'POST':
						
							if (is_array($postarray)) $postdata = json_encode($postarray);
						
							$opts = array(
                                'http' => array(
                                'method'  => 'POST',
                                'header'  =>  [
                                        'Content-type: application/json',
                                        'Content-length: '.strlen($postdata),
                                        'Authorization: Basic '.$this->auth
                                ],
                                'content' => $postdata
                                ),
                                'ssl' => array(
                                'verify_peer'   => false,
                                'ciphers' => 'HIGH:!SSLv2:!SSLv3'
                                )

							);
							$ctx  = stream_context_create($opts);
                        break;

						case 'GET':
						default:
							$postdata = '';
							$opts = array(
                                'http' => array(
                                'method'  => 'GET',
                                'header'  =>  [
                                        'Content-type: application/json',
                                        'Content-length: '.strlen($postdata),
                                        'Authorization: Basic '.$this->auth
                                ],
                                'content' => $postdata
                                ),
                                'ssl' => array(
                                'verify_peer'   => false,
                                'ciphers' => 'HIGH:!SSLv2:!SSLv3'
                                )

							);
							$ctx  = stream_context_create($opts);
						break;
						
					}
                        
					$form = file_get_contents($this->form_url.$this->page, false, $ctx);
					$this->response = $form;

                }

        }

        public function __toString()
        {
                return $this->response;
        }
        
        public function toArray()
        {
			return json_decode($this->response,true);
		}
		
		public function contactsCount()
		{
			$this->page = '/Contacts/count';
			$this->qp('GET');
			return $this->response;
		}
		
		public function contactRecord($id=0)
		{
			if ($id>0)
			{
				$this->page = '/Contacts/'.$id.'.json';
				$this->qp('GET');
				return $this->response;
			} else {
				return 'Error - Contact Id Not Specified in Request.';
			}
		}

		public function accountRecord($id=0)
		{
			if ($id>0)
			{
				$this->page = '/Accounts/'.$id.'.json';
				$this->qp('GET');
				return $this->response;
			} else {
				return 'Error - Account Id Not Specified in Request.';
			}
		}

		public function createContact($a)
		{
			if (is_array($a))
			{
				$this->page = '/Contacts';
				$this->qp('POST',$a);
				return $this->response;
			} else {
				return 'Error - No Contact Array sent.';
			}
		}

		public function updateContact($id=0,$a)
		{
			if (is_array($a))
			{
				if ($id>0)
				{
					$this->page = '/Contacts/'.$id.'.json';
					$this->qp('PUT',$a);
					return $this->response;
				} else {
					return 'Error - No Contact Id sent.';
				}
			} else {
				return 'Error - No Contact Array sent.';
			}
		}

		public function searchContactEmail($email=null)
		{
			if ($email!=null)
			{
				$this->page = '/Contacts/by:email='.$email.'.json';
				$this->qp('GET');
				return $this->response;
			} else {
				return 'Error - no Email specified.';
			}
		}
		
		public function createAccount($a)
		{
			if (is_array($a))
			{
				$this->page = '/Accounts';
				$this->qp('POST',$a);
				return $this->response;
			} else {
				return 'Error - No Account Array sent.';
			}
		}
		
		public function updateAccount($id=0,$a)
		{
			if (is_array($a))
			{
				if ($id>0)
				{
					$this->page = '/Accounts/'.$id.'.json';
					$this->qp('PUT',$a);
					return $this->response;
				} else {
					return 'Error - No Account Id sent.';
				}
			} else {
				return 'Error - No Account Array sent.';
			}
		}

		
		public function lastAction($limit=1)
		{
			$this->page = '/Actions?_order=-id&_limit='.$limit;
			$this->qp('GET');
			return $this->response;
		}

}

