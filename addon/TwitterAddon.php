<?php
class TwitterAddon {
	/**
	 * Start the job
	 * @param mixed $args
	 */

	function __construct() {
		
		include('../classes/Social/hybridauth/Hybrid/Auth.php');
		$this->config = Session::getConfig();
		
		$url = Util::getCurrentUrl();
		$url = str_replace("admin/admin-server.php","",$url);
		
		$this->configHybridAuth = array(
				// "base_url" the url that point to HybridAuth Endpoint (where the index.php and config.php are found)
				"base_url" => $url."classes/Social/hybridauth/",
				"providers" => array (

						"Twitter" => array (
								"enabled" => true,
								"keys"    => array ( "key" => "idYGAJncvuakWv0P0HVp7Q", "secret" => "qJilSF1fmxTZOI7M8ixqWfmAPXDYDLwSCPDWfpE0" )
						)
				)
		);
		$this->adapter = PDODataAdapter::getInstance();
		
		$this->hybridauth = new Hybrid_Auth( $this->configHybridAuth );

		$this->current_user_id = 1;
		$this->hybridauth_session_data = $this->adapter->get_sotred_hybridauth_session( $this->current_user_id );
	}

	function __destruct() {
		$this->config = null;
		$this->adapter = null;
	}
	
	
	function attachTwitter(){
		HookHandler::getInstance()->fire("onError", 'Fire(AttachTwitter)');
		if($this->hybridauth_session_data){
			$data['message'] = 'Connected';
		}else{

			$twitter = $this->hybridauth->authenticate( "Twitter" );
			$hybridauth_session_data = $this->hybridauth->getSessionData();

			$twitter_user_profile = $twitter->getUserProfile();
			$this->adapter->sotre_hybridauth_session( $this->current_user_id, $hybridauth_session_data,$twitter_user_profile );
			$hybridauth_session_data = $this->adapter->get_sotred_hybridauth_session( $this->current_user_id );

			if($hybridauth_session_data){
				$data['message'] = 'Connected';
			}
			$twitter->logout();
		}
	}

	function sendTweet(){
		$config = Session::getConfig();
		HookHandler::getInstance()->fire("onDebug", 'Fire(sendTwitter)');
		if($this->hybridauth_session_data){
			HookHandler::getInstance()->fire("onDebug", 'Found session data, lets try to Tweet');
			$data = $this->hybridauth->restoreSessionData( $this->hybridauth_session_data['hybridauth_session'] );
			try{
				$twitter = $this->hybridauth->getAdapter( "Twitter" );
				$indexValues = $this->adapter->readPageIndexData($this->config->hybridAuth);
				$url = Common::getShortUrl($config->url);
				$twitter->setUserStatus("Today we generated ". $indexValues['summary']['totalEnergyToday'][0]['KWH']." kWh. Check out: " . $url . " #SunCounter.nl" );
				$twitter->logout();
				$data['message']='Tweet send';
				$data['tweetSend']=1;
				HookHandler::getInstance()->fire("onDebug", 'It looks like we Tweeted for you :D');
			}
			catch( Exception $e ){
				HookHandler::getInstance()->fire("onError", $e->getMessage());
			}
		}else{
			$data['tweetSend']=0;
			$data['message']= "No credentials available, so Twitter doesn't no how you are and you may not Tweet :| ";
		}
	}
}