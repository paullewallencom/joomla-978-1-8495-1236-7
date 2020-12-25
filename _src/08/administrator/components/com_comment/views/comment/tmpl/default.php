<?php
defined('_JEXEC') or die('Restricted access');
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
JToolbarHelper::title('Edit Comment');
JtoolbarHelper::save();
JtoolbarHelper::apply();
JToolbarHelper::cancel();
?>
<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		if (form.comment.value == ""){
			alert( "You must at least write the comment text." );
		} else if (form.contentid.value == "0"){
			alert( "You must select a corresponding content item." );
		} else {
			submitform( pressbutton );
		}
	}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
    <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">

		<tr>
			<td width="20%" align="right"><?php echo JText::_('Component'); ?>:</td>
			<td width="80%">
				<?php echo $this->lists['component']; ?>
			</td>
		</tr>
		<tr>
			<td valign="top" align="right">Content Item:</td>
			<td>
				<?php echo $this->lists['content']; ?>
			</td>
		</tr>
		<tr>
			<td width="20%" align="right">Name:</td>
			<td width="80%">
				<input class="inputbox" type="text" name="name" size="50" maxlength="30" value="<?php echo htmlentities(stripslashes($this->comment->name)); ?>" />
			</td>
		</tr>
		<tr>
			<td width="20%" align="right">Userid:</td>
			<td width="80%"><?php echo $this->lists['userid']; ?> </td>
		</tr>
		<tr>
			<td width="20%" align="right">Email:</td>
			<td width="80%">
				<input class="inputbox" type="text" name="email" size="50" maxlength="50" value="<?php echo htmlentities(stripslashes($this->comment->email)); ?>" />
			</td>
		</tr>
		<tr>
			<td width="20%" align="right">Date:</td>
			<td width="80%">
				<input class="inputbox" type="text" name="date" size="50" maxlength="50" value="<?php echo htmlentities(stripslashes($this->comment->date)); ?>" />
			</td>
		</tr>
		<tr>
			<td width="20%" align="right">Website:</td>
			<td width="80%">
				<input class="inputbox" type="text" name="website" size="50"  maxlength="100" value="<?php echo htmlentities(stripslashes($this->comment->website)); ?>" />
			</td>
		</tr>

		<tr>
			<td width="20%" align="right">Notify:</td>
			<td width="80%">
				<?php echo JHTML::_('select.booleanlist', 'notify', 'class="inputbox"', htmlentities(stripslashes($this->comment->notify))); ?>
			</td>
		</tr>

		<tr>
			<td valign="top" align="right">Title:</td>
			<td>
				<input class="inputbox" type="text" name="title" value="<?php echo htmlentities(stripslashes($this->comment->title));

					   ?>" size="50" maxlength="50" />
			</td>
		</tr>

		<tr>
			<td valign="top" align="right">Comment:</td>
			<td>
				<textarea class="inputbox" cols="50" rows="5" name="comment"><?php echo htmlentities(stripslashes($this->comment->comment));

					?></textarea>
			</td>
		</tr>

		<tr>
			<td valign="top" align="right">IP:</td>
			<td>
				<input class="inputbox" type="text" name="ip" value="<?php echo  $this->comment->ip; ?>" />
			</td>
		</tr>

		<tr>
			<td valign="top" align="right">Published:</td>
			<td>
				<?php echo JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $this->comment->published); ?>
			</td>
		</tr>

    </table>

    <input type="hidden" name="id" value="<?php echo $this->comment->id; ?>" />
    <input type="hidden" name="option" value="com_comment" />
	<input type="hidden" name="controller" value="comment" />
    <input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' );?>
</form>