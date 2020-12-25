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
/**
 * Description of JOSC_tabRow
 *
 * @author Daniel Dimitrov
 */

class JOSC_tabRow {
    var $caption;
    var $component;
    var $help;
    var $id;
    function visible($visible = true)
    {
        if ($this->id) {
            echo "<script type='text/javascript'>";
            echo JOSC_element::get($this->id);
            echo JOSC_element::visible($visible);
            echo "</script>";
        }
    }

    function tabRow_htmlCode()
    {
        $cols = "\n<td align='left' valign='top'><b>$this->caption</b></td>\n";
        $colspan = ($this->help == false) ? " colspan='2'" : '';
        $cols .= "\n<td align='left' valign='top'$colspan>$this->component</td>\n";
        $cols .= ($this->help == false) ? '' : "\n<td align='left' valign='top' width='50%'>$this->help</td>\n";
        $id = $this->id ? " id='$this->id'" : "";
        return "\n<tr$id>$cols</tr>\n";
    }
}
?>
