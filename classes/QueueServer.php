<?php
class QueueServer {
	private static $instance;
	
	// Singleton
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new QueueServer();
		}
		return self::$instance;
	}
	
	// Class
	private $queue;

	function __construct() {
		// Initialize objects
		$this->queue = array();
	}
	
	function __destruct() {
		// Release objects
		$this->sync();
		$this->queue = null;
	}
	
	public function start() {
		$syncOffset = 30; // seconds
		$maxQueueItemsAtOnce = 5;
		$this->sync();
		
		while (true) {
			$syncOffsetTime = time();
			$itemsAtOnceCounter = 0;
			while (time() - $syncOffsetTime < $syncOffset) {
				$nextItem = $this->next();
				// Do we have an item to process
				if ($nextItem != null) {
					$this->process($nextItem);
					if ($itemsAtOnceCounter > $maxQueueItemsAtOnce - 1) {
						sleep(2); // Sleep for two second
						$itemsAtOnceCounter = 0;
					}
				} else {
					sleep(2); // Sleep for two second
					$itemsAtOnceCounter = 0;
				}
				$itemsAtOnceCounter++;
			}
			// Sync as we have reached the offset
			$memory = memory_get_usage() / 1024 / 1024; // calculate mb
			$memory_string = number_format($memory, 2); 
			
			// Print memory info when debug is enabled
			HookHandler::getInstance()->fire("onDebug", " QueueServer: current memory usage = " . $memory_string . "mb queue size: " . count($this->queue));
			$this->sync();
		}
	}
	
	public function add(QueueItem $item) {
		$item->validate(); // Validate before adding
		$this->queue[] = $item;
	}
	
	/**
	 * Checks if there is an item in the queue needed to be processed, returns the item and removes it from the queue
	 * @return Null, QueueItem
	 */
	private function next() {
		$currentTime = time();
		
		foreach ($this->queue as $key => $queueItem) {
			if ($queueItem->time <= $currentTime) {
				unset($this->queue[$key]);
				return $queueItem;
			} 
		}
		return null;
	}
	
	
	// Retrieve new items from the queue
	private function sync() {
		$currentQueueIds = $this->getDbQueueItemIds();
		
		$beans = R::findAll("QueueItem", "1 order by time");
		foreach (beans as $bean) {
			if (!in_array($bean->id, $ids)) {
				$arguments = null;
				// Create new queue Item 
				$newQueueItem = new QueueItem($bean->time, $bean->method, $arguments, true, $device->refreshTime, true);
				$newQueueItem->dbId = $bean->id;
				
				// Add the new item to the queue
				QueueServer::getInstance()->add($newQueueItem);
			}
		}
	}
	
	private function getDbQueueItemIds () {
		$ids = array();
		foreach ($this->queue as $key => $queueItem) {
			if ($queueItem->dbSync && $queueItem->dbId > 0) {
				$ids[] = $queueItem->dbId;
			}
		}
		return $ids;
	}
	
	private function removeDbQueueItem(QueueItem $item) {
		// Check if it is already in the database
		if ($item->dbId > 0) {
			$dbQueueItem = R::load("QueueItem", $item->dbId);
			if ($dbQueueItem) {		
				R::trash($item);
			}
		}
	}
	
	private function updateDbQueueItem(QueueItem $item) {
		$dbItem = null;
		if ($item->dbId > 0) {
			$dbItem = R::load("QueueItem", $item->dbId);
		}
		if ($dbItem == null) {
			$dbItem = R::dispense("QueueItem");
		}
		$dbItem->time = $item->time;
		$dbItem->classmethod = $item->classmethod;
		$dbItem->arguments = $item->arguments;
		$dbItem->requeue = $item->requeue;
		$dbItem->requeueTime = $item->requeueTime;
		$dbItem->dbSync = $item->dbSync;
		
		$item->dbId = R::store($dbItem);
		
		return $item;
	}
	
	// Process the job 
	private function process(QueueItem $item) {
		try {
			R::begin(); // Every queuItem has its own database transaction
			$classmethod = explode (".",$item->classmethod);
			$classname = $classmethod[0];
			$methodname = $classmethod[1];
			
			$parameters = array();
			$parameters[] = $item;
			if (is_array($item->arguments)) {
				$parameters = array_merge($parameters, $item->arguments);
			} elseif (trim($item->arguments)) {
				$parameters = array_merge($parameters, array($item->arguments));
			}
	
			if ($classname == "HookHandler") {
				$object = HookHandler::getInstance();
			} else {
				$object = new $classname();
			}
			$object->$methodname($parameters);
			
			// do we need to requeue this item?
			if ($item->requeue) {
				$item->time = $item->getNextTime();
				// Save the new version to the database
				if ($item->dbSync) {
					$item = $this->updateDbQueueItem($item);
				}				
				$this->add($item);
				//echo ("Requeue item: " . $item->classmethod . " time: " . date("ymd h:i:s", $item->time) . "\n");
			}
			
			// Remove from database if needed
			if ($item->dbSync && $item->requeue == false) {
				$this->removeDbQueueItem($item);
			}
			
			R::commit(); // Commit the transaction
		} catch (Exception $e) {
			R::rollback();
			HookHandler::getInstance()->fire("onError", "QueueItem $item->classmethod had an error: " . $e->getMessage());
		}
	}
}
?>