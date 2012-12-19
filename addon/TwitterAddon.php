<?php
class TwitterAddon {
	/**
	 * Start the job
	 * @param mixed $args
	 */
	
	function __construct() {
		$this->adapter = PDODataAdapter::getInstance();
		$this->config = Session::getConfig();
	}
	
	function __destruct() {
		$config = null;
		$adapter = null;
	}
	
	public function Tweet($args) {
		include('../classes/Social/hybridauth/Hybrid/Auth.php');
		$current_user_id = 1;
		// create an instance for Hybridauth with the configuration file path as parameter
		$hybridauth = new Hybrid_Auth( $this->config->hybridAuth );

		$hybridauth_session_data = $this->adapter->get_sotred_hybridauth_session( $current_user_id );

			
		// get the stored hybridauth data from your storage system

		// then call Hybrid_Auth::restoreSessionData() to get stored data
		if($hybridauth_session_data){
			
			$data = $hybridauth->restoreSessionData( $hybridauth_session_data['hybridauth_session'] );
			// call back an instance of Twitter adapter

				$twitter = $hybridauth->getAdapter( "Twitter" );		

				$indexValues = $adapter->readPageIndexData($this->config->hybridAuth);
	
				$twitter->setUserStatus("Hi all, today we generated ". $indexValues['summary']['totalEnergyToday'][0]['KWH']." kWh. Check it out on: http://bit.ly/QV9DxJ. Grtz! Power by #SunCounter.nl" );
				$twitter->logout();
				$data['message']='Tweet send';
				$data['tweetSend']=1;


		}
		
	}
	
}