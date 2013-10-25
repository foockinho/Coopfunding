<?php
/**
 * Profile widgets/tools
 * 
 * @package Coopfunding
 * @subpackage Projects
 */ 

echo '<ul id="projects-tools" class="elgg-gallery elgg-gallery-fluid mtl clearfix">';

echo elgg_view("projects/campaigns", $vars);
echo elgg_view("projects/blogs", $vars);

echo "</ul>";



