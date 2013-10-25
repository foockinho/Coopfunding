<?php
/**
 * Profile widgets/tools
 * 
 * @package Coopfunding
 * @subpackage Projects
 */ 

echo '<ul id="projects-tools" class="elgg-gallery elgg-gallery-fluid mtl clearfix">';

// enable tools to extend this area
//echo elgg_view("projects/tool_latest", $vars);
// enable tools to extend this area
echo elgg_view("projects/campaigns", $vars);

echo "</ul>";



