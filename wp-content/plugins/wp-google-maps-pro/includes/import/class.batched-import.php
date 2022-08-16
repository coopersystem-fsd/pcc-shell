<?php

namespace WPGMZA;

abstract class BatchedImport extends BatchedOperation
{
	protected $data;
	
	public function __construct($id=-1)
	{
		BatchedOperation::__construct($id);
	}
	
	static protected function getTableName()
	{
		global $WPGMZA_TABLE_NAME_BATCHED_IMPORTS;
		return $WPGMZA_TABLE_NAME_BATCHED_IMPORTS;
	}
	
	protected function geocode()
	{
		
	}
	
	protected function load()
	{
		
	}
	
	protected function iterate()
	{
		$this->load();
		
		BatchedOperation::iterate();
	}
	
	public function start()
	{
		// TODO: Store hashed filename in uploads directory for URL's, convert here
		BatchedOperation::start();
		
		if(!is_file($this->source))
		{
			$this->halt("{$this->source} is not a file");
			return;
		}
	}
}
