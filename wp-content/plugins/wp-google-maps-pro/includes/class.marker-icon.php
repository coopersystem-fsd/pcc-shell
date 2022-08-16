<?php

namespace WPGMZA;

class MarkerIcon extends Factory implements \JsonSerializable
{
	public $url = "";
	public $retina = false;
	
	public function __construct($arg=null)
	{
		if(is_string($arg))
		{
			$obj = @json_decode($arg);
			
			if(!$obj)
				$this->url = $arg;
			else foreach($obj as $key => $value)
				$this->{$key} = $value;
		}
		else if(is_array($arg) || is_object($arg))
		{
			$arr = (array)$arg;
			
			foreach($arr as $key => $value)
				$this->{$key} = $value;
		}
	}
	
	public function __get($name)
	{
		if($name == "isDefault")
			return empty($this->url) || $this->url == preg_replace("/^http(s?):|\?.+$/", "", Marker::DEFAULT_ICON);
	}
	
	public function jsonSerialize()
	{
		return array(
			'url'		=> $this->url,
			'retina'	=> $this->retina
		);
	}
	
	public function __toString()
	{
		return $this->url;
	}
}