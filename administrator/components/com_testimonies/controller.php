<?php
/**
 * @version		$Id: controller.php $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		kiennh
 * This component was generated by http://xipat.com/ - 2015
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Testimonies master display controller.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_testimonies
 * @since		1.6
 */
class TestimoniesController extends JControllerLegacy
{
	public $default_view = 'items';
	
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/testimonies.php';

		// Load the submenu.
		TestimoniesHelper::addSubmenu(JRequest::getCmd('view', 'items'));

		parent::display();

		return $this;
	}
}
