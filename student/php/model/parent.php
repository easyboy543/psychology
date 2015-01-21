<?php
	//驗證登入
	session_start();
	class parents
	{
		public $account;
		public $pwd;
		
		public function is_login()
		{
			if(!empty($_SESSION["parents_account"]) && !empty($_SESSION["parents_pwd"]) && !empty($_SESSION["parents_name"]))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		public function give_session($acc,$pwd,$name)
		{
			session_regenerate_id();
			if(!empty($_SESSION["parents_account"]))
			{
				unset($_SESSION["parents_account"]);
			}
			if(!empty($_SESSION["parents_pwd"]))
			{
				unset($_SESSION["parents_pwd"]);
			}
			if(!empty($_SESSION["parents_name"]))
			{
				unset($_SESSION["parents_name"]);
			}
			
			$_SESSION["parents_account"] = $acc;
			$_SESSION["parents_pwd"] = $pwd;
			$_SESSION["parents_name"] = $name;
		}
		
		public function give_fb_session($session)
		{
			if($session!=null)
			{
				$_SESSION['fb_token'] = $session->getToken();
				// create a session using saved token or the new one we generated at login
				$session = new FacebookSession( $session->getToken() );
				return true;
			}
			else
			{
				return false;
			}
			/*
			// graph api request for user data
			$request = new FacebookRequest( $session, 'GET', '/me' );
			$response = $request->execute();
			// get response
			//$graphObject = $response->getGraphObject()->asArray();
			print_r($response);*/
		}
		
		public function get_session()
		{
			return $_SESSION["parents_account"];
		}
		
		public function update_session($new_pwd)
		{
			if(!empty($_SESSION["parents_pwd"]))
			{
				$_SESSION["parents_pwd"] = $new_pwd;
			}
		}
		
		public function unset_session()
		{
			if(!empty($_SESSION["parents_account"]))
			{
				unset($_SESSION["parents_account"]);
			}
			if(!empty($_SESSION["parents_pwd"]))
			{
				unset($_SESSION["parents_pwd"]);
			}
			if(!empty($_SESSION["parents_name"]))
			{
				unset($_SESSION["parents_name"]);
			}
		}
	}
?>
