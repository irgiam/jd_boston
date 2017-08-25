<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// get params
$sitename  = $this->params->get('sitename');
$slogan    = $this->params->get('slogan', '');
$logotype  = $this->params->get('logotype', 'text');
$logoimage = $logotype == 'image' ? $this->params->get('logoimage', T3Path::getUrl('images/logo.png', '', true)) : '';
$logoimgsm = ($logotype == 'image' && $this->params->get('enable_logoimage_sm', 0)) ? $this->params->get('logoimage_sm', T3Path::getUrl('images/logo-sm.png', '', true)) : false;

if (!$sitename) {
	$sitename = JFactory::getConfig()->get('sitename');
}

$logosize = 'col-sm-3';
if ($headright = $this->countModules('head-search or languageswitcherload')) {
	$logosize = 'col-sm-8';
}

?>

<!-- HEADER -->
<header id="t3-header" class="t3-header">

<?php if ($this->checkSpotlight('topbar', 'topbar-1, topbar-2')) : ?>
	<!-- topbar -->
	<div id="topbar">
		<div class="container">
		<?php $this->spotlight('topbar', 'topbar-1, topbar-2') ?>
		</div>
	</div>
	<!-- //topbar -->
<?php endif ?>

<!-- //HEADER -->

<div class="container header">
	<div class="row">

		<!-- LOGO -->
		<div class="col-xs-12 <?php echo $logosize ?> logo">
			<div class="navbar-header mobile-off-canvas">
			<?php if ($this->getParam('addon_offcanvas_enable')) : ?>
				<?php $this->loadBlock ('off-canvas') ?>
				<?php endif ?>
			</div>
			<div class="logo-<?php echo $logotype, ($logoimgsm ? ' logo-control' : '') ?>">
				<a href="<?php echo JUri::base() ?>" title="<?php echo strip_tags($sitename) ?>">
					<?php if($logotype == 'image'): ?>
						<img class="logo-img" src="<?php echo JUri::base(true) . '/' . $logoimage ?>" alt="<?php echo strip_tags($sitename) ?>" />
					<?php endif ?>
					<?php if($logoimgsm) : ?>
						<img class="logo-img-sm" src="<?php echo JUri::base(true) . '/' . $logoimgsm ?>" alt="<?php echo strip_tags($sitename) ?>" />
					<?php endif ?>
					<span><?php echo $sitename ?></span>
				</a>
				<small class="site-slogan"><?php echo $slogan ?></small>
			</div>
			<?php if ($this->getParam('navigation_collapse_enable', 1) && $this->getParam('responsive', 1)) : ?>
				<?php $this->addScript(T3_URL.'/js/nav-collapse.js'); ?>
				<button type="button" class="navbar-toggle default-menu-icon" data-toggle="collapse" data-target=".t3-navbar-collapse">
					<div></div>
				</button>
			<?php endif ?>
		</div>
		<!-- //LOGO -->
		<nav id="t3-mainnav" class="col-xs-12 col-sm-9 navbar navbar-default t3-mainnav">
				<!-- Brand and toggle get grouped for better mobile display -->
				<?php if ($this->getParam('navigation_collapse_enable')) : ?>
					<div class="t3-navbar-collapse navbar-collapse collapse mobile-menu"></div>
				<?php endif ?>
				<div class="t3-navbar navbar-collapse collapse main-menu">
					<jdoc:include type="<?php echo $this->getParam('navigation_type', 'megamenu') ?>" name="<?php echo $this->getParam('mm_type', 'mainmenu') ?>" />
				</div>
		</nav>
		<?php if ($headright): ?>
			<div class="col-xs-12 col-sm-4">
				<?php if ($this->countModules('languageswitcherload')) : ?>
					<!-- LANGUAGE SWITCHER -->
					<div class="languageswitcherload">
						<jdoc:include type="modules" name="<?php $this->_p('languageswitcherload') ?>" style="raw" />
					</div>
					<!-- //LANGUAGE SWITCHER -->
				<?php endif ?>
			</div>
		<?php endif ?>
	</div>
</div>
</header>