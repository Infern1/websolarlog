<?php
class TwitterAddon {
	/**
	 * Start the job
	 * @param mixed $args
	 */

	function __construct() {
		
		include_once('../classes/Social/hybridauth/Hybrid/Auth.php');
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
		$this->type = 'Twitter';
		$this->current_user_id = 1;
		$this->hybridauth_session_data = $this->adapter->get_hybridauth_session( $this->current_user_id, $this->type);
	}

	function __destruct() {
		$this->config = null;
		$this->adapter = null;
	}


	function attachTwitter(){
		HookHandler::getInstance()->fire("onDebug", 'Fire(AttachTwitter)');
		if($this->hybridauth_session_data){
			$data['message'] = 'Connected';
		}else{
			
			$twitter = $this->hybridauth->authenticate( "Twitter" );
			$hybridauth_session_data = $this->hybridauth->getSessionData();
	
			$twitter_user_profile = $twitter->getUserProfile();
			$this->adapter->save_hybridauth_session( $this->current_user_id, $hybridauth_session_data,$twitter_user_profile, $this->type );
			$hybridauth_session_data = $this->adapter->get_hybridauth_session( $this->current_user_id,$this->type );
	
			if($hybridauth_session_data){
				$data['message'] = 'Connected';
			}
			$twitter->logout();
		}
	}


	function detachTwitter(){
		HookHandler::getInstance()->fire("onDebug", 'Fire(DetachTwitter)');


			
			$detached = $this->adapter->remove_hybridauth_session(1,'twitter');

			if($detached){
				$data['message'] = 'Detached';
			}
			$twitter->logout();
	}
	
	function sendTweet(){
		$config = Session::getConfig();
		HookHandler::getInstance()->fire("onDebug", 'Fire(sendTwitter)');
		if($this->hybridauth_session_data){
			HookHandler::getInstance()->fire("onDebug", 'Found session data, lets try to Tweet');
			$data = $this->hybridauth->restoreSessionData( $this->hybridauth_session_data['hybridauth_session'] );
			try{
				$twitter = $this->hybridauth->getAdapter( "Twitter" );
				$indexValues = $this->adapter->readPageIndexData();
				$url = Common::getShortUrl($config->url);
				$twitter->setUserStatus("Today we generated ". $indexValues['summary']['today']['todayAvgKwh-0']." kWh. Check out: " . $url . " #SunCounter" );
				$twitter->logout();
				$data['message']=var_dump($indexValues);
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