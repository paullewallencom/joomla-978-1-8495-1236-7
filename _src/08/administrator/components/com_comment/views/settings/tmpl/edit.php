<?php
/***************************************************************
*  Copyright notice
*
*  Copyright 2009 Daniel Dimitrov. (http://compojoom.com)
*  All rights reserved
*
*  This script is part of the Compojoom Comment project. The Compojoom Comment project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
defined('_JEXEC') or die('Restricted access');
require_once( JPATH_COMPONENT_ADMINISTRATOR . DS .'library' . DS . 'JOSC_tabRows.php' );
require_once( JPATH_COMPONENT_ADMINISTRATOR . DS .'library' . DS . 'JOSC_tabRow.php' );
JOSC_library::initVisibleJScript();

JToolbarHelper::save();
JToolbarHelper::apply();
JToolbarHelper::cancel();
?>
<form action='index.php' method='POST' name='adminForm'>
    
    <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminheading">
	<tr>
	    <th  width="100%" align="left">
	    <?php echo $this->config->_set_name . " / " . $this->config->_set_component; ?></th>
	    <td  nowrap="nowrap">
		<?php echo JTEXT::_('SETTING_LINE_NAME'); ?>
	    </td>
	    <td>
		<input type="text" name="set_name" value="<?php echo $this->config->_set_name; ?>" class="inputbox" />
	    </td>
	    <td  nowrap="nowrap">
		<?php echo JTEXT::_('SETTING_LINE_COMPONENT'); ?>
	    </td>
	    <td>
<?php
		$component		= JOSC_library::getComponentList();
        echo JHTML::_('select.genericlist',$component, 'set_component', 'class="inputbox" ', 'value', 'text', $this->config->_set_component);
?>
	    </td>
	</tr>
    </table>
    <?php
    jimport('joomla.html.pane');

    $pane =& JPane::getInstance('tabs', '');
    echo $pane->startPane( 'pane' );


    echo $pane->startPanel(JText::_('TAB_GENERAL_PAGE'), "General-page");
		require_once('general.php');
    echo $pane->endPanel();




    echo $pane->startPanel(JTEXT::_('TAB_SECURITY'), "Security-page");
		require_once('security.php');
    echo $pane->endPanel();


    echo $pane->startPanel(JText::_('TAB_POSTING'), "Posting-page");
		require_once('posting.php');
    echo $pane->endPanel();



    echo $pane->startPanel(JText::_('TAB_LAYOUT'), "Layout-page");
		require_once('layout.php');
    echo $pane->endPanel();

    if ($this->config->_template_modify && $this->config->_template_custom) {
	echo $pane->startPanel(JText::_('CSS'), "TemplateCSS");
	    require_once('editcss.php');
	echo $pane->endPanel();

	echo $pane->startPanel(JText::_('HTML'), "TemplateHTML");
	    require_once('edithtml.php');
	echo $pane->endPanel();

    }

	echo $pane->startPanel(JText::_('COMPOJOOMCOMMENT_TAB_INTEGRATIONS'), "Integrations");
		require_once('integrations.php');
    echo $pane->endPanel();


    echo $pane->endPane();


    ?>
    <input type="hidden" name="set_id" value="<?php echo $this->set_id>0 ? $this->set_id:''; ?>" />
    <input type="hidden" name="controller" value="settings" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="option" value="<?php echo $option; ?>" />
	<?php echo JHTML::_( 'form.token' );?>
</form>