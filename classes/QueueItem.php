<?php
class QueueItem {
	/**
	 *	This is the time after which it is executed 
	 */
	public $time;
	
	/**
	 * The class and method to be executed example: Logger.debug() 
	 */
	public $classmethod;
	
	/**
	 * The arguments to be passed to the method: Logger.debug($arguments)
	 */
	public $arguments;

	/**
	 * Should this item be put on queue again
	 */
	public $requeue;
	
	/**
	 * The time the queue should wait before processed again
	 */
	public $requeueTime;
	
	public function __construct($time, $classmethod, $arguments = null, $requeue = false, $requeueTime = 3600) {
		$this->time = $time;
		$this->classmethod = $classmethod;
		$this->arguments = $arguments;
		$this->requeue = $requeue;
		$this->requeueTime;
	}
	
	/**
	 * Throws QueueItemValidationException if something is wrong
	 */
	public function validate() {
		
	}
	
	/**
	 * Calculate the next time.
	 * If the time is already past, calculate the first next time.
	 * @return time
	 */
	public function getNextTime() {
		$newTime = $this->time + $this->requeueTime;
		if (time() > $newTime) {
			$newTime = time();
			return $newTime + $this->requeueTime - ($newTime % $this->requeueTime);
		}
		return $newTime;
	}
}
?>