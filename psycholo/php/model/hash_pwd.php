<?php
	class hash_pwd
	{
		public $pwd;
		public $verify_pwd;
		public $db_pwd;
		
		public function pwd_encrypt($pass)
		{
			$pwd = $pass;
			$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_RAND)), '+', '.');
			$salt_sha512 = '$6$rounds=5000$'.$salt.'$';
			$sha512md = crypt($pwd, $salt_sha512);
			return $sha512md;
		}
		
		public function pwd_verify($db_pass,$pass)
		{
			$verify_hash = crypt($pass, $db_pass);
			if($verify_hash==$db_pass)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
?>