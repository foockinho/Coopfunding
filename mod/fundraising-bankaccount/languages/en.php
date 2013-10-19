<?php
/**
* Fundraising-bankaccount language file
*/

$language = array(

	/**
	* Content
	*/

	'fundraising:bankaccount:title' => "Contribute with Bank Account deposit to %s",
	'fundraising:bankaccount:contributeToBAN' => "Make deposit of %s€ to this BAN: %s indicating this reference number: %s",
	'fundraising:bankaccount:contributeNoBAN' => "This entity is not configured to recieve Bank Account Transfers.",
	'fundraising:contributions:bankaccountEUReur' => '%.4f €',
	'fundraising:contribute:bankaccount:alreadydeposited' => "I have already done the deposit. Promise.",
	'fundraising:bankaccount' => 'Bank account transfers',
    'fundraising:bankaccount:newdeposit' => 'New deposit',
    'fundraising:bankaccount:editdeposit' => 'Edit deposit',
    'fundraising:bankaccount:manage' => 'Manage transfers',
    'fundraising:bankaccount:notransactions' => 'There is no transactions.',
    'fundraising:bankaccount:message:error:delete_item' => 'Deleted item',
    'fundraising:bankaccount:message:error:cannot_delete_item' => 'Cannot delete item',
    'fundraising:contributor' => 'Contributor',
    'fundraising:bankaccount:message' => 'A notification has been sent to admins that will wait for 10 days you to make effective the transaction. And also your suitable reward will be reserved during this period of time.',
    'fundraising:date' => 'Date',
    
    'fundraising:bankaccount:verified' => 'Transaction is verified?',

);

add_translation(basename(__FILE__, '.php'), $language);



