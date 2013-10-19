<?php
/**
 * Delete campaign_reward entity
 *
 * @package campaign_reward
 */
elgg_load_library('coopfunding:fundraising');

$transaction_guid = get_input('guid');
$transaction = get_entity($transaction_guid);

if (elgg_instanceof($transaction, 'object', 'transaction') && $transaction->canEdit()) {
	
	$amount = $transaction->eur_amount;
   
    $contributions_set = fundraising_get_contributions_set($transaction->container_guid, $transaction->contributor);
    $container = get_entity($transaction->container_guid);
    
    if ($contributions_set) {
	    $contributions_set->eur_amount -= $amount;
	  
	    if ($contributions_set->eur_amount <= 0 && $contributions_set->btc_amount <= 0) {
	        $contributions_set->delete();
	    }
    }
    if ($container) {
        $container->eur_amount -= $amount;
    }
	
	if ($transaction->delete()) {
		system_message(elgg_echo('fundraising:bankaccount:message:error:delete_item'));
		forward("fundraising/bankaccount/managedeposits/$container->guid");
	} else {
		register_error(elgg_echo('fundraising:bankaccount:message:error:cannot_delete_item'));
	}
} else {
	register_error(elgg_echo('fundraising_bankaccount:error:item_not_found'));
}

forward(REFERER);