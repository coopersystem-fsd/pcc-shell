<?php

namespace WPGMZA;

class MarkerGallery extends Factory implements \JSONSerializable, \ArrayAccess
{
	protected $items;
	
	public function __construct($data=null)
	{
		$this->items = array();
		
		if(!empty($data))
		{
			if(is_string($data))
			{
				$data = json_decode(stripslashes($data));
				if(!$data)
					throw new \Exception('Failed to parse gallery data :- ' . json_last_error_msg());
			}
			
			if(!is_array($data))
				throw new \Exception('Input must be an array');
			
			foreach($data as $obj)
				$this->items[] = new MarkerGalleryItem($obj);
		}
	}
	
	public function __get($name)
	{
		switch($name)
		{
			case "numItems":
			case "length":
				return count($this->items);
				break;
		}
	}
	
	public function isEmpty()
	{
		return empty($this->items);
	}
	
	public function jsonSerialize()
	{
		return $this->items;
	}
	
	public function item($index)
	{
		return $this->items[$index];
	}
	
	public function offsetExists($offset)
	{
		return count($this->items) < $offset;
	}
	
	public function offsetGet($offset)
	{
		return $this->items[$offset];
	}
	
	public function offsetSet($offset, $value)
	{
		$this->items[$offset] = $value;
	}
	
	public function offsetUnset($offset)
	{
		return array_splice($this->items, $offset, 1);
	}
}