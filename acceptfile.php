<?php

   var_dump($_POST);exit();


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



   /*
   $fp = fopen($upload_path."/".$filename.".wav", "wb");
   fwrite($fp, file_get_contents('php://input'));
   fclose($fp);
   */
   /************/
   $key = 'filename';
   $tmp_name = $_FILES["upload_file"]["tmp_name"][$key];
   $upload_name = $_FILES["upload_file"]["name"][$key];
   $type = $_FILES["upload_file"]["type"][$key];

   $filename = $upload_path.'/'.$filename.'.wav';

   $saved = 0;
   if($type == 'audio/x-wav' && preg_match('/^[a-zA-Z0-9_\-]+\.wav$/', $upload_name) && valid_wav_file($tmp_name)) {
      $saved = move_uploaded_file($tmp_name, $filename) ? 1 : 0;
   }

   app_convert_wav_to_mp3($filename);

   if($_POST['format'] == 'json') {
      header('Content-type: application/json');
      print "{\"saved\":$saved}";
   } else {
      print $saved ? "Saved" : 'Not saved';
   }



   /****************/

   //convert to mp3
   //app_convert_wav_to_mp3($upload_path.'/'.$filename.'.wav');

   $connection = mysql_connect( $database_host, $database_user, $database_pass );

   if ( !$connection ) {

      die( 'sql connection failed: ' . mysql_error() );

   }
   mysql_select_db($database_name);

   $sound_file_size=filesize($upload_path.'/'.$filename.'.mp3');

   //sound url
   $sound_url='http://www.app-arena.com/uploads/apps/instance/' . $session->instance['aa_inst_id'] . '/user_upload/'.$filename.'.mp3';

   // the user didnt tag the image yet, so save his tag
   $saveSql = "Update `tags` SET `sound_url` = '$sound_url' ,sound_file_size='$sound_file_size' WHERE `fb_user_id` = '" . $fb_user_id ."'";
   //SET `sound_url` = '1'";
   //echo ($saveSql);
   $saveResult = mysql_query( $saveSql ,$connection);

   exit('done');

function valid_wav_file($file) {
  $handle = fopen($file, 'r');
  $header = fread($handle, 4);
  list($chunk_size) = array_values(unpack('V', fread($handle, 4)));
  $format = fread($handle, 4);
  fclose($handle);
  return $header == 'RIFF' && $format == 'WAVE' && $chunk_size == (filesize($file) - 8);
}


?>
