<?php
/**
 * has current fb user saved an record
 */
function app_has_recorded($aa_inst_id,$fb_user_id)
{
	if($aa_inst_id <= 0 || $fb_user_id <= 0)
	{
		return false;
	}

$db=getDb();

$select=$db->select();
$select->from("tags","id");
$select->where("aa_inst_id=?",$aa_inst_id);
$select->where("fb_user_id=?",$fb_user_id);


	$ret=$db->fetchRow($select);


	return $ret;

}

/**
*get recorded sound list
*/
function app_record_list($aa_inst_id)
{
//	$table=new Frd_Db_Table("tags","id");
	//$rows=$table->getAll(array('aa_inst_id'=>$aa_inst_id),"timestamp desc" );
$db=getDb();

$select=$db->select();
$select->from("tags","*");
$select->where("aa_inst_id=?",$aa_inst_id);


$rows=$db->fetchAll($select);

	return (array) $rows;
}

/**
*convert wav file to mp3
*/
function app_convert_wav_to_mp3($path)
{

	if(file_exists($path) == false)
	{
		return false;
	}

	$new_path=dirname($path).'/'.str_replace("wav","mp3",basename($path));

	$cmd="ffmpeg -i $path $new_path" ;

//echo $cmd;
exec($cmd);

	return $new_path;
}


