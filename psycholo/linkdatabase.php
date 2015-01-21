<?php
$hostname='mysql.lionfree.net';
$database='u597449507_psy';
$username='u597449507_psy';
$password='psycsie';
$Link=mysql_connect($hostname,$username,$password);
if(!$Link){
	die('連線失敗，輸出錯誤訊息:'.mysql_error());
}
mysql_query('set name utf8');
echo'連線成功';
mysql_select_db($database,$Link);
mysql_close($Link);
?>