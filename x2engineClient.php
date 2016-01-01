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

$url = 'https://www.arduent.com/index.php/api2';

/* 
	optionally store auth key in /etc/x2engineAuth 
	otherwise create object with 
	$test = new x2engineClient('username','api key',$url);
*/

$access = explode('|',trim(str_replace("\r",'',str_replace("\n",'',file_get_contents('/etc/x2engineAuth')))));
$test = new x2engineClient($access[0],$access[1],$url);
echo $test."\n";
print_r($test->toArray());
echo "\n";

echo "Number Of Contacts In System: ". $test->contactsCount();
echo "\n";

echo "Show Contact 1:\n";
echo $test->contactRecord(1);
echo "\n";
print_r ($test->toArray());
echo "\n";

/* New Contact Record, Also used for Update */

$a = array(

	'assignedTo' => 'administration',				/* optional */
    'backgroundInfo' => 'totally hot chick',
    'visibility' => 1, 								/* 1=public */
    'priority' => 3, 								/* 1=Low,2=Medium,3=High */
    'rating' => 3,									/* 0 to 5 stars */
    
    'firstName' => 'Jessica',
    'lastName' => 'Sampson',
    'title' => 'Queen',

    'company' => 'Electric Gadgets Co',

    'address' => '123 Electric Ave',
    'address2' => '',
    'city' => 'San Jose',
    'state' => 'CA',
    'country' => 'US',
    'zipcode' => '95124',
	'timezone' => 'America/Los_Angeles',

    'phone' => '+1 650-900-8557',
    'phone2' => '',

    'email' => 'gobble.wa@gmail.com',    
    'facebook' => '',
	'googleplus' => '',
    'linkedin' => '',
    'otherUrl' => '',
    'skype' => 'waitman.gobble',
    'twitter' => '@waitman',
    'website' => 'http://www.cristinaribeiro.org/',
    
	'fingerprintId' => '',

    'doNotCall' => 0, 								/* 0 or 1 (true) */
    'doNotEmail' => 0,								/* 0 or 1 (true) */
    
    'leadDate' => strtotime('2016-01-01 06:30:00'),
    'leadscore' => 5, 								/* 0 through 5 */
    'leadSource' => 'Facebook', 					/* 'None','Google','Facebook','Walk In' */
    'leadstatus' => '',
    'leadtype' => 'In Person', 						/* 'None','Web','In Person','Phone','E-Mail' */

    
);

echo "\nCreate Contact\n";
echo $test->createContact($a);
echo "\nUpdate Contact\n";
echo $test->updateContact(2,$a);
echo "\nSearch For Contact by Email\n";
echo $test->searchContactEmail('gobble.wa@gmail.com'); /* returning unsupported media type 415, todo */
echo "\n";
print_r($test->toArray());
echo "\n";

echo "\nLast Action\n";
echo $test->lastAction(10); 						/* Get Last 10 Actions */
echo "\n";

echo "\nList Account 1\n";
echo $test->accountRecord(1);
echo "\n";
print_r($test->toArray());
echo "\n";


/* New Account Record, also used for update */

$a = array(

	'assignedTo' => 'administration',							/* optional */
	'visibility' => 1,											/* 1 public */

    'name' => 'Test Account',
	'description' => 'This is a test account',
	
    'address' => '123 Lovely Bush Place',
    'city' => 'Mahoganilia',
    'state' => 'CA',
    'zipcode' => '90210',
    'country' => 'US',
            
    'website' => 'http://www.aduent.com/',
    'phone' => '+1 650 999 0406',
            
    'annualRevenue' => 1000000.00,
        
    'closedate' => 0,
    
    'dealstatus' => 'Working',									/* 'Working','Won','Lost' */
    'dealvalue' => 400.44,
    
    'employees' => 5000,
    'expectedCloseDate' => strtotime('2016-03-01 08:00:00'),

    'interest' => 'hot sales',

    'leadDate' => strtotime('2016-01-02 08:00:00'),
    'leadscore' => 5,											/* 0 to 5 stars */
    'leadSource' => 'Google',									/* 'None','Google','Facebook','Walk In' */
    'leadstatus' => 'Accepted',									/* 'Unassigned','Assigned','Accepted','Working','Dead','Rejected' */
    'leadtype' => 'Phone',										/* 'None','Web','In Person','Phone','E-Mail' */
 
    'rating' => 3,												/* 0 to 5 stars */

    'tickerSymbol' => 'testa',
    'type' => 'Fun Type',
        
);



echo "\nCreate Account\n";
echo $test->createAccount($a);

echo "\nUpdate Account\n";
echo $test->updateAccount(2,$a);
echo "\n";

exit();

