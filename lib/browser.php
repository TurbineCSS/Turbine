<?php


/**
 * Browser
 * Detects browsers by user agent string
 * 
 * Copyright (C) 2009 Peter Kröner
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Library General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Library General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class Browser{


	/**
		@var array $mobileAgents List of mobile user agents
	*/
	public $mobileAgents = array(
		'Android',
		'Blackberry',
		'Blazer',
		'Fennec',
		'Handspring',
		'iPhone',
		'iPod',
		'Kyocera',
		'LG',
		'Motorola',
		'Nokia',
		'Palm',
		'PlayStation Portable',
		'Samsung',
		'Smartphone',
		'SonyEricsson',
		'Symbian',
		'WAP',
		'Windows CE'
	);


	/**
		@var array $tvAgents List of tv user agents
	*/
	public $tvAgents = array(
		'Nintendo Wii',
		'Playstation 3',
		'WebTV'
	);


	/**
	 * isMobile
	 * Returns true for mobile user agents
	 * @return bool 
	 */
	public function isMobile(){
		
	}


	/**
	 * isTv
	 * Returns true for tv user agents
	 * @return bool 
	 */
	public function isTv(){
		
	}


	/**
	 * isAncient
	 * Returns true for a list of prehistoric user agents
	 * @return bool 
	 */
	public function isAncient(){
		
	}


}

?>