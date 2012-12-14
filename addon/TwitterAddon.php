<?php
class TwitterAddon {
	/**
	 * Start the job
	 * @param mixed $args
	 */
	public function onJob($args) {
		include('../classes/Social/hybridauth/Hybrid/Auth.php');
		$current_user_id = 1;
		// create an instance for Hybridauth with the configuration file path as parameter
		$config = new Config();
		$hybridauth = new Hybrid_Auth( $config );	
		
		$hybridauth_session_data = $adapter->get_sotred_hybridauth_session( $current_user_id );

			
		// get the stored hybridauth data from your storage system

		// then call Hybrid_Auth::restoreSessionData() to get stored data
		if($hybridauth_session_data){
			
			$data = $hybridauth->restoreSessionData( $hybridauth_session_data['hybridauth_session'] );
			// call back an instance of Twitter adapter

				$twitter = $hybridauth->getAdapter( "Twitter" );		

				$indexValues = $adapter->readPageIndexData($config->hybridAuth);
	
				$twitter->setUserStatus("Hi all, today we generated ". $indexValues['summary']['totalEnergyToday'][0]['KWH']." kWh. Check it out on: http://bit.ly/QV9DxJ. Grtz! Power by #SunCounter.nl" );
				$twitter->logout();
				$data['message']='Tweet send';
				$data['tweetSend']=1;


		}
		
	}
	
}