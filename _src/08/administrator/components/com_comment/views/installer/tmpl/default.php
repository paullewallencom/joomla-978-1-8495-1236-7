<?php
defined('_JEXEC') or die('Restricted access');
JToolbarHelper::title('Compojoom comment install addons');
?>
<div>
	<a href="<?php echo JRoute::_('index.php?option=com_comment&view=installer'); ?>">Install</a>
	<a href="<?php echo JRoute::_('index.php?option=com_comment&view=installer&controller=installer&task=manage'); ?>">Unintall</a>
</div>
<div style="clear:both;">
	<form enctype="multipart/form-data" action="index.php" method="post" name="filename">
		<table class="adminheading">
			<tr>
				<th class="install">
					<a name="install">Install New Plugin</a>
				</th>
			</tr>
		</table>

		<table class="adminform">
			<tr>
				<th>
			Upload Package File
				</th>
			</tr>
			<tr>
				<td align="left">
			Package File:
					<input class="input_box" id="install_package" name="install_package" type="file" size="70"/>
					<input class="button" type="submit" value="Upload File &amp; Install" />
				</td>
			</tr>
		</table>

		<input type="hidden" name="task" value="install"/>
		<input type="hidden" name="view" value="installer"/>
		<input type="hidden" name="controller" value="installer"/>
		<input type="hidden" name="option" value="com_comment"/>
		<?php echo JHTML::_( 'form.token' );?>
</form>
</div>