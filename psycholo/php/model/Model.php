<?php
	include_once("psycholo.php");
	include_once("hash_pwd.php");
	include_once("../db_connect.php");
	class Model
	{
		public $link;
		public $res_login;
		
		public function check_login()
		{
			$res_login = new psycholo();
			$response = array();
			if(!$res_login->is_login())
			{
				$response["result"] = "no_login";
			}
			else
			{
				$response["result"] = "is_login";
			}
			return $response;
		}
		
		public function handle_login($table_name,$data)
		{
			$account = $data[0]["user-acc"];
			$pwd = $data[0]["user-pwd"];
			$captcha = $data[0]["captcha"];
			if(empty($account) || empty($pwd) || empty($captcha))
			{
				$response["result"] = "post-error";
			}
			else
			{
				$sql = "SELECT account,pwd FROM ".$table_name." WHERE account = :account";
				$response = array();
			
				$link = $this->link_db();
				if($link==null)
				{
					$response["result"] = "cannot link DB.";
				}
				else
				{
					$hash_pwd = new hash_pwd();
					$stmt = $link -> prepare($sql);
					$stmt -> execute(array(":account"=>$account));
					$row = array();
				
					$row = $stmt -> fetch(PDO::FETCH_ASSOC);
					if(count($row)!=2)
					{
						$response["result"] = "verify-fail";
					}
					else
					{
						$verify = $hash_pwd -> pwd_verify($row["pwd"],$pwd);
						$is_active = $row["active"];
						
						if($verify)
						{
							$give_session = new psycholo();
							$give_session -> give_session($row["account"]);
							$response["result"] = "verify-success";
						}
						else if(!$verify)
						{
							$response["result"] = "verify-fail";
							//$response["result"] = $give_session -> give_session($row["account"]);
						}
						else if((int)$is_active==0)
						{
							$response["result"] = "not-active";
							//$response["result"] = $result["account"].$result["pwd"].$result["name"];
						}
						else
						{
							$response["result"] = "verify-fail";
						}
					}
				
					$link = null;
				}
			}
			
			return $response;
		}
		
		public function update_pwd($table_name,$data)
		{
			$response = array();
			$link = $this -> link_db();
			$pwd1 = $data[0]["update-pwd"];
			$pwd2 = $data[0]["update-pwd2"];
			if(strlen($pwd1)<8)
			{
				$response["result"] = "pwd-short-len";
			}
			else if($pwd1==$pwd2)
			{
				$sql = "SELECT pwd FROM ".$table_name." WHERE account = :account";
				$student = new psycholo();
				$account = $student -> get_session();
				$stmt = $link -> prepare($sql);
				$stmt -> execute(array(":account"=>$account));
				$res = $stmt -> fetch(PDO::FETCH_ASSOC);
				if(count($res)==1)
				{
					$pwd_en = new hash_pwd();
					if($pwd_en->pwd_verify($res["pwd"], $pwd1))
					{
						$response["result"] = "pwd-not-change";
					}
					else
					{
						$sql = "UPDATE ".$table_name." SET pwd = :pwd WHERE account = :account";
						$stmt = $link -> prepare($sql);
						$update_res = $stmt -> execute(array(":pwd"=>$pwd_en->pwd_encrypt($pwd1), ":account"=>$account));
						if($update_res)
						{
							$student->update_session($pwd_en->pwd_encrypt($pwd1));
							$response["result"] = "update-pwd-success";
						}
						else
							$response["result"] = "update-pwd-fail";
					}
				}
				else
				{
					$response["result"] = "select-error";
				}
			}
			else
			{
				$response["result"] = "pwd-diff";
			}
			$link = null;
			return $response;
		}
		
		public function get_report_list($table_name)
		{
			$response = array();
			$psy = new psycholo();
			$get_session = $psy->get_session();
			$link = $this->link_db();
			$sql = "SELECT * FROM report WHERE ".$table_name." WHERE account = :account";
			$stmt = $link -> prepare($sql);
			$stmt->execute(array(":account"=>$get_session));
			$len = 0;
			$row = array();
			
			while($res = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$row[$len]["accept_date"] = $res["accept_date"];
				$row[$len]["finish_date"] = $res["finish_date"];
				$row[$len]["notify_date"] = $res["notify_date"];
				$row[$len]["notify_account"] = $res["notify_account"];
				$row[$len]["notified_account"] = $res["notified_account"];
				$row[$len]["reason"] = $res["reason"];
				$len++;
			}
			
			if(count($row)==0)
			{
				$response["result"] = "沒有輔導過學生！";
			}
			else
			{
				$response["result"] = $row;
			}
			$link = null;
			return $response;
		}
		
		public function get_list($table_name)
		{
			$response = array();
			$link = $this->link_db();
			$stu = new student();
			$account = $stu -> get_session();
			$sql = "SELECT * FROM ".$table_name." WHERE notify_account = :notify_account";
			$link -> query("SET NAMES utf8");
			$stmt = $link -> prepare($sql);
			$stmt -> execute(array(":notify_account"=>$account));
			$res_len = 0;
			while($res = $stmt -> fetch(PDO::FETCH_ASSOC))
			{
				$response[$res_len]["notified_account"] = $res["notified_account"];
				$response[$res_len]["reason"] = $res["reason"];
				$response[$res_len]["notify_date"] = $res["notify_date"];
				$response[$res_len]["account"] = $res["account"];
				$response[$res_len]["accept_date"] = $res["accept_date"];
				$response[$res_len]["finish_date"] = $res["finish_date"];
				$response[$res_len]["state"] = $res["state"];
				$res_len++;
			}
			$link = null;
			return $response;
		}
		
		public function get_report_available($table_name)
		{
			$link = $this->link_db();
			$result = $link -> query("SELECT * FROM ".$table_name." WHERE state = '0'");
			$row = array();
			$len = 0;
			while($res = $result->fetch(PDO::FETCH_ASSOC))
			{
				if($row[$len]["accept_date"]=="0000-00-00")
					$row[$len]["accept_date"] = "未知";
				else
					$row[$len]["accept_date"] = $res["accept_date"];
				if($row[$len]["finish_date"]=="0000-00-00")
					$row[$len]["finish_date"] = "未知";
				else
					$row[$len]["finish_date"] = $res["finish_date"];
				$row[$len]["notify_date"] = $res["notify_date"];
				$row[$len]["notify_account"] = $res["notify_account"];
				$row[$len]["notified_account"] = $res["notified_account"];
				$row[$len]["reason"] = $res["reason"];
				$len++;
			}
			if(count($row)==0)
				$response["result"] = "no-available";
			$response["result"] = $row;
			$link = null;
			return $response;
		}
		
		public function get_comment($table_name)
		{
			$link = $this->link_db();
			$get_session = new psycholo();
			if(!$get_session->get_session())
				$response["result"] = "session-error";
			else
			{			
				$sql = "SELECT parent.name,comment.contents,comment.msg_date FROM comment,parent WHERE comment.account = parent.account AND msg_person = :account";
				$stmt = $link -> prepare($sql);
				$stmt -> execute(array(":account"=>$get_session->get_session()));
				$row = array();
				$len = 0;
				while($res = $stmt->fetch(PDO::FETCH_ASSOC))
				{
					$row[$len]["parent.name"] = $res["parent.name"];
					$row[$len]["comment.contents"] = $res["comment.contents"];
					$row[$len]["comment.msg_date"] = $res["comment.msg_date"];
					$len++;
				}
				
				if(count($row)==0)
				{
					$response["result"] = "no-comment";
				}
				else
				{
					$response["result"] = $row;
				}
			}	
			$link = null;
			return $response;
		}
		
		private function report_lie($account)
		{
			$link = $this -> link_db();
			$sql = "SELECT lie_times FROM student WHERE account = :acc";
			$stmt = $link -> prepare($sql);
			$row = $stmt -> execute(array(":acc"=>$account));
			$row = array();
			$row = $stmt -> fetch(PDO::FETCH_ASSOC);
			if($row["lie_times"]>=3)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		
		public function handle_logon()
		{
			$unset_session = new psycholo();
			$unset_session -> delete_session();
			return true;
		}
		
		private function link_db()
		{
			$link_db = null;
			try
			{
				$link_db = new PDO(host, user_name, user_pwd);
			}
			catch(PDOEcxception $e)
			{
				$link_db = null;
			}
			
			return $link_db;
		}
	}
?>
