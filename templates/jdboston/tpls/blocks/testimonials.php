<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<?php if ($this->checkSpotlight('testimonials', 'testimonials-1')) : ?> 
	<!-- testimonials -->
	<div id="testimonials">
		<div class="container">
		<?php $this->spotlight('testimonials', 'testimonials-1') ?>
		</div>
	</div>
	<!-- //testimonials -->
<?php endif ?>