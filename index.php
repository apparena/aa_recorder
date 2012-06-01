<?php 
	// init app-arena once, use init_session.php later...
 	include_once( "init.php" );
	$session->app['fb_share_url'] = "https://apps.facebook.com/" . $session->instance['fb_app_url']."/fb_share.php?aa_inst_id=".$session->instance['aa_inst_id'];

	$aa_inst_id=$session->instance['aa_inst_id'];
?>
<?php if(app_has_recorded($session->instance['aa_inst_id'],$session->fb['fb_user_id']) == true): ?>
<?php $has_recorded=true; ?>
<?php else: ?>
<?php $has_recorded=false; ?>
<?php endif; ?>

<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
         
	<!-- Facebook Meta Data -->
    <meta property="fb:app_id" content="<?=$session->instance['fb_app_id']?>" />
    <meta property="og:title" content="<?=$session->config['title']['value'];?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?=$session->instance['fb_page_url']."?sk=app_".$session->instance['fb_app_id']?>" />
    <meta property="og:image" content="<?=$session->config['image']['value'];?>" />
    <meta property="og:site_name" content="<?=$session->config['title']['value'];?>" />
    <meta property="og:description" content=""/>

	<title></title>
	<meta name="description" content="">
	<meta name="author" content="iConsultants UG - www.app-arena.com">

	<meta name="viewport" content="width=device-width">
	
	<!-- Include bootstrap css files -->
	<style type="text/css">
		<?=$session->config['css_bootstrap']['value'];?>
		<?=$session->config['css']['value'];?>
	</style>

	<script src="js/libs/modernizr-2.5.2-respond-1.1.0.min.js"></script>

<?php if($has_recorded == false): ?>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
  <script type="text/javascript" src="js/wami/recorder.js"></script>

  <script>
     function recorder_init()
     {
           //init recorder
           Wami.setup("wami");
     }
     </script>

     <?php endif; ?>
</head>

<body>

	<!-- Here starts the header -->
	<!-- Prompt IE 6 users to install Chrome Frame. Remove this if you support IE 6.
	     chromium.org/developers/how-tos/chrome-frame-getting-started -->
	<!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
	
	<?php // Here you can integrate your fangate
	if ($session->fb['is_fan'] == false && $session->config['fangate_activated']['value']) { ?>
		<div class="page_non_fans_layer"> 
			<div class="img_non_fans">
				<img src="<?php echo $session->config['page_welcome_nonfans']['value']?>" />
				
			</div>
			<div id="non_fan_background">&nbsp;</div>
		</div>
	<?php } ?>
	
    <!--<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
        	<div class="container-fluid">
            	<nav>
					<ul class="nav">
						<li><a class="template-welcome"><?//=__p("Homepage");?></a></li>
						<li><a class="template-terms"><?//=__p("Terms & Conditions");?></a></li>
					</ul>
				</nav>
			</div>
		</div>
    </div>-->
	
	<!-- this is the div you can append info/alert/error messages to -->
	<div id="msg-container">
	<?php if($has_recorded == true): ?>
     <div class="alert alert-success span9">
     <?php __p('You already record a message'); ?>
     </div>
	<?php endif; ?>

	</div> 
	
	<div class="custom-header">
		<div class="row">
			<div class="span10">
				<?php echo $session->config['custom_header']['value']; ?>
			</div>
		</div>
	</div>
	
	<div id="main" class="container">
		<?php if ( $session->fb['is_fan'] == true || !$session->config['fangate_activated']['value'] ){ ?>
			<div id="header" class="row">
				<div class="span10">
					<div class="thumbnail">	
						<img id="header_img" src="<?=$session->config['image_header']['value']?>" />
						
					</div>
				</div>
			</div>
			
			<?php if ( $session->config['admin_audio_intro_activated']['value'] ){ ?>
				<div class="audio-introduction">
				<!-- Player -->
        <audio id="audio1" src="<?php echo $session->config['audio_intro_url']['value']; ?>" preload="auto" ></audio>
				</div>
			<?php } ?>
	
	
			<?php if($has_recorded == false): ?>

         <div id="wami"></div>

				<!-- Recorder -->
				<div class="row show-grid player-status-bar">
					<div class="span2 time-container">
						<?php __p("Time"); ?>: <span id="time">00:00</span>
					</div>
					<div class="span4 status-container">
						<?php __p("Status"); ?>: <span id="status"></span>
					</div>
					<div class="span4 level-container hide" id="levelbase" >
						<div id="levelbar"></div>  
					</div>
				</div>

				<div class="row show-grid player-control">
					<div class="span2 record-container" >
             <button class="btn btn-danger" id="record" value="Record">
                <i class="icon-volume-up icon-white"></i> 
                <span>
                <?php __p("Record"); ?>
                </span>
              </button>
            <!--
					</div>
					<div class="span2 stop-container">
             -->
             <button class="btn hide" id="stop" >
                <i class="icon-stop icon-black"></i>
                <span>
                <?php __p("Stop"); ?>
                <?php //__p('Stop & Saving'); ?>
                </span>
             </button>
					</div>
          <!--
					<div class="span2 save-container" id="img_tag">
						<a class="btn" id="send" value="Send Data">
							<i class="icon-upload icon-black"></i> <?php __p("Save"); ?>
						</a>
					</div>
          -->

				</div>
			<?php endif; ?>

			
          <div class="row">
             <div id="record_list" class="span10">

             </div>
          </div>

		<?php } ?>
	</div> <!-- #main -->
	
	<!-- fb comment -->
	<?php $comment_link= $session->app['fb_share_url'] ; ?>
	<div class="fb-comments" data-href="<?php echo $comment_link; ?>" data-num-posts="10" data-width="470"></div>

	<div class="custom-footer">
		<?php echo $session->config['custom_footer']['value']; ?>
	</div>
	 	
 	<?php // include the file for the loading screen
 	require_once( dirname(__FILE__).'/templates/loading_screen.phtml' );
 	?>
 	
 	<?php if ($session->config['admin_debug_mode']['value']):?>
		  <span class="btn" onclick='$("#_debug").toggle();'>Show debug info</span>
		  <div id="_debug" style="display:none;">
		   <h1>Debug information</h1>
		   <?php Zend_Debug::dump($session->app, "session->app");?>
		   <?php Zend_Debug::dump($session->fb, "session->fb");?>
		   <?php Zend_Debug::dump($session->instance, "session->instance");?>
		   <?php Zend_Debug::dump($session->config, "session->config");?>
		   <?php Zend_Debug::dump($_COOKIE, "_COOKIE");?>
		  </div>
	<?php endif;?>
 	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.1.min.js"><\/script>')</script>
	
	<!-- scripts concatenated and minified via ant build script-->
	<script src="js/bootstrap.min.js"></script>
	<script src="js/plugins.js?v2"></script>
	<script src="js/script.js?v9"></script>
	<script src="js/audiojs/audio.min.js?v7"> </script> 
	<script src="js/libs/aa.js?v5"></script>



	<!-- end scripts-->
	
	<!--<script>
		var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']]; // Change UA-XXXXX-X to be your site's ID
		(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.async=1;
		g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
		s.parentNode.insertBefore(g,s)}(document,'script'));
	</script>-->
	
	<!--[if lt IE 7 ]>
		<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
		<script>window.attachEvent("onload",function(){CFInstall.check({mode:"overlay"})})</script>
	<![endif]-->
	
	<div id="fb-root"></div>

  <!--
	<script src="js/jRecorder.js"> </script>
  -->
	<script type="text/javascript">
		/** Init AppManager vars for js */

		var save_tag_callback=false; //callback after save tag
		<?php
		if(isset($session->fb['fb_user_id']))
		{
			echo "var fb_user_id='".$session->fb['fb_user_id']."';";
			$fb_user_id=$session->fb['fb_user_id'];
		}
		else
		{
			echo "var fb_user_id='';";
      $fb_user_id=0;
		}
		?>
		//defined post wall variables
		var fb_share_title=  '<?=$session->config["fb_share_title"]["value"]?>';
		var fb_share_link=  'https://apps.facebook.com/<?=$session->instance["fb_app_url"].'/fb_share.php?aa_inst_id='.$session->instance['aa_inst_id'] ?>';
		var fb_share_subtitle= '<?=$session->config["fb_share_subtitle"]["value"]?>';
		var fb_share_desc =  '<?=$session->config["fb_share_desc"]["value"]?>';
    var fb_share_img='<?=$session->config["fb_share_img"]["value"]?>';
    var aa_inst_id    = '<?=$session->instance["aa_inst_id"]?>';		

    var fb_canvas_url = '<?=$session->instance["fb_canvas_url"]?>';

    var recorder_timer= false; //js recorder's timer


    var tag_image='';
		var userHasAuthorized = false;
		

    jQuery(document).ready(function() {

          userHasAuthorized = false;

          flush_record_list();

          //init audio 
          init_audio();

          jQuery('#record').click(function(){
                recorder_pre_start_record();
          });

          jQuery('#stop').click(function(){
                recorder_stop_record();
          });


          //defined translation
          try{
             //set translate
             var translation=<?php echo json_encode($global->translate->getMessages()); ?>;
             set_translation(translation);
          }
          catch(e)
          {
             //frd.alert(__('About us'));
          }

    });
	
		window.fbAsyncInit = function() {
			if( typeof( fb_app_id ) == "undefined" ) {
				fb_app_id = '<?=$session->instance["fb_app_id"]?>';				
			}
		
			FB.init({
		      appId      : fb_app_id, // App ID
		      status     : true, // check login status
		      cookie     : true, // enable cookies to allow the server to access the session
		      xfbml      : true, // parse XFBML
		      oauth		 : true
		    });
			
			FB.Canvas.setAutoGrow();
			//FB.Canvas.setSize({ width: 640, height: 1800 });
		    
		    // Additional initialization code here
			FB.getLoginStatus(function(response) {
		    	  if (response.status === 'connected') {
		    	    // the user is logged in and connected to your
		    	    // app, and response.authResponse supplies
		    	    // the users ID, a valid access token, a signed
		    	    // request, and the time the access token 
		    	    // and signed request each expire
		    	    fb_user_id   = response.authResponse.userID;
					fb_user_name = response.authResponse.userName;
		    	    fb_status = "connected";
					
		    	    var fb_accessToken = response.authResponse.accessToken;
		    	    userHasAuthorized = true;
	
		    	    // get user name
		    	    FB.api('/me', function(response) {
						fb_user_name = response.name;
			     	});
		    	  } else if (response.status === 'not_authorized') {
		    	    // the user is logged in to Facebook, 
		    	    //but not connected to the app
					//alert("not connected");
						fb_status = "not_authorized";
		    	  } else {
		    	    // the user isn't even logged in to Facebook.
		    		  fb_status = "not_logged_in";
		    	  }
			});
		};
		// Load the SDK Asynchronously
		(function(d){
			var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
			if (d.getElementById(id)) {return;}
			js = d.createElement('script'); js.id = id; js.async = true;
			js.src = "//connect.facebook.net/de_DE/all.js";
			ref.parentNode.insertBefore(js, ref);
		}(document));


    function recorder_pre_start_record()
    {
       recorder_init();
       timer=setInterval(recorder_start_record,500);
    }

    function recorder_start_record()
    {
       if(Wami && typeof Wami.show == 'function')
       {
          window.clearInterval(timer);
       }
       else
       {
          return false;
       }

       var settings = Wami.getSettings();
       if (settings.microphone.granted == false) 
       {
          //jQuery('#wami-modal').modal();
          modal( '<?php __p("Notice"); ?>', '<?php __p("please allowed to use the microphone"); ?>', false );

          Wami.show();
          return false;
       }


          //unable record

          if(fb_user_id == false)
          {
                FB.login(function(response) {
                      if (response.authResponse) {
                            FB.api('/me', function(response) {
                                  fb_user_id = response.id;
                                  fb_user_name = response.name;


                                  //
                                  Wami.startRecording("acceptfile.php?aa_inst_id="+aa_inst_id+"&fb_user_id="+fb_user_id,"recorder_start","recorder_finish","recorder_failed");

                                  $('#status').html('Aufnahme gestartet');

                                  jQuery("#record").hide();
                                  jQuery("#stop").show();

                                  //save fb user infromation
                                  var params=new Object();
                                  params['fb_user']=response;
                                  params['action']='saveuser';
                                  params['aa_inst_id']=aa_inst_id;

                                  jQuery.post('fb_session.php',params);
                            });
                      }
                      else
                      {
                            modal( 'Hinweis', 'Du musst die Abfrage zulassen um eine Aufnahme zu hinterlassen.', false );
                      }
                }, {scope: 'publish_actions'});
          }
          else
          {
                Wami.startRecording("acceptfile.php?aa_inst_id="+aa_inst_id+"&fb_user_id="+fb_user_id,"recorder_start","recorder_finish","recorder_failed");

                $('#status').html('Aufnahme gestartet');

                jQuery("#record").hide();
                jQuery("#stop").show();
          }
    }

    function recorder_stop_record()
    {
         $('#status').html('Aufnahme gestoppt');


         jQuery("#stop").attr('disabled',true);
         jQuery("#stop span").text('<?php __p("Saving"); ?>');

         //save 
         save_tag_callback=function(){

            Wami.stopRecording();
            Wami.stopPlaying();

         };

         saveTag(0,0);
    
    }


    function recorder_start()
    {
    }

    function recorder_finish()
    {
       flush_record_list();

       jQuery("#stop").hide();
       jQuery("#stop span").text('<?php __p("Stop"); ?>');

       jQuery("#record span").text('<?php __p("recording again"); ?>');
       jQuery("#record").show();

       jQuery("#stop").attr('disabled',false);


       //show message and flush record list
       $('#status').html('Aufnahme gespeichert');


       if(jQuery("#recorder_finish_msg").length == 0)
       {
          //append msg
          var html='<div id="recorder_finish_msg" class="alert alert-success span9">';
             //html+='Aufnahme ist fertig';
             html+="<?php __p('Recording is finished'); ?>";
             html+='</div>';

          jQuery("#msg-container").append(html);
       }
       else
       {
          //update msg
          jQuery("#recorder_finish_msg").text("<?php __p('Recording updated'); ?>");
       }
    }

    function recorder_failed()
    {
    }

    function flush_record_list()
    {
       var url="record_list.php?aa_inst_id="+aa_inst_id;
       jQuery.get(url,function(response){
          jQuery("#record_list").hide();
          jQuery("#record_list").html(response);
          jQuery("#record_list").slideDown(600,function(){
             init_audio();
          });

       });

    }

    function init_audio()
    {
       audiojs.events.ready(function() {
          var as = audiojs.createAll();
       });

       //return false; // use orginal html 5 audio 
    }
	</script>
	
	<!-- Show admin panel if user is admin -->
  <?php // Show admin panel, when page admin
     if (is_admin() ) 
     {
        include_once 'admin/admin_panel.php';
     } 
  ?>

</body>
</html>
