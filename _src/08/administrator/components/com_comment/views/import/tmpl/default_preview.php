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
?>
    <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
		<thead>
			<tr>
				<th class="title"><?php echo JText::_('Num'); ?></th>
				<th class="title">
					<?php echo JText::_('viewcom_writer'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('viewcom_userid'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('viewcom_notify'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('viewcom_url'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('viewcom_date'); ?>
				</th>
				<th class="title" nowrap="nowrap">
					<?php echo JText::_('viewcom_comment' ); ?>
				</th>
				<th class="title">
					<?php echo JText::_('viewcom_contentitem'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('viewcom_published'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('viewcom_ip'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('viewcom_votingyes'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('viewcom_votingno'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('viewcom_parentid'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('viewcom_importtable'); ?>
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
				<?php echo $i+1 ; ?>
			</td>

			<td align="center">
				<?php echo $comment->name ; ?>
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
				<?php echo $comment->ctitle ?>
			</td>
			<td align="center">
				<?php echo $comment->published ?>
			</td>
			<td align="center"><?php echo $comment->ip; ?></td>
			<td align="center"><?php echo $comment->voting_yes; ?></td>
			<td align="center"><?php echo $comment->voting_no; ?></td>
			<td align="center"><?php echo $comment->parentid; ?></td>
			<td align="center"><?php echo $comment->importtable; ?></td>
		</tr>
		<?php endfor; ?>
		<?php if(!count($this->comments)) : ?>
		<tr>
			<td colspan="16">
				<?php echo JText::_('No comments'); ?>
			</td>
		</tr>
		<?php endif; ?>
		</tbody>
	</table>
