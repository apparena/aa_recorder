<?php
   $fb_user_id=$_GET['fb_user_id'];
   $aa_inst_id=$_GET['aa_inst_id'];

   if($aa_inst_id == false || $fb_user_id == false )
   {
      $msg=array('error'=>1,'error_msg'=>'invalid request');
      echo json_encode($msg);

      exit();
   }
   include_once( "init.php" );


   $filename=$fb_user_id."_".time();

   $aa_inst_id=$session->instance['aa_inst_id'];

   //init upload folder
   $upload_path ='/var/www/uploads/apps/instance/' . $aa_inst_id . '/user_upload';

   if (!file_exists($upload_path))
   {
      mkdir($upload_path, 0777);
   }

   $wav_path = $upload_path.'/'.$filename.'.wav';
   $mp3_path = $upload_path.'/'.$filename.'.mp3';

   $content = file_get_contents('php://input');
   $fh = fopen($wav_path, 'w') or die("can't open file");
   fwrite($fh, $content);
   fclose($fh);

   $wav_size=filesize($wav_path);
   //convert to mp3
   app_convert_wav_to_mp3($wav_path);


   $mp3_size=filesize($mp3_path);

   //sound url
   if($sound_file_size == false)
   {
      $sound_url='https://www.app-arena.com/uploads/apps/instance/' . $aa_inst_id .  '/user_upload/'.$filename.'.wav';
      $sound_file_size=$wav_size;
   }
   else
   {
      $sound_url='https://www.app-arena.com/uploads/apps/instance/' . $aa_inst_id . '/user_upload/'.$filename.'.mp3';
      $sound_file_size=$mp3_size;
   }

   // the user didnt tag the image yet, so save his tag
   $sql = "Update `tags` SET `sound_url` = '$sound_url' ,sound_file_size='$sound_file_size' WHERE `fb_user_id` = '" . $fb_user_id ."'";

   getDb()->query($sql);


   $msg=array('error'=>0);
   echo json_encode($msg);
   exit();


   function valid_wav_file($file) {
      $handle = fopen($file, 'r');
      $header = fread($handle, 4);
      list($chunk_size) = array_values(unpack('V', fread($handle, 4)));
      $format = fread($handle, 4);
      fclose($handle);
      return $header == 'RIFF' && $format == 'WAVE' && $chunk_size == (filesize($file) - 8);
   }


?>
