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
require_once( JPATH_COMPONENT_ADMINISTRATOR . DS .'library' . DS . 'JOSC_tabRows.php' );
require_once( JPATH_COMPONENT_ADMINISTRATOR . DS .'library' . DS . 'JOSC_tabRow.php' );
JToolBarHelper::title('Import comments');
JToolBarHelper::apply('apply', 'Preview');
JToolBarHelper::save('save', 'ImportAll');
jimport('joomla.html.pane');
?>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminheading">
	<tr>
		<th width="100%" align="left">Import Comments from one another Comment System to Compojoom Comment.</th>
	</tr>
</table>
<form action='' method='POST' name='adminForm'>

	<?php


	$tabs = & JPane::getInstance('tabs', '');
	echo $tabs->startPane("jos_importpanel");

	echo $tabs->startPanel("Mapping", "mapping");
	echo $this->loadTemplate('mapping');
	echo $tabs->endPanel();

	echo $tabs->startPanel("Preview", "preview");
	if(!$this->preview) {
		echo 'Select a table or component and click preview.';
	} else {
		echo $this->loadTemplate('preview');
	}

	echo $tabs->endPanel();

	echo $tabs->endPane();
	?>
	<input type='hidden' name='task' value='' />
	<input type='hidden' name='onchangecomponent' value='1' />
	<input type='hidden' name='option' value='com_comment' />
	<input type='hidden' name='controller' value='import' />
	<?php echo JHTML::_( 'form.token' );?>
</form>