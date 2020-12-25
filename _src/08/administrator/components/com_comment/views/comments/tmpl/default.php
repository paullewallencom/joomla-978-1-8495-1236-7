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
JToolbarHelper::title('Manage Comments');
JtoolbarHelper::publish();
JtoolbarHelper::unpublish();
JtoolbarHelper::editList();
JToolbarHelper::deleteList();
// add the JavaScript for the tooltip
JHTML::_('behavior.tooltip');
?>
<form action="" method="post" name="adminForm">
	<table>
		<tr>
			<td width="100%">
				<?php echo JText::_('Filter'); ?>:
				<input type="text" name="search" id="search"
					   value="<?php echo $this->lists['search']; ?>" class="text_area"
					   onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();">
					<?php echo JText::_('GO'); ?>
				</button>
				<button onclick="document.getElementById('search').value='';
											this.form.getElementById('filter_state').value='';
											this.form.getElementById('component').value='';
											this.form.submit();">
							<?php echo JText::_('RESET'); ?>
				</button>
			</td>
			<td nowrap="nowrap">
				<?php echo $this->lists['componentlist']; ?>
				<?php echo $this->lists['state']; ?>
			</td>

		</tr>
	</table>
    <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
		<thead>
			<tr>
				<th class="title"><?php echo JText::_('Num'); ?></th>
				<th width="2%" class="title">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->comments); ?>);" />
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', 'viewcom_writer', 'name', $this->lists['orderDirection'], $this->lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort','viewcom_userid', 'userid', $this->lists['orderDirection'], $this->lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort','viewcom_notify', 'notify', $this->lists['orderDirection'], $this->lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort','viewcom_url', 'url', $this->lists['orderDirection'], $this->lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort','viewcom_date', 'date', $this->lists['orderDirection'], $this->lists['order']); ?>
				</th>
				<th class="title" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort','viewcom_comment', 'comment', $this->lists['orderDirection'], $this->lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort','viewcom_contentitem', 'contentid', $this->lists['orderDirection'], $this->lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort','viewcom_published', 'published', $this->lists['orderDirection'], $this->lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort','viewcom_delete', 'delete', $this->lists['orderDirection'], $this->lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort','viewcom_ip', 'delete', $this->lists['orderDirection'], $this->lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort','viewcom_votingyes', 'voting_yes', $this->lists['orderDirection'], $this->lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort','viewcom_votingno', 'voting_no', $this->lists['orderDirection'], $this->lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort','viewcom_parentid', 'parentid', $this->lists['orderDirection'], $this->lists['order']); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort','viewcom_importtable', 'importtable', $this->lists['orderDirection'], $this->lists['order']); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php
				for ($i=0, $n=count($this->comments); $i<$n; $i++) :
				$comment =& $this->comments[$i];
			?>
		<tr class="row<?php echo $i%2; ?>">
			<td align="center">
				<?php echo $this->pagination->getRowOffset( $i ) ?>
			</td>
			<td>
				<?php echo $comment->checked; ?>
			</td>

			<td align="center">
				<a href="<?php echo $comment->link_edit; ?>"><?php echo $comment->name ; ?></a>
			</td>
			<td align="center">
				<?php echo $comment->userid; ?>
			</td>
			<td align="center">
				<?php echo $comment->notify; ?>
			</td>
			<td align="center">
				<?php echo $comment->website; ?>
			</td>
			<td align="center">
				<?php echo $comment->date; ?>
			</td>
			<td>
				<?php echo $comment->comment; ?>
			</td>
			<td align="center">
				<a href="<?php echo $comment->link ?>"><?php echo ($comment->ctitle) ? $comment->ctitle : 'This item has no title'; ?></a>
			</td>
			<td align="center">
				<span class="hasTip" title="<?php echo JText::_( 'NOTIFYPUBLISH' );?>">
					<?php echo $comment->published ?>
				</span>
			</td>
			<td align="center">
				<span class="hasTip" title="<?php echo JText::_( 'NOTIFYREMOVE' );?>">
					<?php echo $comment->delete; ?>
				</span>
			</td>
			<td align="center"><?php echo $comment->ip; ?></td>
			<td align="center"><?php echo $comment->voting_yes; ?></td>
			<td align="center"><?php echo $comment->voting_no; ?></td>
			<td align="center"><?php echo $comment->parentid; ?></td>
			<td align="center"><?php echo $comment->importtable; ?></td>
		</tr>
		<?php
				endfor;
				if(!count($this->comments)) :
			?>
		<tr>
			<td colspan="16">
				<?php echo JText::_('No comments'); ?>
			</td>
		</tr>
		<?php endif; ?>
		<tr>
			<td colspan="16">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
		</tbody>
	</table>
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['orderDirection']; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_comment" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="comments" />
	<input type="hidden" name="confirm_notify" value="" />
	<?php echo JHTML::_( 'form.token' );?>
</form>