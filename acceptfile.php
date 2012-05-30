<?php
   if(!isset($_REQUEST['filename']))
   {
     exit('No file');
   }
		

$filename=$_REQUEST['filename'];

list($fb_user_id,$aa_inst_id)=explode("_",$filename);
$filename=$fb_user_id."_".time();

if(!isset($_GET['aa_inst_id']))
{
$_GET['aa_inst_id']=$aa_inst_id;

}
include_once( "init.php" );

   
   if (!file_exists('/var/www/uploads/apps/instance/' . $session->instance['aa_inst_id'] . '/user_upload/')){
   mkdir('/var/www/uploads/apps/instance/' . $session->instance['aa_inst_id'] . '/user_upload/', 0777);
   }
   
   $upload_path ='/var/www/uploads/apps/instance/' . $session->instance['aa_inst_id'] . '/user_upload/';

   
   
   $fp = fopen($upload_path."/".$filename.".wav", "wb");

   
   fwrite($fp, file_get_contents('php://input'));
   
   fclose($fp);

   //convert to mp3
   app_convert_wav_to_mp3($upload_path.'/'.$filename.'.wav');
   
   $connection = mysql_connect( $database_host, $database_user, $database_pass );
	
	if ( !$connection ) {
	
		die( 'sql connection failed: ' . mysql_error() );
		 
	}
mysql_select_db($database_name);

	//sound url
	$sound_url='http://www.app-arena.com/uploads/apps/instance/' . $session->instance['aa_inst_id'] . '/user_upload/'.$filename.'.mp3';
   
// the user didnt tag the image yet, so save his tag
	$saveSql = "Update `tags` SET `sound_url` = '$sound_url' WHERE `fb_user_id` = '" . $fb_user_id ."'";
				//SET `sound_url` = '1'";
	//echo ($saveSql);
	$saveResult = mysql_query( $saveSql ,$connection);
   
   exit('done');
   


?>
