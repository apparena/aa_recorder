<?php
require('init.php');
/**
 * this file is work with facebook.js
 * for set/get fb data in session 
 */
if(count($_POST) > 0 && isset($_POST['action']) )
{

  $aa_inst_id=getrequest("aa_inst_id",false);
  $action=getrequest("action",false);

  if($aa_inst_id == false)
  {
    echo errorMsg("missing parameter aa_inst_id");
    exit();
  }

  if($action == false)
  {
    echo errorMsg("missing parameter action");
    exit();
  }

  if($_POST['action'] == 'saveuser')
  {
    $user=getrequest("fb_user",array());

    $fb=new Js_Facebook();
    $fb->saveUser($aa_inst_id,$user);

    //log auth
    $app_log=getModule("app_log")->getTable("user");
    $data=array(
       'aa_inst_id'=>$aa_inst_id,
       'fb_user_id'=>$user['id'],
       'action'=>'auth',
       'ip'=>getClientIp(),
       'timestamp'=>date("Y-m-d H:i:s"),
    );


    echo successMsg('',array('user'=>$user));
    exit();
  }
  else if($_POST['action'] == 'getuser')
  {
    $fb=new Js_Facebook();
    $user=$fb->getUser($aa_inst_id);

    echo successMsg('',array('user'=>$user));
    exit();
  }
  else
  {
    echo errorMsg("action $action not exists"); 
    exit();
  }

}

?>
