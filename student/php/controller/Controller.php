<?php
	include_once("../model/Model.php");
	class Controller
	{
		public $model;
		public $router;
		public function __construct()
		{
			$this->model = new Model();
		}
		
		public function invoke($act)
		{
			$router = $act;
			//傳回Model結果
			$result = null;
			$table_name = explode("_", $router["action"]);;
			
			switch($router["action"])
			{
				case "getTeacher":
					$result = $this->model->getTeacher();
					break;
				case "check_login":
					$result = $this->model->check_login();
					break;
				case "student_login":
					$result = $this->model->handle_login($table_name[0],$router["data"]);
					break;
				case "logon":
					$result = $this->model->handle_logon();
					break;
				case "parent_logon":
					$result = $this->model->parent_handle_logon();
					break;	
				case "student_getName":
					$result = $this->model->getName();
					break;
				case "report_handle":
					$result = $this->model->report_handler($table_name[0],$router["data"]);
					break;
				case "filter_student_number":
					$result = $this->model->filter_handler($router["data"]);
					break;
				case "report_list":
					$result = $this->model->get_list($table_name[0]);
					break;
				case "student_info":
					$result = $this->model->get_student_info($table_name[0]);
					break;
				case "student_update":
					$result = $this->model->update_pwd($table_name[0],$router["data"]);
					break;
				case "parent_update":
					$result = $this->model->update_pwd($table_name[0],$router["data"]);
					break;
				case "parent_login":
					$result = $this->model->handle_login($table_name[0],$router["data"]);
					break;
				case "parent_check_login":
					$result = $this->model->parent_check_loginn();
					break;
				default:
					$result = array("status"=> "terminated", "response"=> "router-error");
			}
			
			return json_encode($result);
		}
	}
?>
