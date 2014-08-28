<?php
class AdminMessageService {
	public static $tbl = "adminMessage";
	
	function __construct() {
		HookHandler::getInstance()->add("onJanitorDbCheck", "AdminMessageService.janitorDbCheck");
	}
	
	/**
	 * Save the object to the database
	 * @param Live $object
	 * @return Live
	 */
        
        public function save(AdminMessage $object) {
            $bObject = ($object->id > 0) ? R::load(self::$tbl, $object->id) : R::dispense(self::$tbl);
            $bObject = $this->toBean($object, $bObject);
            $object->id = R::store($bObject);
            return $object;
	}
        
	public function saveNewAdminMessage($message) {
                // see if this message ID already exists in this WSL database
		
            $bObject = R::findOne(self::$tbl, ' mId = :mId', array(':mId'=>$message->mId));
           
            if(!$bObject){
                $bObject = R::dispense(self::$tbl);
                // message id doesn't exists, so dispense an bean!
                $object = $this->toObject($message);
                $object->active = 1;
                // only save if its not in the DB
                $this->save($object);
            }

            return $object;
	}

	/**
	 * Load an object from the database
	 * @param int $id
	 * @return Live
	 */
	public function load($id) {
		$bObject = R::load(self::$tbl, $id);
		if ($bObject->id > 0) {
			$object = $this->toObject($bObject);
		}
		return isset($object) ? $object : new AdminMessage();
	}
        
        
        public function hideAdminMessage($id){
            if($id){
                $bObject = $this->load($id);
                $bObject->active = 0;
                $this->save($bObject);
            }
        }

	/**
	 * Retrieve object for an device
	 * @param Device $device
	 * @return Live
	 */
	public function getAdminMessages($active=1) {
		$messages = R::find(self::$tbl, 'time >= :timeHalfYearAgo and time <= :time and active = :active ORDER BY time DESC',array(':timeHalfYearAgo'=>(time()-15552000),':time'=>time(),':active'=>$active));
                foreach($messages as $message){
                    $newMessages[] = $this->toObject($message);
                }
		return $newMessages;
   	}

        public function getAllAdminMessages() {
		$messages = R::findAll(self::$tbl, ' ORDER BY time DESC');
                foreach($messages as $message){
                    $message = $this->toObject($message);
                    $message->halfYearAgo = (time()-15552000);
                    $newMessages[] = $message;
                }
		return $newMessages;
	}
	


	public function janitorDbCheck() {
		HookHandler::getInstance()->fire("onDebug", "AdminMessageService janitor DB Check");
		R::wipe(self::$tbl);
	}
	
	private function toBean($object, $bObject) {
                $bObject->mId = $object->mId;
		$bObject->message = $object->message;
                $bObject->title = $object->title;
		$bObject->time = $object->time;
                $bObject->active = $object->active;
		return $bObject;
	}
	
	private function toObject($bObject) {
		$object = new AdminMessage();
		if (!isset($bObject)) {
			return $object;
		}
		$object->id = $bObject->id;
                $object->mId = $bObject->mId;
		$object->message = $bObject->message;
		$object->title = $bObject->title;
		$object->time = $bObject->time;
                $object->active = $bObject->active;
		return $object;
	}
}
?>