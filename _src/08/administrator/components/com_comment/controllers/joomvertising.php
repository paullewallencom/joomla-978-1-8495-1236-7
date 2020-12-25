<?php
/**
 * @version		23.03.2008
 * @package		!Compojoom Comment
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.controller' );

/**
 * Plugins Component Controller
 *
 * @package		Joomla
 * @subpackage	Plugins
 * @since 1.5
 */
class CommentControllerJoomvertising extends CommentController
{
    function __construct($default = array())
    {
        parent::__construct();
        JRequest::setVar( 'view', 'joomvertising' );
        JRequest::setVar( 'layout', JRequest::getVar('layout'));
    }

    function display() {
        parent::display();
    }

    function send() {
        $model =& $this->getModel('joomvertising');
        if($model->sendEmail()) {
            $message = JText::_('E-Mail sent');
            $layout = 'success';
        } else {
            $message = JText::_("Couldn't send E-Mail");
            $layout = JRequest::getVar('layout');
        }
        
        $this->setRedirect("index.php?option=com_comment&controller=joomvertising&layout=$layout", $message);
    }


    function saveStandardBannercode() {
        $model =& $this->getModel('joomvertising');

        if($model->saveStandardBannercode()) {
            $message = JText::_('Code written to database');
        } else {
            $message = JText::_('Error when writing to the database');
        }

        $this->setRedirect('index.php?option=com_comment&controller=joomvertising&layout=standard_banner', $message);
    }
    
}
?>
