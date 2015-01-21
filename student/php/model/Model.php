<?php
	include_once("student.php");
	include_once("parent.php");
	include_once("hash_pwd.php");
	include_once("../db_connect.php");
	class Model
	{
		public $link;
		public $res_login;
		
		public function getTeacher()
		{
			$link = $this->link_db();
			if($link==null)
			{
				return "cannot link DB.";
			}
			else
			{
				$sql = "CALL getTeacher()";
				$result = $link -> query($sql);
				
				$psy_len = 0;
				$psy = array();
				while($row = $result -> fetch())
				{
					$psy[$psy_len]["name"] = $row["name"];
					$psy[$psy_len]["phone"] = $row["phone"];
					$psy[$psy_len]["img"] = $row["img"];
					$psy[$psy_len]["account"] = $row["account"];
					$psy_len++;
				}
				
				$link = null;
				return $psy;
			}
			
		}
		
		public function check_login()
		{
			$res_login = new student();
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
		public function parent_check_login()
		{
			$res_login = new parents();
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
				$sql = "SELECT name,account,pwd FROM ".$table_name." WHERE account = :account";
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
					if(count($row)!=3)
					{
						$response["result"] = "verify-fail";
					}
					else
					{
						$verify = $hash_pwd -> pwd_verify($row["pwd"],$pwd);
						$is_active = $row["active"];
						
						if($verify)
						{
							$give_session = new student();
							$give_session -> give_session($row["account"],$row["pwd"],$row["name"]);
							$response["result"] = "verify-success";
						}
						else if(!$verify)
						{
							$response["result"] = "verify-fail";
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
				$student = new student();
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
		
		public function report_handler($table_name,$data)
		{
			date_default_timezone_set("Asia/Taipei");
			$link = $this -> link_db();
			$report_name = $data[0]["report_name"];
			$select_event = $data[0]["select_event"];
			$response = array();
			if($link==null)
			{
				return "cannot link db.";
			}
			else
			{
				$get_session = new student();
				$account = $get_session -> get_session();
				$check_lie = $this -> report_lie($account);
				if($check_lie)
				{
					$link -> query("SET NAMES utf8");
					$sql = "SELECT finish_date FROM report WHERE notified_account = :notified_account";
					$stmt = $link -> prepare($sql);
					
					$is_handle = true;
					$stmt -> execute(array(":notified_account"=>$report_name));
					while($arr = $stmt -> fetch(PDO::FETCH_ASSOC))
					{
						if($arr["finish_date"]=="0000-00-00")
						{
							$is_handle = false;
							break;
						}
					}
					
					if($is_handle)
					{
						$sql = "INSERT INTO ".$table_name."(notify_account,notified_account,reason,notify_date,account,accept_date,finish_date,state) VALUES(:notify_account,:notified_account,:reason,:notify_date,:account,:accept_date,:finish_date,:state)";
						$stmt = $link -> prepare($sql);
						$res = $stmt -> execute(array(":notify_account"=>$account, ":notified_account"=>$report_name, ":reason"=>$select_event, ":notify_date"=>date("Y-m-d"), ":account"=>"NULL", ":accept_date"=>"NULL", ":finish_date"=>"NULL", ":state"=>"0"));
					
						if($res)
							$response["result"] = "report_success";
						else
							$response["result"] = $report_name.",".$select_event;
					}		
					else
					{
						$response["result"] = "not-finish";
					}
				}
				else
				{
					$response["result"] = "lie_times_limit";
				}
				
				$link = null;
			}
			
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
		
		public function get_student_info($table_name)
		{
			$response = array();
			$link = $this -> link_db();
			$student = new student();
			$account = $student -> get_session();
			$sql = "SELECT * FROM ".$table_name." WHERE account = :account";
			$stmt = $link -> prepare($sql);
			$stmt -> execute(array(":account"=>$account));
			$res = $stmt -> fetch(PDO::FETCH_ASSOC);
			if(count($res)==6)
			{
				$response["pwd"] = $res["pwd"];
				$response["verify"] = $res["verify"];
				$response["active"] = $res["active"];
				$response["lie_times"] = $res["lie_times"];
				$response["result"] = "select-success";
			}
			else
			{
				$response["result"] = "select-error";
			}
			$link = null;
			return $response;
		}
		
		public function filter_handler($data)
		{
			chdir("/home/u597449507/public_html/psychology/json");
			$response = array();
			$json_str = file_get_contents("student_name.json");
			$json_arr = json_decode($json_str, true);
			$json_len = count($json_arr);
			$count = 0;
			$res_len = 0;
			for(;$count<$json_len;$count++)
			{
				if(stristr($json_arr[$count]["account"], $data))
				{
					$response[$res_len]["account"] = $json_arr[$count]["account"];
					$res_len++;
				}
			}
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
		
		public function getName()
		{
			if(!file_exists("student_name.json"))
			{
				$link = $this -> link_db();
				if($link==null)
				{
					return "cannot link db.";
				}
				else
				{
					$sql = "CALL getName()";
					$result = $link -> query($sql);
					$name_arr = array();
					$name_len = 0;
					
					while($row = $result -> fetch())
					{
						$name_arr[$name_len]["name"] = $row["name"];
						$getStuNum = explode("@", $row["account"]);
						$name_arr[$name_len]["account"] = substr($getStuNum[0],1);
						$name_len++;
					}
					$link = null;
					file_put_contents("/home/u597449507/public_html/psychology/json/student_name.json", json_encode($name_arr));
				}
			}
			
			return "student_name.json";
		}
		
		public function handle_logon()
		{
			$unset_session = new student();
			$unset_session -> unset_session();
			return true;
		}
		public function parent_handle_logon()
		{
			$unset_session = new parents();
			$unset_session -> unset_session();
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
