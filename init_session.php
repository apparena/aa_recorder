<?php
	
	/********************************************************************
	 * This init is for the template files and where it might be needed *
	 * after the index.php has used the init.php for initializing the   *
	 * session.                                                         *
	 * This only gets the session where the aa-contents were previously *
	 * stored in the init.php.                                          *
	 * NOTE that the aa_inst_id is needed here as a GET parameter!      *
	 ********************************************************************/
	
   if(isset($_GET['aa_inst_id']))
   {
      $aa_inst_id=$_GET['aa_inst_id'];
   }
   else if(isset($_POST['aa_inst_id']))
   {
      $aa_inst_id=$_POST['aa_inst_id'];
   }
   else {

      die( "invalid session! exiting..." );
      exit( -1 );

   }
	
/*
 * Initial process to start the app
 */
date_default_timezone_set('Europe/Berlin'); // Load config values
ini_set('session.gc_probability',0); //disable session expired check
header('P3P: CP=CAO PSA OUR'); //fix ie can not save cookie in iframe problem
define("ROOT_PATH",realpath(dirname(__FILE__))); //set include path
require_once ROOT_PATH.'/libs/Frd/Frd.php';
set_include_path(ROOT_PATH.'/libs/' . PATH_SEPARATOR );

/**** init ***/
$config=array(
   'timezone'=>'Europe/Berlin',
   'root_path'=>ROOT_PATH,
   'include_paths'=>array(
      ROOT_PATH.'/libs',
      ROOT_PATH.'/modules',
   ),
   'module_path'=>ROOT_PATH.'/modules',
);
Frd::init($config);

//start session
Zend_Session::start();

/**** config and init other resource ***/
//necessary files
require_once ROOT_PATH.'/config.php';
if(file_exists(ROOT_PATH.'/config_local.php'))
{
   require_once ROOT_PATH.'/config_local.php';
}
require_once ROOT_PATH.'/app_functions.php';
require_once ROOT_PATH.'/libs/AA/functions.php';
require_once ROOT_PATH.'/libs/fb-php-sdk/src/facebook.php';
setConfig($config_data);

//set db
addDb(array(
   'adapter'=>'MYSQLI',
   'host'=>getConfig("database_host"),
   'username'=>getConfig("database_user"),
   'password'=>getConfig("database_pass"),
   'dbname'=>getConfig("database_name")
));

getDb()->query("set time_zone='+2:00'");

// Initialize App-Manager connection

if(isset($_GET['aa_inst_id']))
{
 $aa_inst_id=$_GET['aa_inst_id'];
}
else
{
 $aa_inst_id=false;
}
$aa = new AA_AppManager(array(
			'aa_app_id'  => getConfig("aa_app_id"),
			'aa_app_secret' => getConfig("aa_app_secret"),
			'aa_inst_id' => $aa_inst_id,
			));

$aa->setServerUrl('http://dev.app-arena.com/manager/server/soap4.php' );

$aa_instance = $aa->getInstance();
setGlobal('aa_inst_id',$aa_instance['aa_inst_id']);

// Build up information array about facebook
$session->fb = array();
$fb_signed_request = parse_signed_request(getRequest('signed_request'));
$fb_is_admin = is_admin();
$fb_is_fan = is_fan();
$fb_data = array("is_admin" => $fb_is_admin,
				"is_fan" => $fb_is_fan,
				"app_data" => json_decode(urldecode(get_app_data()),true),
				"signed_request" => $fb_signed_request
				);
if (isset($fb_signed_request['page']['id'])){
	$fb_data['fb_page_id'] = $fb_signed_request['page']['id'];
}
if (isset($fb_signed_request['user_id'])){
	$fb_data['fb_user_id'] = $fb_signed_request['user_id'];
}
else
{
	$fb_data['fb_user_id'] = false;
}

					
$current_app = array();

$session = new Zend_Session_Namespace( 'aa_' . $aa_instance['aa_inst_id'] );
$session->config = $aa->getConfig();
$session->instance = $aa_instance;
// Add translation management
$locale=$session->instance['aa_inst_locale'];
$translation=$aa->getTranslation($locale);

if($translation == false)
{
	//just make sure the translation do not trigger error
	$translation=array( '__'=>'__');
}
$translate = new Zend_Translate('array',$translation,$locale); 

$global->translate=$translate;

foreach($fb_data as $k=>$v)
{
   $session->fb[$k] = $v;
}

if(!isset($session->app))
{
   $session->app = $current_app;
}

//echo "aa:".$session->instance["aa_inst_id"];


//Zend_Debug::dump( $session->instance );

