<?php
/**
 * 
 *
 */
class CRegistration extends CUserInput
{

	public $errors = array();
	public $emailValid;
	#public $activation;
	public $username;
	private $name ="Peter";
	public $email_bool = false;
	#public $userAgent = $_SERVER['HTTP_USER_AGENT'];
	#public $activationCode = md5(uniqid(rand(), true));
	

	/************************************************************************** 
	* user_exists, check if there is a username as the input.
	* @param - username input
	* @return - return true if exsist, else false om inte exist
	*/
	public function user_exists($username){
		$user = htmlentities($username);
		$sql = "SELECT COUNT(id) AS id FROM USER WHERE acronym = '$user' ";
		$res = $this->db->ExecuteSelectQueryAndFetchAll($sql);

		$count = ceil($res[0]->id);
		if ($count != "0") {
		    return true;
		  }
		else{
		    return false;
		}
	}		  





	/************************************************************************** 
	* user_exists, check if there is a username as the input.
	* @param - user email
	* @return - return true if exsist, else false om inte exist
	* Reminder: Tänk på SSX attacks. Bör förbättra. 
	* http://forums.devshed.com/php-faqs-stickies-167/program-basic-secure-login-system-using-php-mysql-891201.html
	*/
	public function add_user($user,$email, $pass){
		$tracking = new CTracking($this->db);
		  #$getIp = CTracking::getUserIP($this->db);
		//Get userinformation, user-agent and ip.
		$getIp = $tracking->getUserIP();
		//set username
		$this->username = $user;
		$this->emailValid = $email;

		$sql = " INSERT INTO USER ( 
		        acronym, 
		        email,
		        name, 
		        password, 
		        salt,
		        activation,
		        userAgent,
		        IP                 
		    ) VALUES ( 
		        :username, 
		        :email,
		        :name, 
		        :password, 
		        :salt,
		        :activation,
		        :userAgent,
		        :ip 
		    ) 
		";

		$salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));  
		#jag undrar faktiskt om jag verkligen är skyddad mot DaveGhrol!
		$password = hash('sha256', $pass . $salt); 

		for($round = 0; $round < 4689; $round++) { 
		            $passwordReal = hash('sha256', $password . $salt); 
		} 

		$params = array(            
			':username' => $this->username, 
			':email' => $this->emailValid,
			':name' => $this->name, 
			':password' => $passwordReal, 
			':salt' => $salt,
			':activation' => $this->activationCode,
			':ip' => $getIp,
			':userAgent' => $this->userAgent 
		);
		$res = $this->db->ExecuteQuery($sql, $params);

		if (isset($res)) {
		 	//Om databasen är påverkad, skicka mail till lagrad adress.
		 	$this->send_activation_mail();
		    //Save email to session
		    $_SESSION['email'] = $this->emailValid;
		 	//Head of to activatedAcc.php
			header('Location: activatedAcc.php');
		}

	}




	public function send_activation_mail(){
		#ini_set('SMTP', "send.one.com");
		ini_set('SMTP', "mail.myt.mu");

		// Overide The Default Php.ini settings for sending mail
		//This is the address that will appear coming from ( Sender )
		$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
		$website = 'http://'.parse_url($url, PHP_URL_HOST) . '/';

		$recipient= "{$this->emailValid}";
		$sender= "peter@one.com";
		$subject= "Activation ACC";
		$senderEmail= "peter@one.com";
		$message = " Ditt användarnamn är {$this->username}. \n\nTo activate your account, please click on this link:\n\n" . $website   . "webroot/activate.php?email=" . urlencode($this->emailValid) . "&key={$this->activationCode}";
		$mailBody="Name: $sender\n Email: $senderEmail\nSubject: $subject\n\nMeddelande:\n$message";

		mail($recipient, $subject, $mailBody, "From: $sender <$senderEmail>");  
		$msgSent ="<h2> Medellande är skickat till {$this->emailValid}. Bekräfta genom att klicka på den skickade länken. ";
	}



	public function valid_credentials($user, $pass){
		$user = htmlentities($user);
		$pass = md5($pass);
		$total = "SELECT COUNT ('id') FROM 'USER' WHERE 'acronym' = '{$user}' AND 'password' = '{$pass}'  ";
		$res = $this->db->ExecuteQuery($total);

		$count = ceil($res[0]->id);
		if ($count != "0") {
			return true;
		}
		else{
			return false;
		}
	}

	/************************************************************************** 
	* user_exists, check if there is a username as the input.
	* @param - user email
	* @return - return true if exsist, else false om inte exist
	*/
	public function validate_email($email){
		$regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/"; 

		if (!preg_match($regex, $email)) {
			return true;
		} 
		else { 
			return false;
		} 
	}



	public function validate_Input($username, $email, $password, $repeat_password){
	#$errors = array();
	$this->container =  null;

		if (isset($username, $email, $password, $repeat_password)) {
			if (empty($username)) {
				# code...
				$this->errors[] = "The username cannot be empty.";
			}

			if (empty($email)) {
				# code...
				$this->errors[] = "Email cannot be empty.";
			}

			if (empty($password) || empty($repeat_password) ) {
				# code...
				$this->errors[] = "The password cannot be empty.";
			}

			if ($password !== $repeat_password) {
				# code...
				$this->errors[] = "PASSWORD DONT MATCH";
			}

			if ($this->validate_email($email)) {
				# code...
				$this->errors[] = "Det är nått konstigt med din mail.";
			}

			if ($this->user_exists($username)) {
				# code...
				$this->errors[] = "Username already taken.";
			}

			if (empty($this->errors)) {
				$this->add_user($username, $email, $password);
			}

			if (empty($this->errors) === false) {
				$this->print_error($this->errors);
			}

		}
		
	}


}
