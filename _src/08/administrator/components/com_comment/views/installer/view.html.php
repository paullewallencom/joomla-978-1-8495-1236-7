<?php

/**
 * @version 1.0 $Id: view.html.php 2009-03-23
 * @package Joomla
 * @subpackage Compojoom Comment 
 * @copyright (C) 2008 - 2010 Compojoom.com
 * @license GNU/GPL, see LICENSE.php
 * Compojoom Comment is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.

 * Compojoom Comment is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with Compojoom Comment ; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * View class for the Install screen
 *
 * @package Joomla
 * @subpackage Compojoom Comment
 * @since 4.0
 */
class CommentViewInstaller extends JView {

	function display($tpl = null) {
		$task = JRequest::getVar('task');
		if($task) {
			$tpl = 'manage';
		}
		parent::display($tpl);
	}

}
?>
