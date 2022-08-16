<?php

namespace WPGMZA\Import;

class JSON extends \WPGMZA\BatchedImport
{
	private $linear;
	
	public function __construct($id=-1)
	{
		\WPGMZA\BatchedImport::__construct($id);
		
		// TODO: Temporary code, remove this
		$this->source = ABSPATH . 'big.json';
		$this->iterations = 10;
		$this->seconds = 2;
	}
	
	protected function load()
	{
		$data = file_get_contents($this->source);
		$json = json_decode($data);
		
		if(!$json)
			throw new \Exception('Invalid JSON');
		
		$this->data = $json;
		
		$this->linear = array();
		$this->linearize($json);
	}
	
	private function linearize($obj, $parent=null)
	{
		$this->log("Linearising " . substr(json_encode($obj), 0, 100));
		
		if(is_scalar($obj))
			return;
		
		/*$this->linear[] = (object)array(
			'node'		=> $obj,
			'parent'	=> $parent
		);*/
		
		foreach($obj as $key => $value)
		{
			$this->log($key);
			
			if(is_scalar($value))
				continue;
			
			$this->linearize($obj->{$key}, $obj);
		}
	}
	
	protected function getTotalSteps()
	{
		$this->load();
		return count($this->linear);
	}
	
	protected function work($playhead)
	{
		$node = $this->linear[$playhead];
		
		$str = substr(json_encode($node), 0, 100) . "...";
		
		$this->log("Looking at $str");
	}
}
