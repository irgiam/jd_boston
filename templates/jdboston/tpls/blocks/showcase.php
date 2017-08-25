<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<!-- Showcase -->

	<?php if ($this->checkSpotlight('showcase', 'showcase-1, showcase-2, showcase-3, showcase-4')) : ?>
	<div id="showcase">
		<!-- Showcase NAVIGATION -->
		<div class="container">
			<?php $this->spotlight('showcase', 'showcase-1, showcase-2, showcase-3, showcase-4') ?>
		</div>
	</div>
		<!-- //Showcase NAVIGATION -->
	<?php endif ?>
<!-- //Showcase -->