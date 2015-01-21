<?php
	//驗證登入
	session_start();
	class student
	{
		public $account;
		public $pwd;
		
		public function is_login()
		{
			if(!empty($_SESSION["student_account"]) && !empty($_SESSION["student_pwd"]) && !empty($_SESSION["student_name"]))
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
			if(!empty($_SESSION["student_account"]))
			{
				unset($_SESSION["student_account"]);
			}
			if(!empty($_SESSION["student_pwd"]))
			{
				unset($_SESSION["student_pwd"]);
			}
			if(!empty($_SESSION["student_name"]))
			{
				unset($_SESSION["student_name"]);
			}
			
			$_SESSION["student_account"] = $acc;
			$_SESSION["student_pwd"] = $pwd;
			$_SESSION["student_name"] = $name;
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
			return $_SESSION["student_account"];
		}
		
		public function update_session($new_pwd)
		{
			if(!empty($_SESSION["student_pwd"]))
			{
				$_SESSION["student_pwd"] = $new_pwd;
			}
		}
		
		public function unset_session()
		{
			if(!empty($_SESSION["student_account"]))
			{
				unset($_SESSION["student_account"]);
			}
			if(!empty($_SESSION["student_pwd"]))
			{
				unset($_SESSION["student_pwd"]);
			}
			if(!empty($_SESSION["student_name"]))
			{
				unset($_SESSION["student_name"]);
			}
		}
	}
?>