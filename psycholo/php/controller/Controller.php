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
				case "psycholo_login":
					$result = $this->model->handle_login($table_name[0],$router["data"]);
					break;
				case "check_login":
					$result = $this->model->check_login();
					break;
				case "logon";
					$result = $this->model->handle_logon();
					break;
				case "comment_parent":
					$result = $this->model->get_comment($table_name[0]);
					break;
				case "psycholo_update":
					$result = $this->model->update_pwd($table_name[0],$router["data"]);
					break;
				case "report_list_get":
					$result = $this->model->get_report_list($table_name[0]);
					break;
				case "report_list_available":
					$result = $this->model->get_report_available($table_name[0]);
					break;
				default:
					$result = array("status"=> "terminated", "response"=> "router-error");
			}
			
			return json_encode($result);
		}
	}
?>
