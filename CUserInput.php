<?php
/**
 * 
 *
 */
abstract class CUserInput{

	protected $db  = null;
	protected $a = array();
	protected $user;
	public $status;
	public $userAgent;
	public $activationCode;
	#public $age;

	//CLogin, CRegistration must check if user does exist in database
	abstract public function user_exists($user);
	#abstract public function password_length($user);

	public function __construct($database) {
		$this->db = $database;
		$this->status;
		$this->activationCode = md5(uniqid(rand(), true));
		$this->userAgent = $_SERVER['HTTP_USER_AGENT'];
		//Track userinformation.
		$this->checkLoggedIn($this->isAuthenticated());
	}

	/*
	* print_error, Loop out array of errors from validation in classes, CLogin, CRegistration
	* @param, $a, array of errors
	* @return, table list of all errors.
	*/
	protected function print_error($a) {
		foreach ($a as $val) {
			$this->status .= "<li>
			                 	$val
			              	  </li>";
		}
		return $this->status;
	}

	protected function isAuthenticated(){    
		return isset($_SESSION['user']) ? true : false;
	}

	/************************************************************************** 
	* isAuthenticated, checks if the user is logged in or not 
	* @param -$user, Check if session is active
	* @return bool, if logged in already or not
	*/
	protected function checkLoggedIn($user){    
	  if($user) {
	    header('Location: admin.php');
	  }
	  else {
	    return false;
	  }
	}
	

}
