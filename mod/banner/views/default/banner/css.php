<?php

/**
* Elgg blog CSS extender
*
* @package ElggBlog
*/

?>
.banner {
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	background: white url(<?php echo $CONFIG->wwwroot; ?>mod/banner/banner.png) no-repeat !important;
}

	
.banner .collapsable_box_content > div {
	padding: 20px;
	text-align: right;
	font-weight: bold;
	background-color: rgba(190, 190, 190, 0.5);	
}
