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
 $rows = new JOSC_tabRows();

        //echo JOSC_library::hidden('component', '1'); /* to initialise and TODO: allows to change */
		$rows->createRow('title'	, 'Import Comments Mapping');
		$rows->createRow('parameter',
			'Import from standard comment system component', $this->lists['fromcomponent'],
			'<b>If your component is available in the list, select its value will propose automatically below the table and columns according to this component</b>.'
			. ' There should be missing columns dependent of the release of the components, verify the mapping and if needed, you can change it manually !'
			. ' It is possible that some columns have no equivalent in the source system. In this case leave them empty. For example mXcomment is using rating, not voting yes, and voting no. We affect "voting_yes" joomlacomment to "rating" and we leave empty "voting_no".'
			. '<br /><b>If your component is not in the list, you have to do the mapping manually</b>, selecting first the table, then the columns (see below).'
			. '<br />If you need help, do not hesitate to contact the Compojoom comment support !'
			);
		$rows->createRow('separator');
		$rows->createRow('parameter',
		'Import from table <b style="color:red;">(required)</b>', $this->lists['fromtablelist'], 'Select the <b>database table which contains the comments to import</b>' );
		$rows->createRow('parameter',
		'Commented component value <b style="color:red;">(required)</b>', $this->lists['componentlist'], 'Select the <b>Component name</b> from and to which you want to import comments.' );
		$rows->createRow('parameter',
		'Commented component column', JHTML::_('select.genericlist', $this->lists['columns'], 'componentfield', ' class="inputbox" ', 'Field', 'desc', $this->lists['sel_columns']['componentfield'] ), 'Select the column which contains the <b>Component name selection</b>.' );
		$rows->createRow('parameter',
		'--- Id from the column <b style="color:red;">(required)</b>', JHTML::_('select.genericlist', $this->lists['columns'], 'id', ' class="inputbox" ', 'Field', 'desc', $this->lists['sel_columns']['id'] ), 'Select the column which contains the <b>Comment Id</b>.' );
		$rows->createRow('parameter',
		'--- Content Id from the column <b style="color:red;">(required)</b>', JHTML::_('select.genericlist', $this->lists['columns'], 'contentid', ' class="inputbox" ', 'Field', 'desc', $this->lists['sel_columns']['contentid'] ), 'Select the column which contains the <b>Content Item Id</b>' );
		$rows->createRow('parameter',
		'--- Date from the column', JHTML::_('select.genericlist', $this->lists['columns'], 'date', ' class="inputbox" ', 'Field', 'desc', $this->lists['sel_columns']['date'] ), 'Select the column which contains the <b>Date of the comment</b>' );
		$rows->createRow('parameter',
		'--- Name from the column', JHTML::_('select.genericlist', $this->lists['columns'], 'name', ' class="inputbox" ', 'Field', 'desc', $this->lists['sel_columns']['name'] ), 'Select the column which contains the <b>Name</b> of the comment writer' );
		$rows->createRow('parameter',
		'--- Userid from the column', JHTML::_('select.genericlist', $this->lists['columns'], 'userid', ' class="inputbox" ', 'Field', 'desc', $this->lists['sel_columns']['userid'] ), 'Select the column which contains the <b>Userid</b> of the comment writer' );
		$rows->createRow('parameter',
		'--- IP from the column', JHTML::_('select.genericlist', $this->lists['columns'], 'ip', ' class="inputbox" ', 'Field', 'desc', $this->lists['sel_columns']['ip'] ), 'Select the column which contains the <b>IP</b> of the comment writer' );
		$rows->createRow('parameter',
		'--- Email from the column', JHTML::_('select.genericlist', $this->lists['columns'], 'email', ' class="inputbox" ', 'Field', 'desc', $this->lists['sel_columns']['email'] ), 'Select the column which contains the <b>Email</b> of the comment writer' );
		$rows->createRow('parameter',
		'--- Notify parameter from the column', JHTML::_('select.genericlist', $this->lists['columns'], 'notify', ' class="inputbox" ', 'Field', 'desc', $this->lists['sel_columns']['notify'] ), 'Select the column which contains the <b>Notify parameter</b> of the comment writer (notify if new post paramter)' );
		$rows->createRow('parameter',
		'--- Website from the column', JHTML::_('select.genericlist', $this->lists['columns'], 'website', ' class="inputbox" ', 'Field', 'desc', $this->lists['sel_columns']['website'] ), 'Select the column which contains the <b>Website</b> of the comment writer' );
		$rows->createRow('parameter',
		'--- Title from the column', JHTML::_('select.genericlist', $this->lists['columns'], 'title', ' class="inputbox" ', 'Field', 'desc', $this->lists['sel_columns']['title'] ), 'Select the column which contains the <b>Title</b> of the comment' );
		$rows->createRow('parameter',
		'--- Text from the column', JHTML::_('select.genericlist', $this->lists['columns'], 'comment', ' class="inputbox" ', 'Field', 'desc', $this->lists['sel_columns']['comment'] ), 'Select the column which contains the <b>Text</b> of the comment' );
		$rows->createRow('parameter',
		'--- Voting Yes count from the column', JHTML::_('select.genericlist', $this->lists['columns'], 'voting_yes', ' class="inputbox" ', 'Field', 'desc', $this->lists['sel_columns']['voting_yes'] ), 'Select the column which contains the <b>Voting Yes count</b> of the comment' );
        $rows->createRow('parameter',
		'--- Voting No count from the column', JHTML::_('select.genericlist', $this->lists['columns'], 'voting_no', ' class="inputbox" ', 'Field', 'desc', $this->lists['sel_columns']['voting_no'] ), 'Select the column which contains the <b>Voting No count</b> of the comment' );
        $rows->createRow('parameter',
		'--- Published from the column', JHTML::_('select.genericlist', $this->lists['columns'], 'published', ' class="inputbox" ', 'Field', 'desc', $this->lists['sel_columns']['published'] ), 'Select the column which contains the <b>Published parameter</b> of the comment' );
        $rows->createRow('parameter',
		'--- Parent id from the column', JHTML::_('select.genericlist', $this->lists['columns'], 'parentid', ' class="inputbox" ', 'Field', 'desc', $this->lists['sel_columns']['parentid'] ), 'Select the column which contains the <b>Parent Id</b> of the comment (when comment is linked as a child -- response -- of another comment)' );
		$rows->createRow('separator');
		$rows->createRow('parameter',
		'Save sql queries after import execution ?', $this->lists['savequeries'], 'will save the sql queries in a file (directory: media).' );

        echo $rows->tabRows_htmlCode();
?>
