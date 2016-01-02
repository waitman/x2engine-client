<?php

require_once('x2engineClient/x2engineClient.php');
use x2engineClient\x2engineClient;

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

/*
$test->getModels();
print_r($test->toArray());
exit();
*/

/*
echo "\nLast Contact\n";
$test->lastContact(0); 								 Get All Contacts (limit=0) 
print_r($test->toArray());
echo "\n";
*/

echo "\nSearch For Contact by Email\n";
$test->searchContactEmail('gobble.wa@gmail.com',1);
$res = $test->toArray();
if (count($res)>0)
{
	echo 'Found Contact Record(s)'."\n";
	if (count($res)>1)
	{
		echo "\nError: Multiple Records Found - Cannot Update\n";
		foreach ($res as $k=>$v)
		{
			echo "id: ".$v['id']."\n";
		}
	} else {
		$ci = array_pop($res);
		$id = $ci['id'];
		echo "id: ".$id."\n";
		//$test->updateContact($id,$a);
	}
	
} else {
	echo 'Contact Record Not Found'."\n";
	//$test->createContact($a);
}
exit();
echo "\n";
print_r($test->toArray());
echo "\n";

echo "\nSearch For Contact by First Name\n";
$test->searchContactFirst('jess',1);
echo "\n";
print_r($test->toArray());
echo "\n";

exit();

echo "\nLast Action\n";
$test->lastAction(10); 								/* Get Last 10 Actions */
print_r($test->toArray());
echo "\n";

echo "\Create Action\n";

$a = array(

	'assignedTo' => 'administration',
	'visibility' => 1,										/* 1 public */
	
	'color' => 'Pink',										/* 'Blue','Light Blue','Turquoise','Light Green','Yellow','Orange','Pink','Red','Purple','Gray' */
	
	'subject' => 'The Test Subject',
	'actionDescription' => 'This is the action description',

	'calendarId' => '',
	'associationId' =>	19,
	'associationType' => 'contacts',
	'associationName' => 'Jessica Sampson',
	'dueDate' => strtotime('2016-01-03 11:11:13'),
	'allDay' => '',
	
	'complete' => 'No',										/* 'Yes' or 'No' */
	'completeDate' => '',
	'completedBy' => 'administration',
	
	'reminder' => 1											/* remind user = 1 */
);

echo $test->createAction($a);



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
            
    'website' => 'http://www.arduent.com/',
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

