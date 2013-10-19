<?php


elgg_load_library('coopfunding:fundraising');

$guid = get_input('guid');

$container_guid = get_input('container_guid');
$container = get_entity($container_guid);
$contributor_guid = get_input('members')[0];
$contributor = get_entity($contributor_guid);
$amount  = (int) get_input('eur_amount');
if (!$amount) {$amount=0;}

$commit_date  = date(get_input('commit_date'));

if ($contributor) {
    
    //If changed user, remove transaction from old user
    if ($guid) {
        $transaction = get_entity($guid);
        $OldUser_guid = $transaction->contributor;
        
        if ($OldUser_guid != $contributor_guid){
            $contributions_set_old_user = fundraising_get_contributions_set($container_guid, $OldUser_guid);
            $contributions_set_old_user->eur_amount -= $amount;
            $container_guid->eur_amount -= $amount;
            
            if ($contributions_set_old_user->eur_amount <= 0 &&  $contributions_set_old_user->btc_amount <= 0) {
                 $contributions_set_old_user->delete();
            }
          
        }
    }

    //save new transaction
    $contributions_set = fundraising_get_contributions_set($container_guid, $contributor_guid);

    if (!$contributions_set) {
    	$contributions_set = new ElggObject();
    	$contributions_set->subtype = "contributions_set";
    	$contributions_set->owner_guid = $contributor_guid;
    	$contributions_set->container_guid = $container_guid;
    }
    
    if ($contributions_set_guid = $contributions_set->save()) {
        
        $transaction = get_entity($guid);

        if (!$transaction){
    	    $transaction = new ElggObject();
    	    $transaction->subtype = "transaction";
    	    $transaction->method = 'bankaccount';
    	   
        } else {
            
            //if edition take out all quantity
            $contributions_set->eur_amount -= $transaction->eur_amount;
            $container->eur_amount -= $transaction->eur_amount;
           
        }
        
    	$transaction->owner_guid = elgg_get_logged_in_user_guid();
    	$transaction->container_guid = $container_guid;
    	$transaction->eur_amount = $amount;
    	
    	$transaction->contributor = $contributor_guid;
    	$transaction->commit_date = $commit_date;
        $transaction->save();
    }
   
    $contributions_set->eur_amount += $amount;
    $container->eur_amount += $amount;
   
    system_message(elgg_echo('fundraising:contribute:success'));
    forward(elgg_get_site_url() . "fundraising/bankaccount/managedeposits/{$container_guid}");
    
} else {
    forward(REFERER);
}
    
  
  