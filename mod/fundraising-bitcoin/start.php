<?php
/**
 * Fundraising plugin
 *
 * @package Coopfunding
 * @subpackage Fundraising.Bitcoin
 */

elgg_register_event_handler('init', 'system', 'fundraising_bitcoin_init');

function fundraising_bitcoin_init() {
	elgg_register_library('coopfunding:fundraising:bitcoin', elgg_get_plugins_path() . 'fundraising-bitcoin/lib/fundraising-bitcoin.php');
	elgg_register_library('coopfunding:fundraising:bitcoin:addrgen', elgg_get_plugins_path() . 'fundraising-bitcoin/lib/fundraising-bitcoin-addrgen.php');
	elgg_register_action('fundraising/bitcoin-callback', dirname(__FILE__) . '/actions/bitcoin-callback.php', "public");
	elgg_register_plugin_hook_handler('fundraising', 'sum_amount', 'fundraising_bitcoin_sum_amount');

    elgg_register_library('coopfunding:fundraising', elgg_get_plugins_path() . 'fundraising/lib/fundraising.php');
    fundraising_register_method('bitcoin');
    fundraising_register_currency('btc');

	elgg_register_plugin_hook_handler('cron', 'hourly', 'fundraising_bitcoin_cron');
}

function fundraising_bitcoin_page_handler($page) {

	if (isset($page[1]) && $page[1] == 'contribute') {
	    
	   if (isset($page[2])) {
	        
	        $entity = get_entity($page[2]);    
	    
	        if($entity) {
    	        if (elgg_instanceof($entity, 'group', 'project')) {
                    $entity_text = 'project';
                    $entities_text = 'projects';
                }else{
                    $entity_text = 'fundcampaigns';
                    $entities_text = 'fundcampaigns';
                }
    		    elgg_load_library("elgg:{$entities_text}");
    		    
    			elgg_set_page_owner_guid($entity->guid);
    
    			elgg_push_breadcrumb(elgg_echo("{$entities_text}"), "{$entities_text}/all");
    			elgg_push_breadcrumb($entity->name, $entity->getURL());
    			elgg_push_breadcrumb(elgg_echo('fundraising:contribute'));
    
    			$title = elgg_echo('fundraising:bitcoin:title', array($entity->name));
    			$content = elgg_view('fundraising/bitcoin/contribute', array(
    				'entity' => $entity,
    				'amount' => get_input('amount'),
    			));
    			$body = elgg_view_layout('content', array(
    				'title' => $title,
    				'content' => $content,
    				'filter' => '',
    			));
    			echo elgg_view_page($title, $body);
    			return true;
	        }
		}
	} elseif (isset($page[1]) && $page[1] == 'bitcoin-callback') {
		include(elgg_get_plugins_path() . 'fundraising-bitcoin/actions/bitcoin-callback.php');
		return true;
	}

}

function fundraising_bitcoin_sum_amount($hook, $type, $returnvalue, $params) {
	$market_value = elgg_get_plugin_setting('market_value', 'fundraising-bitcoin');

	if (!$market_value) {
		return $returnvalue;
	}
        return (float) $returnvalue + (float) $params['entity']->btc_amount * (float) $market_value;
}

function fundraising_bitcoin_cron($hook, $entity_type, $returnvalue, $params) {
	$data = file_get_contents('https://blockchain.info/ticker');
	if ($data && ($json = json_decode($data, true))) {
		elgg_set_plugin_setting('market_value', $json["EUR"]["last"], 'fundraising-bitcoin');
	}
}
