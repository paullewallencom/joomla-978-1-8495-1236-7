<?php
/* * *************************************************************
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
 * ************************************************************* */
defined('_JEXEC') or die('Restricted access');

JToolbarHelper::title('Compojoom comment settings per component');
JToolBarHelper::addNew();
JToolbarHelper::deleteList();
?>

<form action="" method="post" name="adminForm">
    <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminheading">
		<tr>
			<th width="100%" align="left"><?php echo $this->lists['title'] ?></th>
		</tr>
		<tr>
			<td width="100%"><?php echo JText::_('Filter'); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="inputbox" onchange="document.adminForm.submit();" />
				<button onclick="document.adminForm.submit();">
					<?php echo JText::_('Go'); ?>
				</button>
				<button onclick="document.getElementById('search').value = '';
							this.form.submit();">
							<?php echo JText::_('Reset'); ?>
				</button>
			</td>
		</tr>
    </table>

    <table cellpadding="4" cellspacing="0" border="0" style="width:500px" class="adminlist">
		<tr>
			<th class="title"><?php echo JText::_('Num'); ?></th>
			<th class="title">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" />
			</th>
			<th class="title" width="10%" ><?php echo JText::_('Component'); ?></th>
			<th class="title"><?php echo JText::_('Name'); ?></th>
			<th class="title"><?php echo JText::_('Id'); ?></th>
		</tr>
		<?php
							for ($i = 0, $n = count($this->rows); $i < $n; $i++) {
								$row = $this->rows[$i];
								$checked = JHTML::_('grid.id', $i, $row->id);
								$link = JRoute::_('index.php?option=com_comment&view=settings&controller=settings&task=edit&id=' . $row->id);
		?>
								<tr class="row<?php echo $i % 2; ?>">
									<td width="2%">
				<?php echo $i + 1; ?>
							</td>
							<td width="2%">
				<?php echo $checked ?>
							</td>
							<td>
								<a href="<?php echo $link; ?>">
									<b><?php echo $row->set_component; ?></b>
								</a>
							</td>
							<td>
								<a href="<?php echo $link ?>">
					<?php echo $row->set_name; ?>
							</a>
						</td>
						<td width="2%">
				<?php echo $row->id; ?>
							</td>
						</tr>
		<?php
							}
		?>
						</table>
						<input type="hidden" name="boxchecked" value="0" />
						<input type="hidden" name="option" value="com_comment" />
						<input type="hidden" name="task" value="" />
						<input type="hidden" name="controller" value="settings" />
	<?php echo JHTML::_('form.token'); ?>
</form>
