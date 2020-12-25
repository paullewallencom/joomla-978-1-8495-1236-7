<?php
/**
 * @version 1.0 $Id: view.html.php 2009-03-23
 * @package Joomla
 * @subpackage Compojoom Comment 
 * @copyright (C) 2008 - 2009 Compojoom.com
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

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
/**
 * View class for the JoomVertising! screen
 *
 * @package Joomla
 * @subpackage Compojoom Comment
 * @since 4.0
 */
class CommentViewJoomvertising extends JView {
    function display($tpl = null)
    {
        if (JRequest::getVar('layout') == 'standard_banner') {
            $model = $this->getModel('joomvertising');
            $contents = $model->getStandardBannerCode();

            $this->assignRef('contents', $contents);
        }
        parent::display($tpl);
    }

    function quickiconButton( $link, $image, $text )
	{
		$mainframe =& JFactory::getApplication();
		$lang		=& JFactory::getLanguage();
		$template	= $mainframe->getTemplate();
		?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="<?php echo JRoute::_($link); ?>">
					<?php echo JHTML::_('image.site',  $image, '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
		<?php
	}
}

?>
