<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<!-- feature -->
	<?php if ($this->checkSpotlight('feature', 'feature-1, feature-2, feature-3, feature-4')) : ?>
	<div id="feature">
		<!-- Feature NAVIGATION -->
		<div class="container">
			<?php $this->spotlight('feature', 'feature-1, feature-2, feature-3, feature-4') ?>
		</div>
	</div>
		<!-- //Feature NAVIGATION -->
	<?php endif ?>
<!-- //feature -->