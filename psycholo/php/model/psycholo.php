<?php
	//驗證登入
	session_start();
	class psycholo
	{
		public function give_session($account)
		{
			if(empty($_SESSION["psycholo"]))
			{
				$_SESSION["psycholo"] = $account;
			}
		}
		
		public function delete_session()
		{
			if(!empty($_SESSION["psycholo"]))
			{
				unset($_SESSION["psycholo"]);
			}
		}
		
		public function get_session()
		{
			if(!empty($_SESSION["psycholo"]))
				return $_SESSION["psycholo"];
			else
				return false;
		}
		
		public function is_login()
		{
			if(!empty($_SESSION["psycholo"]))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		public function update_session($pwd)
		{
			if(!empty($_SESSION["psycholo"]))
			{
				$_SESSION["psycholo"] = $pwd;
			}
			
		}
	}
?>
