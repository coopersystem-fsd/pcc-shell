<?php

namespace WPGMZA;

class CloudAPI
{
	const URL	= "https://www.wpgmaps.com/cloud/public";
	
	public function __construct()
	{
		global $wpgmza;
		
		add_filter('script_loader_tag', array($this, 'onScriptLoaderTag'), 11, 3);
	}
	
	public static function isCloudKey($key)
	{
		return preg_match('/^wpgmza/', $key);
	}
	
	protected static function getFullRequestURI()
	{
		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	}
	
	public static function exchangeKey($cloudKey)
	{
		global $wp_version;
		
		$url	= CloudAPI::URL . '/keys/exchange/' . $cloudKey;
		$ch		= curl_init($url);
		
		curl_setopt($ch, CURLOPT_USERAGENT, 'WordPress/' . $wp_version . '; ' . get_bloginfo('url'));
		curl_setopt($ch, CURLOPT_REFERER, CloudAPI::getFullRequestURI());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($ch);
		
		if(!$response)
			throw new \Exception('Invalid response from Cloud API server');
		
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		switch($http_code)
		{
			case 200:
				break;
			
			case 401:
			case 403:
				throw new \Exception("Server refused reequest (HTTP code $http_code)");
				break;
			
			default:
				trigger_error("Unknown HTTP response code $http_code from Cloud API Server", E_USER_WARNING);
				break;
		}
		
		$json = json_decode($response);
		
		if(!$json)
			throw new \Exception('Cloud API server sent an invalid response :- ' . $response);
		
		return $json->key;
	}
	
	public function onScriptLoaderTag($tag, $handle, $src)
	{
		global $wpgmza;
		
		if($handle == 'wpgmza_api_call')
		{
			if(CloudAPI::isCloudKey($wpgmza->settings->wpgmza_google_maps_api_key))
			{
				// header('Access-Control-Allow-Origin: wpgmaps.com');
				
				$cloudKey	= $wpgmza->settings->wpgmza_google_maps_api_key;
				
				try{
					$googleKey	= CloudAPI::exchangeKey($cloudKey);
				}catch(\Exception $e) {
					return "<script>console.warn('WP Google Maps - Cloud API :- " . addslashes($e->getMessage()) . "');</script>";
				}
				
				$tag		= str_replace($cloudKey, $googleKey, $tag);
			}
		}
		
		return $tag;
	}
}