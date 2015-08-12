<?php
/**
 * 
 *
 */
class CLoginSystem extends CUserInput  {
 
    #protected $db  = null;
    public $errors = array();
    public $count;
    public $username;
    public $password;




  /*
   * validate_Input, Check the incomming parameters for errors.
   * @param, $username, check the username
   * @param, $password, -||-
   *
   * @return, table list of all errors.
   * else, login user.
   */
public function validate_Input($username, $password){

      if (isset($username, $password)) {
          
          if (empty($username)) {
            $this->errors[] = "The username cannot be empty.";
          }
          
          if (empty($password)) {
            $this->errors[] = "The password cannot be empty.";
          }

         if ($this->user_exists($username)) {
            $this->errors[] = "Finns inget sådant användarnamn.";
          }

         if ($this->check_activation($username)) {
            $this->errors[] = "Inte aktiverat kontot. Gå till din mail, asap.";
          }



        if (empty($this->errors)) {
            $this->login($username, $password);
            }

        if (empty($this->errors) === false) {
          #print_error, inherit from BaseClass, UserInput
          $this->print_error($this->errors);
        }
    }

}

  /*
   * validate_Input, Check the incomming parameters for errors.
   * @param, $user, select username
   * @param, $password, -||-
   *
   * @return, table list of all errors.
   * else, login user.
   */
  public function login($user, $password){    
              
                  $sql = " SELECT id, acronym, name, password, salt, activation, status 
                            FROM USER 
                            WHERE acronym = :username";

                    $params = array(':username' => $user);
                    $res = $this->db->ExecuteSelectQueryAndFetchAll($sql, $params);
                    
                      if(isset($res)) { 
                          $this->check_password($res, $password);
                        } 
                      else{
                          return $this->status =  "Uhm. Nått stämmer inte, databaseConnection FAIL";
                        }
  }


  /*
   * validate_Input, Check the incomming parameters for errors.
   * @param, $user, select username
   *
   * @return, true, if user doesnt exsist
   * else, false, user exsist.
   */
public function check_activation($username){
           $user = htmlentities($username);
           $sql = "SELECT COUNT(id) AS id FROM USER WHERE acronym = '$user' AND status ='1' ";
           $res = $this->db->ExecuteSelectQueryAndFetchAll($sql);

           $count = ceil($res[0]->id);

              if ($count == "0") {
                    return true;
                  }
              else{
                    return false;
              }
}

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

                if ($count == "0") {
                      return true;
                    }
                else{
                      return false;
                }
    } 


  /************************************************************************** 
   * check_password the hash and compares to old and new 
   * @param -$dbQuery, database query
   * @param -$password, user inuput password
   * @return bool, true = logged in, false = not logged in
   */
  public function check_password($dbQuery,$password){
            
            //Loop out from database.
              foreach ($dbQuery as $value) {
                $p = $value->password;
                $t = $value->salt;
                # code...
              }
                // Using the password submitted by the user and the salt stored in the database, 
                // we now check to see whether the passwords match by hashing the submitted password 
                // and comparing it to the hashed version already stored in the database. 
                $check_password = hash('sha256', $password. $t);

                    for($round = 0; $round < 4866 ; $round++) 
                    { 
                        $check_password2 = hash('sha256', $check_password . $t); 
                    } 

                    //Check match
              if($check_password2 === $p) 
                  { 
                    //Set session function
                    $_SESSION['user'] = $dbQuery[0]->acronym; 
                    #$this->set_user_session = $res[0]->acronym; 
                    header('Location: admin.php');
                      #return true; 
                  } 

              else{
                return $this->status ="Fel lösenord";
              }
  }


  private function set_user_session($username){    
    #return isset($_SESSION['user']) ? 
      #"{$_SESSION['user']->acronym} ({$_SESSION['user']->name})" : null;
      $_SESSION['user'] = $username;
  }
    private function getUserSession(){    
      return $this->setUserSession;
  }



}
?>
