<?php
/*
This PHP class is free software: you can redistribute it and/or modify
the code under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version. 

However, the license header, copyright and author credits 
must not be modified in any form and always be displayed.

This class is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

@author geoPlugin (gp_support@geoplugin.com)
@copyright Copyright geoPlugin (gp_support@geoplugin.com)

*/

class GeoPlugin {
	
	protected $host = 'http://www.geoplugin.net/php.gp?ip={IP}';
	
	public function locate($ip = null)
	{
		global $_SERVER;
		
		if (is_null($ip)) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		$host = str_replace('{IP}', $ip, $this->host);
		
		$response = $this->fetch($host);
		
		if (!$response) return;

		$data = @unserialize($response);
		
		return array(
			'ip' => $ip,
			'city' => @$data['geoplugin_city'],
			'countryCode' => @$data['geoplugin_countryCode'],
			'countryName' => @$data['geoplugin_countryName'],
			'continentCode' => @$data['geoplugin_continentCode'],
			'latitude' => @$data['geoplugin_latitude'],
			'longitude' => @$data['geoplugin_longitude']
		);
	}
	
	protected function fetch($host) 
	{
		if (function_exists('curl_init')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $host);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, 'geoPlugin PHP Class v1.0');
			$response = curl_exec($ch);
			curl_close ($ch);
		} elseif (ini_get('allow_url_fopen')) {
			$response = file_get_contents($host, 'r');
		}
		
		return isset($response) ? $response : null;
	}

	public static function countryCode()
	{
		$instance = new static;

		$data = $instance->locate();

		if (isset($data['countryCode'])) {
			return strtolower($data['countryCode']);
		}

		return '';
	}
}