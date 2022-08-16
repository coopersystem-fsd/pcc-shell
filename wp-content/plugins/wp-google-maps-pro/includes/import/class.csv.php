<?php

namespace WPGMZA\Import;

class CSV extends \WPGMZA\BatchedImport
{
	public function __construct($id=-1)
	{
		\WPGMZA\BatchedImport::__construct($id);
		
		// TODO: Temporary code, remove this
		$this->source = ABSPATH . 'big.csv';
		$this->iterations = 10;
		$this->seconds = 2;
	}

	protected function getTotalSteps()
	{
		$rows = 0;
		
		$fp = fopen($this->source, "r");
		
		if(!$fp)
			throw new \Exception('Invalid file');
		
		while(($record = fgetcsv($fp)) !== false)
			$rows++;
		
		return $rows;
	}
	
	protected function work($playhead)
	{
		$fp = fopen($this->source, "r");
		
		if(!$fp)
			throw new \Exception('Invalid file');
		
		$pointer = 0;
		
		while($pointer < $playhead)
		{
			fgetcsv($fp);
			$pointer++;
		}
		
		$row = fgetcsv($fp, 65536);
		
		$this->log("Read " . print_r($row, true));
	}
}
