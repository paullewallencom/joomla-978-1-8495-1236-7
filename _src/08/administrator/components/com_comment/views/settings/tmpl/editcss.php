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
$template_path = $this->file_css;
$template = $this->config->_template_custom;
$templateCSS = $this->config->_template_custom_css;
?>
<table cellpadding="1" cellspacing="1" border="0" width="100%">
    <tr>
	<td><table class="adminheading"><tr>
		    <th class="templates">CSS template editor : <?php echo $template; ?></th>
		</tr></table></td>
	<td>
	    <span class="componentheading">file is :
		<b><?php echo is_writable($template_path) ? '<font color="green"> Writable</font>' : '<font color="red"> Not Writable</font>'; ?></b>
	    </span>
	</td>
	<?php
	jimport('joomla.filesystem.path');
	if (JPath::canChmod($template_path)) {
	    if (is_writable($template_path)) {
		?>
	<td>
	    <input type="checkbox" id="disable_writeCSS" name="disable_writeCSS" value="1"/>
	    <label for="disable_writeCSS"></label>
				Set Not Writable
	    <label for="label">after saved</label></td>
	    <?php
	    } else {
		?>
	<td>
	    <input type="checkbox" id="enable_writeCSS" name="enable_writeCSS" value="1"/>
	    <label for="enable_writeCSS">Ignore the Writable / Not Writable status</label>			</td>
	    <?php
	    } // if
	} // if
	?>
    </tr>
</table>
<table class="adminform">
    <tr><th><?php echo $template_path; ?></th></tr>
    <tr><td><textarea style="width:100%;height:500px" cols="110" rows="25" name="joscTemplateCSScontent" class="inputbox"><?php echo $this->css; ?></textarea></td></tr>
</table>
<input type="hidden" name="joscTemplateCSS" value="<?php echo $template; ?>" />
<input type="hidden" name="joscTemplateCSSCSS" value="<?php echo $templateCSS; ?>" />
