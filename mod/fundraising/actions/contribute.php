
<?php

elgg_load_library('coopfunding:fundraising');

$guid = get_input('guid');
$amount = get_input('amount');
$reward_guid = get_input('reward_guid'); 

$methods = fundraising_get_methods();

foreach ($methods as $method) {
	if (elgg_echo('fundraising:contribute:method', array($method)) == get_input('method')) {
		global $CONFIG;
		if (isset($CONFIG->libraries["coopfunding:fundraising:{$method}"])) {
			elgg_load_library("coopfunding:fundraising:{$method}");
		}
		if (function_exists("fundraising_contribute_{$method}")) {
		    call_user_func("fundraising_contribute_{$method}", $guid, $amount, $reward_guid);
		}		
	}
}

register_error(elgg_echo('fundraising:error:invalidmethod'));
forward(REFERER);
