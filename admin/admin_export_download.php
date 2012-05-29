<?php
require_once(dirname(__FILE__).'/../init.php');
ini_set('display_errors', 1);

$aa_inst_id=$_GET['aa_inst_id'];

//Register Admin

//Export of the User Data
$exporter = new iCon_Export();

//add admin log
$admin=getModule("app_log")->getTable("admin");

if(isset($session->fb) && isset($session->fb['fb_user_id']))
{
   $fb_user_id=$session->fb['fb_user_id'];
}
else
{
   $fb_user_id=0;
}


$data=array(
   'fb_user_id'=>$fb_user_id,
   'aa_inst_id'=>$aa_inst_id,
   'action'=>'export',
   'ip'=>getClientIp(),
   'timestamp'=>date("Y-m-d H:i:s"),

);

$admin->add($data);



//get data
// Get participants
$rows=app_export_list($aa_inst_id);
$arrData=array();

$arrTitle = array(
   //0=>__t("FB User Id"), 
   1 =>__t("First name"), 
   2 =>__t("Last name"),
   3 =>__t("Email-address"), 
   4 => __t("Gender"), 
   5 => 'Timestamp',
   6 => 'IP',
   9 => __t("Sound Url"), 
);


if(isset($rows) && count($rows) != "0")
{
   //do not export fb_user_id column
   foreach($rows as $k=>$v)
   {
   $arrData[]=array(
      $v['first_name'],
      $v['last_name'],
      $v['email'],
      $v['gender'],
      $v['timestamp'],
      $v['ip_address'],
      $v['sound_url'],
   
   );
   }

   $exporter->arrayToCsv($arrData, $arrTitle);
}
else
{
   __p("During this time nobody participated.");

}
?>
