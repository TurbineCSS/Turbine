<?php

/**
 * Browser - A PHP browser sniffer
 * http://github.com/SirPepe/Browser
 *
 * Copyright (C) 2010 Peter Kröner
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

class Browser {

	/**
	 * @var string $ua User agent string
	 */
	public $ua = NULL;

	/**
	 * @var string $browser Browser type (e.g. "firefox")
	 */
	public $browser = 'unknown';

	/**
	 * @var float $browser_version Browser version (e.g. "3.6")
	 */
	public $browser_version = 'unknown';

	/**
	 * @var string $engine Browser engine (e.g. "webkit")
	 */
	public $engine = 'unknown';

	/**
	 * @var float $engine_version Browser engine version (e.g. "5.0")
	 */
	public $engine_version = 'unknown';

	/**
	 * @var string $platform OS type (e.g. "windows")
	 */
	public $platform = 'unknown';

	/**
	 * @var float $platform_version OS version (e.g. "4.0")
	 */
	public $platform_version = 'unknown';

	/**
	 * @var string $platform_type Browser platform type (e.g. "desktop")
	 */
	public $platform_type = 'unknown';

	/**
	 * @var array $mobile_agents List of keywords that may identify a mobile something
	 */
	public $mobile_agents = array(
		'android',
		'blackberry',
		'blazer',
		'handspring',
		'kyocera',
		'lg',
		'motorola',
		'nokia',
		'palm',
		'playstation portable',
		'samsung',
		'smartphone',
		'sonyericsson',
		'symbian',
		'wap',
		'htc(-|_)',
		'midip'
	);


	/**
	 * @var array $gecko_firefox_versions Maps gecko engine versions to firefox versions
	 */
	public $gecko_firefox_versions = array(
		'1.81' => '2.0',
		'1.9'  => '3.0',
		'1.91' => '3.5',
		'1.92' => '3.6',
		'1.93' => '4.0'
	);


	/**
	 * __construct
	 * Class constructor. Sets the user agent var
	 * @return void
	 */
	public function __construct($ua = NULL){
		if($ua !== NULL){
			$this->ua = $ua;
		}
		else{
			$this->ua = $_SERVER['HTTP_USER_AGENT'];
		}
	}


	/**
	 * parse
	 * Parses the user agent
	 * @return void
	 */
	public function parse(){
		if($this->ua){
			$this->get_platform();
			$this->get_browser();
		}
	}


	/**
	 * get_platform
	 * Tries to get the platform (os plus version number) from the ua string
	 * @return void
	 */
	public function get_platform(){
		// Windows
		if(preg_match('/windows/i', $this->ua)){
			$this->platform = 'windows';
			// Windows mobile or desktop?
			if(preg_match('/Windows CE/i',$this->ua)){
				$this->platform_type = 'mobile';
				$this->platform_version = '0';
			}
			else{
				if($this->detect_mobile_devices() !== false){
					$this->platform_type = 'mobile';
				}
				else{
					$this->platform_type = 'desktop';
				}
				$this->platform_version = $this->get_windows_version();
			}
		}
		// Mac
		elseif(preg_match('/mac/i', $this->ua)){
			$this->platform = 'mac';
			// Mobile or desktop?
			if(preg_match('/(iphone|ipod|mobile)/i', $this->ua)){
				// The iPad should count as "desktop"
				if(preg_match('/ipad/i', $this->ua)){
					$this->platform_type = 'desktop';
				}
				else{
					$this->platform_type = 'mobile';
				}
			}
			else{
				$this->platform_type = 'desktop';
			}
			$this->platform_version = $this->get_mac_version();
		}
		// Linux
		elseif(preg_match('/linux/i', $this->ua)){
			$this->platform = 'linux';
			// Mobile linux or desktop?
			if(preg_match('/(linux arm|android|kindle)/i', $this->ua)){
				$this->platform_type = 'mobile';
			}
			else{
				$this->platform_type = 'desktop';
			}
		}
		// Unix
		elseif(preg_match('/(dragonfly|freebsd|openbsd|solaris|sunos)/i', $this->ua)){
			$this->platform = 'unix';
			$this->platform_type = 'desktop';
		}
		// Other mobile devices
		else{
			$mobile_agent = $this->detect_mobile_devices();
			if($mobile_agent !== false){
				// Set as platform
				$this->platform = $mobile_agent;
				$this->platform_type = 'mobile';
				// Set as a browser if nothing better us available
				if($this->browser == 'unknown'){
					$this->browser = $mobile_agent;
				}
			}
		}
	}


	/**
	 * detect_mobile_devices
	 * When all else fails, it is probably some strange mobile thingy
	 * @return $agent A mobile user agent (or false if nothing is found)
	 */
	public function detect_mobile_devices(){
		foreach($this->mobile_agents as $agent){
			if(preg_match('/'.$agent.'/i', $this->ua)){
				return $agent;
			}
		}
		return false;
	}


	/**
	 * get_browser
	 * Tries to find out which browser we have here...
	 * @return void
	 */
	public function get_browser(){
		// Android
		if(preg_match('/android/i', $this->ua)){
			$this->browser = 'android';
			if(preg_match('/version\/([0-9\.]+)/i', $this->ua, $matches)){
				$this->browser_version = $this->version_to_float($matches[1]);
			}
			$this->engine = 'webkit';
			$this->engine_version = $this->get_webkit_version();
		}
		// Chrome
		elseif(preg_match('/chrome/i', $this->ua)){
			$this->browser = 'chrome';
			if(preg_match('/chrome\/([0-9\.]+)/i', $this->ua, $matches)){
				$this->browser_version = $this->version_to_float($matches[1]);
			}
			$this->engine = 'webkit';
			$this->engine_version = $this->get_webkit_version();
		}
		// Epiphany
		elseif(preg_match('/epiphany/i', $this->ua)){
			$this->browser = 'epiphany';
			if(preg_match('/epiphany\/([0-9\.]+)/i', $this->ua, $matches)){
				$this->browser_version = $this->version_to_float($matches[1]);
			}
			if(preg_match('/webkit/i', $this->ua)){
				$this->engine = 'webkit';
				$this->engine_version = $this->get_webkit_version();
			}
			elseif(preg_match('/gecko/i', $this->ua)){
				$this->engine = 'gecko';
				if(preg_match('/rv:([0-9\.]+)/i', $this->ua, $matches)){
					$this->engine_version = $this->version_to_float($matches[1]);
				}
			}
		}
		// iCab
		elseif(preg_match('/icab/i', $this->ua)){
			$this->browser = 'icab';
			$this->engine = 'icab';
			if(preg_match('/icab\/([0-9\.]+)/i', $this->ua, $matches)){
				$this->browser_version = $this->version_to_float($matches[1]);
				$this->engine_version = $this->version_to_float($matches[1]);
			}
			elseif(preg_match('/iCab ([0-9\.]+)/i',$this->ua, $matches) > 0){
				$this->browser_version = $this->version_to_float($matches[1]);
				$this->engine_version = $this->version_to_float($matches[1]);
			}
		}
		// IE
		elseif(preg_match('/msie/i', $this->ua)){
			$this->browser = 'ie';
			$this->engine = 'ie';
			if(preg_match('/msie ([0-9\.]+)/i', $this->ua, $matches)){
				$this->browser_version = $this->version_to_float($matches[1]);
				$this->engine_version = $this->version_to_float($matches[1]);
			}
		}
		// Konqueror
		elseif(preg_match('/konqueror/i', $this->ua)){
			$this->browser = 'konqueror';
			$this->engine = 'khtml';
			if(preg_match('/konqueror\/([0-9\.]+)/i', $this->ua, $matches)){
				$this->browser_version = $this->version_to_float($matches[1]);
			}
			if(preg_match('/khtml\/([0-9\.]+)/i', $this->ua, $matches)){
				$this->engine_version = $this->version_to_float($matches[1]);
			}
		}
		// Kindle
		elseif(preg_match('/kindle/i', $this->ua)){
			$this->browser = 'kindle';
			if(preg_match('/kindle\/([0-9\.]+)/i', $this->ua, $matches)){
				$this->browser_version = $this->version_to_float($matches[1]);
			}
		}
		// Midori
		elseif(preg_match('/midori/i', $this->ua)){
			$this->browser = 'midori';
			if(preg_match('/midori\/([0-9\.]+)/i', $this->ua, $matches)){
				$this->browser_version = $this->version_to_float($matches[1]);
			}
			if(preg_match('/webkit/i', $this->ua)){
				$this->engine = 'webkit';
				$this->engine_version = $this->get_webkit_version();
			}
			elseif(preg_match('/gecko/i', $this->ua)){
				$this->engine = 'gecko';
				if(preg_match('/rv:([0-9\.]+)/i', $this->ua, $matches)){
					$this->engine_version = $this->version_to_float($matches[1]);
				}
			}
		}
		// Opera
		elseif(preg_match('/opera/i', $this->ua)){
			$this->engine = 'opera';
			$this->engine_version = $this->get_opera_version();
			if(preg_match('/opera mini\/([0-9\.]+)/i', $this->ua, $matches)){
				$this->platform_type = 'mobile';
				$this->browser = 'opera mini';
			}
			else{
				$this->browser = 'opera';
			}
			$this->browser_version = $this->get_opera_version();
		}
		// Safari
		elseif(preg_match('/safari/i', $this->ua)){
			$this->browser = 'safari';
			if(preg_match('/version\/([0-9\.]+)/i', $this->ua, $matches)){
				$this->browser_version = $this->version_to_float($matches[1]);
			}
			$this->engine = 'webkit';
			$this->engine_version = $this->get_webkit_version();
		}
		// Firefox
		elseif(preg_match('/(firefox|minefield|namoroka)/i', $this->ua, $name)){
			$this->browser = 'firefox';
			$this->browser_version = $this->get_firefox_version($name[0]);
			$this->engine = 'gecko';
			$this->engine_version = $this->get_gecko_version();
		}
		// Firefox variations
		elseif(preg_match('/(songbird|flock|seamonkey)/i', $this->ua)){
			$this->browser = 'firefox';
			$this->browser_version = $this->get_firefox_version(null);
			$this->engine = 'gecko';
			$this->engine_version = $this->get_gecko_version();
		}
	}



	/**
	 * get_windows_version
	 * Extracts the os version number from a windows user agent string
	 * @return float The version number
	 */
	public function get_windows_version(){
		if(preg_match('/windows nt\s*([0-9\.]+)/i', $this->ua, $matches)){
			return $this->version_to_float($matches[1]);
		}
		elseif(preg_match('/windows\s*([0-9\.]+)/i', $this->ua, $matches)){
			return $this->version_to_float($matches[1]);
		}
		elseif(preg_match('/(windows xp|winxp)/i', $this->ua)){
			return 5.1;
		}
		elseif(preg_match('/(windows 95|win95)/i', $this->ua)){
			return 4;
		}
		elseif(preg_match('/(windows 98|win98)/i', $this->ua)){
			return 4.1;
		}
		elseif(preg_match('/windows me/i', $this->ua)){
			return 4.9;
		}
		else{
			return 0;
		}
	}


	/**
	 * get_mac_version
	 * Extracts the os version number from an osx user agent string
	 * @return float The version number
	 */
	public function get_mac_version(){
		if(preg_match('/os x (?:[a-z]* )?([0-9_\.]+)/i', $this->ua, $matches)){
			return $this->version_to_float($matches[1]);
		}
		elseif(preg_match('/os x/i', $this->ua)){
			return 10;
		}
		else{
			return 'unknown';
		}
	}


	/**
	 * get_webkit_version
	 * Extracts the webkit engine version number from an user agent string
	 * @return float The version number
	 */
	public function get_webkit_version(){
		if(preg_match('/webkit\/([0-9\.]+)/i', $this->ua, $matches)){
			return $this->version_to_float($matches[1]);
		}
		elseif(preg_match('/version\/([0-9\.]+)/i', $this->ua, $matches)){
			return $this->version_to_float($matches[1]);
		}
		else{
			return 'unknown';
		}
	}


	/**
	 * get_opera_version
	 * Extracts the browser version number from an opera user agent string
	 * @return mixed $version The version number
	 */
	public function get_opera_version(){
		if(!preg_match('/version\/([0-9\.]+)/i', $this->ua, $matches)){
			if(!preg_match('/opera mini\/([0-9\.]+)/i', $this->ua, $matches)){
				preg_match('/opera\/([0-9\.]+)/i', $this->ua, $matches);
			}
		}
		if(!empty($matches[1])){
			$version = $this->version_to_float($matches[1]);
		}
		else{
			$version = 'unknown';
		}
		return $version;
	}


	/**
	 * get_gecko_version
	 * Extracts the gecko version number from user agent string
	 * @return mixed $version The version number
	 */
	public function get_gecko_version(){
		if(preg_match('/rv:([0-9\.]+)/i', $this->ua, $matches)){
			$version = $this->version_to_float($matches[1]);
		}
		else{
			$version = 'unknown';
		}
		return $version;
	}


	/**
	 * get_firefox_version
	 * Extracts the browser version number from a firefox user agent string
	 * @param string $name Browser name (null for firefox variations)
	 * @return mixed $version The version number
	 */
	public function get_firefox_version($name = null){
		if($name !== null){
			if(preg_match('/'.$name.'(?:\/|[\s])([0-9\.]+)/i', $this->ua, $matches)){
				$version = $this->version_to_float($matches[1]);
			}
			else{
				$version = $this->get_firefox_version_by_engine($this->get_gecko_version());
			}
		}
		else{
			$version = $this->get_firefox_version_by_engine($this->get_gecko_version());
		}
		return $version;
	}


	/**
	 * get_firefox_version_by_engine
	 * Returns a firefox version number by a gecko engine version
	 * @param mixed $engine_version Engine verison
	 * @return mixed $version The browser version number
	 */
	public function get_firefox_version_by_engine($engine_version){
		if($engine_version !== 'unknown'){
			$version = '0.0';
			foreach($this->gecko_firefox_versions as $gecko => $firefox){
				if(floatval($gecko) > floatval($engine_version)){
					break;
				}
				else{
					$version = $firefox;
				}
			}
		}
		else{
			$version = 'unknown';
		}
		return $version;
	}


	/**
	 * version_to_float
	 * Method to transform version numbers like 1.2.3 to a float of 1.23
	 * @param string $version Input version number
	 * @return float $version Output version number
	 */
	private function version_to_float($version){
		if(preg_match('/([-_\.])/', $version, $matches)){
			$delimeter = $matches[0];
			$version_array = explode($delimeter, strval($version));
			$version = $version_array[0];
			if(count($version_array) > 1){
				$version .= '.';
				for($i = 1; $i<count($version_array); $i++){
					$version .= $version_array[$i];
				}
			}
			return floatval($version);
		}
		else{
			return floatval($version);
		}
	}


}

?>
