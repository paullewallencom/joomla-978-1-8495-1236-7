<?php
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
/***************************************************************
*  $Revision$
*
*  Copyright notice
*
*  Copyright 2009 Daniel Dimitrov. (http://compojoom.com)
*  All rights reserved
*
*  This script is part of the !JoomlaComment project. The !joomlaComment project is
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
class JOSC_tree {
    var $_id;
    var $_counter;

    function getSeed($data, $seed)
    {
        $this->_counter++;
        if ($seed) {
            foreach($seed as $item) {
                $data[$item]['wrapnum'] = $this->_counter;
                $this->_new[] = $data[$item];
                if (isset($data[$item]['seed']) && $data[$item]['seed']) {
                    $this->getSeed($data, $data[$item]['seed']);
                    $data[$item] = null;
                }
            }
        }
        $this->_counter--;
    }

    function build($data)
    {
        $index = 0;
        $this->_new = null;
        $this->_counter = 0;
        /*
         * TREE :
         * 	parents can have several direct children
         * 	their children can have also their own children etc...
         *
         * 	parent
         * 		|_	child1
         * 		|		|_	child1.1
         * 		|		|			|_ child1.1.1
         * 		|		|			|...
         * 		|		|_	child1.2
         * 		|		...
         * 		|_	child2
         * 		...
         *
         * SEED for one parent is the CHILDS ARRAY
         */

		/*
		 * FIRST LOOP : prepare datas
		 *
		 * $index is $data key  (we call it: INDEX)
		 *
		 * $old[] : key = comment_id / value = INDEX
		 *
		 * - save INDEX in a new 'treeid' column
		 *
		 * - for all children: replace parentid value by PARENT INDEX value
		 * -> sort must be with parents first !! (means already set in old)
		 *
		 */
        foreach($data as $item) {
            $old[$item['id']] = $index;
            $data[$index]['treeid'] = $index;
            if ($data[$index]['parentid'] != -1)
            	$data[$index]['parentid'] = isset($old[$item['parentid']]) ? $old[$item['parentid']] : -2;
            $index++;
        }

		/*
		 * 2ND LOOP : construct SEED
		 *
		 * - for all childrens : construct 1st level 'seed'[]
		 */
        foreach($data as $item) {
        	/*		IS CHILD			->			PARENT[SEED][] = CHILD INDEX				*/
        	if ($item['parentid'] >= 0) {
        		 $data[$item['parentid']]['seed'][] = $item['treeid'];
        	}
        }
        foreach($data as $item) {
        	/*		IS NOT A CHILD		->			DATA[]				*/
            if ($item['parentid'] == -1) {
                $this->_new[] = $item;
                if (isset($item['seed'])) $this->getSeed($data, $item['seed']);
            }
        }

        return $this->_new;
    }

    function getRootId( &$data, $index )
    {
    	if ($data[$index]['parentid']!=-1) {
    		/* is a child */
    		if (!$data[$index]['treerootid']) {
    			/* for every nodes, treerootid = root */
    			$data[$index]['treerootid'] = $this->getRootId($data, $data[$index]['parentid']);
    		}
    		return $data[$index]['treerootid'];
    	} else {
    		return $data[$index]['treeid'];
    	}
    }
}

?>
