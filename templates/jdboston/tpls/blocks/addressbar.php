<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<!-- addressbar -->

	<?php if ($this->checkSpotlight('addressbar', 'addressbar')) : ?>
	<div id="addressbar">
		<!-- Showcase NAVIGATION -->
		<div class="container">
			<?php $this->spotlight('addressbar', 'addressbar') ?>
		</div>
	</div>
		<!-- //addressbar NAVIGATION -->
	<?php endif ?>
<!-- //addressbar -->