<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
?>
<!-- FOOTER -->
<?php if ($this->checkSpotlight('footer', 'footer-1, footer-2, footer-3, footer-4')) : ?>
<footer id="footer" class="wrap t3-footer">
		<!-- FOOT NAVIGATION -->
		<div class="container">
			<?php $this->spotlight('footer', 'footer-1, footer-2, footer-3, footer-4') ?>
		</div>
		<!-- //FOOT NAVIGATION -->
</footer>
<?php endif ?>
<!-- //FOOTER -->
<!-- BACK TOP TOP BUTTON -->
	
<div id="back-to-top" data-spy="affix" data-offset-top="300" class="back-to-top affix-top">
  <button class="btn btn-primary" title="Back to Top"><i class="fa fa-angle-double-up" aria-hidden="true"></i></button>
</div>
<script type="text/javascript">
(function($) {
	// Back to top
	$('#back-to-top').on('click', function(){
		$("html, body").animate({scrollTop: 0}, 500);
		return false;
	});
})(jQuery);
</script>

<!-- BACK TO TOP BUTTON -->