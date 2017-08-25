<?php
/**
 * @version    2.7.x
 * @package    K2
 * @author     JoomlaWorks http://www.joomlaworks.net
 * @copyright  Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die;

?>

<!-- Comments Form -->
<h3><?php echo JText::_('K2_LEAVE_A_COMMENT') ?></h3>

<?php if($this->params->get('commentsFormNotes')): ?>
<p class="itemCommentsFormNotes">
	<?php if($this->params->get('commentsFormNotesText')): ?>
	<?php echo nl2br($this->params->get('commentsFormNotesText')); ?>
	<?php else: ?>
	<?php echo JText::_('K2_COMMENT_FORM_NOTES') ?>
	<?php endif; ?>
</p>
<?php endif; ?>

<form action="<?php echo JURI::root(true); ?>/index.php" method="post" id="comment-form" class="form-validate">

<div class="row">
	<div class="col-md-4">
		<label class="formName" for="userName"><?php echo JText::_('K2_NAME'); ?> *</label>
		<input class="inputbox" type="text" name="userName" placeholder="Your Name" id="userName"/>
	</div>
	<div class="col-md-4">
		<label class="formEmail" for="commentEmail"><?php echo JText::_('K2_EMAIL'); ?> *</label>
		<input class="inputbox" type="text" name="commentEmail" placeholder="E-mail Address" id="commentEmail"/>
	</div>
	<div class="col-md-4">
		<label class="formUrl" for="commentURL"><?php echo JText::_('K2_WEBSITE_URL'); ?></label>
		<input class="inputbox" type="text" name="commentURL" placeholder="Web Address" id="commentURL"/>
	</div>
</div>

	<label class="formComment" for="commentText"><?php echo JText::_('K2_MESSAGE'); ?> *</label>
	<textarea rows="20" cols="10" class="inputbox" placeholder="Write Your Comments" name="commentText" id="commentText"></textarea>

	<?php if($this->params->get('recaptcha') && ($this->user->guest || $this->params->get('recaptchaForRegistered', 1))): ?>
	<?php if(!$this->params->get('recaptchaV2')): ?>
	<label class="formRecaptcha"><?php echo JText::_('K2_ENTER_THE_TWO_WORDS_YOU_SEE_BELOW'); ?></label>
	<?php endif; ?>
	<div id="recaptcha" class="<?php echo $this->recaptchaClass; ?>"></div>
	<?php endif; ?>

	<input type="submit" class="button" id="submitCommentButton" value="<?php echo JText::_('K2_SUBMIT_COMMENT'); ?>" />
	
	<span id="formLog"></span>

	<input type="hidden" name="option" value="com_k2" />
	<input type="hidden" name="view" value="item" />
	<input type="hidden" name="task" value="comment" />
	<input type="hidden" name="itemID" value="<?php echo JRequest::getInt('id'); ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>
