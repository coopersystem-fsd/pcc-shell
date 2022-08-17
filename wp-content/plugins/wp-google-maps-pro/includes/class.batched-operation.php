<?php

namespace WPGMZA;

abstract class BatchedOperation extends Crud
{
	const STATE_INITIAL			= "initial";
	const STATE_RUNNING			= "running";
	const STATE_WORKING			= "working";
	const STATE_HALTED			= "halted";
	const STATE_COMPLETE		= "complete";
	
	public function __construct($id=-1)
	{
		Crud::__construct(static::getTableName(), $id);
	}
	
	public static function runAll()
	{
		global $wpdb;
		
		$subclasses = BatchedOperation::getSubclasses();
		
		foreach($subclasses as $class)
		{
			$tableName = $class::getTableName();
			
			// TODO: Query state here
			$stmt = $wpdb->prepare("SELECT id 
				FROM $tableName 
				WHERE state = 'running' 
				AND next_run <= NOW() 
				AND class=%s", 
				array($class)
			);
			$ids = $wpdb->get_col($stmt);
			
			// TODO: Maybe try / catch here and halt the job on exception?
			foreach($ids as $id)
			{
				$instance = new $class($id);
				
				try{
					$instance->iterate();
				}catch(\Exception $e) {
					$instance->halt($e->getMessage());
					break;
				}
				
				$stmt = $wpdb->prepare("UPDATE $tableName SET next_run = DATE_ADD(NOW(), INTERVAL %d SECOND) WHERE id = %d", array($instance->seconds, $instance->id));
				$wpdb->query($stmt);
			}
		}
	}
	
	protected function get_arbitrary_data_column_name()
	{
		return "data";
	}
	
	static protected function getSubclasses()
	{
		$result = array();
	
		foreach (get_declared_classes() as $class) {
			if (is_subclass_of($class, __CLASS__))
				$result[] = $class;
		}
		
		return $result;
	}
	
	abstract static protected function getTableName();
	abstract protected function getTotalSteps();
	
	protected function log($message)
	{
		$this->output .= date('Y-m-d H:i:s') . " :- " . $message . "\r\n";
	}
	
	protected function create()
	{
		Crud::create();
		
		$this->class = get_called_class();
		$this->state = BatchedOperation::STATE_INITIAL;
	}
	
	public function start()
	{
		$this->state = BatchedOperation::STATE_RUNNING;
		
		$this->playhead = 0;
		$this->steps = $this->getTotalSteps();
	}
	
	protected function halt($reason)
	{
		$this->log("Halted - $reason");
		$this->state = BatchedOperation::STATE_HALTED;
	}
	
	abstract protected function work($playhead);
	
	protected function iterate()
	{
		$this->state = BatchedOperation::STATE_WORKING;
		
		for($i = 0; $i < $this->iterations; $i++)
		{
			$this->work($this->playhead);
			
			if(++$this->playhead >= $this->steps)
			{
				$this->complete();
				return;
			}
		}
		
		$this->state = BatchedOperation::STATE_RUNNING;
	}
	
	private function complete()
	{
		$this->state = BatchedOperation::STATE_COMPLETE;
		
		$this->onComplete();
	}
	
	protected function onComplete()
	{
		
	}
}

add_action('init', function() {
	
	BatchedOperation::runAll();
	
}, 100);
