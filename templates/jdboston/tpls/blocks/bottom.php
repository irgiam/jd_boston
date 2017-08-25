<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<!-- Bottom -->

<?php if ($this->checkSpotlight('bottom', 'bottom-1, bottom-2, bottom-3, bottom-4')) : ?>
	<div id="bottom">
		<!-- Bottom NAVIGATION -->
		<div class="container">
			<?php $this->spotlight('bottom', 'bottom-1, bottom-2, bottom-3, bottom-4') ?>
		</div>
		<!-- //Bottom NAVIGATION -->
	</div>
<?php endif ?>

<!-- //Bottom -->