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

/**
 * Description of JOSC_tabRows
 *
 * @author Daniel Dimitrov
 */
class JOSC_tabRows {
    var $rows = '';
    function addRow(&$row)
    {
        $this->rows .= $row->tabRow_htmlCode();
    }

    function addTitle($title)
    {
        $this->rows .= "\n<tr><th colspan='3' class='title'>$title</th></tr>\n";
    }

    function addSeparator()
    {
        $this->rows .= "\n<tr><td colspan='3'><hr /></td></tr>\n";
    }

    /*
     * lines :
     * -	type		'title'	OR	'separator'	OR	'parameter'
     * -    param1  = 	 title							caption
     * -    param2  =									html input
     * -    param3  =									help
     */
    function createRow( $type=null, $param1=null, $param2=null, $param3=null ) {

            switch ($type) {
                case 'title' :
                	$this->addTitle($param1);
                	break;
                case 'separator':
                	$this->addSeparator();
                	break;
                case 'parameter':
	        		$row 			= new JOSC_tabRow();
       				$row->caption 	= $param1;
       				$row->component	= $param2;
       				$row->help 		= $param3;
       				$this->addRow($row);
                	break;
            }
	}

    function tabRows_htmlCode()
    {
        return "\n<table class='adminlist' width='100%' cellpadding='4' cellspacing='2'>\n$this->rows\n</table>\n";
    }

}
?>
