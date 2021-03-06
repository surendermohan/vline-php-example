<?php
class DbHandler{
	private $mysqli;
	
	function connect($pre = ''){
		$r = fopen($pre.'conf.json', 'r');
		$contents = fread($r, filesize($pre.'conf.json'));
		$conf = json_decode($contents);	
		$mysqli = new mysqli($conf->host, $conf->username, $conf->password, 'thatsapp');
		if (mysqli_connect_error()){
			return false;
		}
		else{
			$this->mysqli = $mysqli;
			return true;	
		}
	}
	
	function getUsers(){
		$query = "select * from `user`";
		return $this->mysqli->query($query);
	}
	
	function authAdmin($data){
		$query = "select * from `user` where `username` = '".$this->mysqli->real_escape_string($data['username'])."' and `password` = '".$this->mysqli->real_escape_string($data['password'])."' and `isadmin` = '1'";
		$result = $this->mysqli->query($query);
		if($result->num_rows == 0){
			$_SESSION['authed'] = 0;
			$_SESSION['plainuserauth'] = 0;
			return false;
		}
		else{
			$_SESSION['authed'] = 1;
			$_SESSION['plainuserauth'] = 1;
			$therow = $result->fetch_array(MYSQLI_ASSOC);
			$_SESSION['user'] = $therow;
			return true;
		}	
	}
	
	function authUser($data){
		if($this->authAdmin($data)){
			return true;	
		}
		else{
			// Compare password with last 4 characters of the username for now
			$password = substr($data['username'], -4); // $this->mysqli->real_escape_string($data['password']);
			$username = $this->mysqli->real_escape_string($data['country-code'].$data['username']);
			$query = "select * from `user` where `username` = '".$username."' and `password` = '".$password."'";
			$result = $this->mysqli->query($query);
			if($result->num_rows == 0){
				//$_SESSION['plainuserauth'] = 0;
				//return false;
				// Add new user with blank name. Ask for user's name on first login. Later ask for confirmation code
				// TODO - this addUser could fail.
				$result = $this->addUser(array('name' => '', 
				'username' => $username,
				'password' => $password));
				$result = $this->mysqli->query($query);
			}
			$therow = $result->fetch_array(MYSQLI_ASSOC);
			$_SESSION['user'] = $therow;
			if (strlen($therow['name']) > 0) {
				$_SESSION['plainuserauth'] = '1';
			} else {
				$_SESSION['plainuserauth'] = '2'; //new user
			}
			return true;
		}
	}
	
	function addUser($data){
		$query = "insert into `user` set 
			`name` = '".$this->mysqli->real_escape_string($data['name'])."',
			`username` = '".$this->mysqli->real_escape_string($data['username'])."',
			`password` = '".$this->mysqli->real_escape_string($data['password'])."' ";
		$this->mysqli->query($query);
	}
	
	function checkUsername($data){
		$query = "select * from `user` where `username` = '".$this->mysqli->real_escape_string($data['username'])."' and `id` != ".$data['exclude'];
		$result = $this->mysqli->query($query);
		if($result->num_rows == 0){
			return true;
		}
		else{	
			return false;
		}
	}
	
	function getUser($id){
		$query = "select * from `user` where `id` = '".$id."'";
		$result = $this->mysqli->query($query);
		if($result->num_rows == 0){
			return false;	
		}
		else{
			return mysqli_fetch_array($result, MYSQLI_ASSOC);	
		}
	}
	
	function saveUser($data){
		$query = "update `user` set 
			`name` = '".$this->mysqli->real_escape_string($data['name'])."',
			`username` = '".$this->mysqli->real_escape_string($data['username'])."',
			`password` = '".$this->mysqli->real_escape_string($data['password'])."'
			where `id` = '".$data['id']."'";
		$this->mysqli->query($query);
	}
	
	function deleteUser($id){
		$query = "delete from `user` where `id` = '".$id."'";
		$this->mysqli->query($query);
	}
}
?>