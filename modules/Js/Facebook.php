<?php
/**
 * facebook Js's facebook helper class
 */

class Js_Facebook
{
  /**
   *
   * save user info to session
   *
   */
  function saveUser($aa_inst_id,$user)
  {
    $session_key=generateSessionKey($aa_inst_id);
    $session=new Zend_Session_Namespace($session_key);
    $session->facebook=array();

    $session->facebook['user']=$user;

    $this->saveToDatabase($user);
    return true;
  }

  /**
   * get user info from session
   *
   */
  function getuser($aa_inst_id)
  {
    $session_key=generateSessionKey($aa_inst_id);
    $session=new Zend_Session_Namespace($session_key);

    if(!isset($session->facebook) || !isset($session->facebook['user']) )
      $user=false;
    else
      $user=$session->facebook['user'];

    return $user;
  }

  /**
   * save to table  user_data
   */
  function saveToDatabase($user)
  {

    if(!is_array($user) || !isset($user['id']) || $user['id'] == false)
      return false;

    $uid=$user['id'];

    $table=new Frd_Db_Table("user_data","fb_user_id");
    $table->load($uid);

    $table->fb_user_id=$uid;

    $firstname=getValue($user,"first_name",false);
    if($firstname != false)
      $table->first_name=$firstname;

    $middlename=getValue($user,"middle_name",false);
    if($middlename != false)
      $table->middle_name=$middlename;

    $lastname=getValue($user,"last_name",false);
    if($lastname != false)
      $table->last_name=$lastname;

    $link=getValue($user,"link",false);
    if($link != false)
      $table->link=$link;

    $gender=getValue($user,"gender",false);
    if($gender != false)
      $table->gender=$gender;

    $email=getValue($user,"email",false);
    if($email != false)
      $table->email=$email;

    $locale=getValue($user,"locale",false);
    if($locale != false)
      $table->location=$locale;

    $table->save();
  }
}
