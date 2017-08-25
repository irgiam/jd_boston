<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_footer
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/* echo "<pre>";
print_r($params);
echo "</pre>";  
 */
 
$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::ROOT().'/modules/mod_customeimg/style.css');
$doc->addScript(JURI::ROOT().'/modules/mod_customeimg/script.js');

 

?>
<div class="headerimg-container" style="background:url(<?php echo $params->get('myimage')?>) center top; background-size:cover; ">
	<div class="content-section">
		<div class="title"><?php echo $params->get('mytextvalue');?></div>
		<div class="description"><?php echo $params->get('description');?></div>
	</div>
</div>