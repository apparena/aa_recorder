<?php 
	// init app-arena once, use init_session.php later...
 	include_once( "init.php" );
	$session->app['fb_share_url'] = "https://apps.facebook.com/" . $session->instance['fb_app_url']."/fb_share.php?aa_inst_id=".$session->instance['aa_inst_id'];

	$aa_inst_id=$session->instance['aa_inst_id'];
?>

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
	<?php if(app_has_recorded($session->instance['aa_inst_id'],$session->fb['fb_user_id']) == true): ?>
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
        <audio id="audio1" src="<?php echo $session->config['audio_intro_url']['value']; ?>" preload="auto"
     ></audio>
				</div>
			<?php } ?>
	
	
			<?php if(app_has_recorded($session->instance['aa_inst_id'],$session->fb['fb_user_id']) == false): ?>
				<!-- Recorder -->
				<div class="row show-grid player-status-bar">
					<div class="span2 time-container">
						<?php __p("Time"); ?>: <span id="time">00:00</span>
					</div>
					<div class="span4 status-container">
						<?php __p("Status"); ?>: <span id="status"></span>
					</div>
					<div class="span4 level-container" id="levelbase" >
						<div id="levelbar"></div>  
					</div>
				</div>

				<div class="row show-grid player-control">
					<div class="span2 record-container" >
						<a class="btn btn-danger" id="record" value="Record">
							<i class="icon-volume-up icon-white"></i> <?php __p("Record"); ?>
						</a>
					</div>
					<div class="span2 stop-container">
						<a class="btn" id="stop" value="Stop">
							<i class="icon-stop icon-black"></i> <?php __p("Stop"); ?>
						</a>
					</div>
					<div class="span2 save-container" id="img_tag">
						<a class="btn" id="send" value="Send Data">
							<i class="icon-upload icon-black"></i> <?php __p("Save"); ?>
						</a>
            <!-- send data -->
              <span id="save_button">
                <span id="flashcontent">
                  <p>Your browser must have JavaScript enabled and the Adobe Flash Player installed.</p>
                </span>
              </span>

					</div>
				</div>
			<?php else: ?>
			<!--
				<div class="alert alert-success span9">
					<?php //__p('You already record a message'); ?>
				</div>
				-->
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
	<script src="js/audiojs/audio.min.js?v3"> </script> 
	<script src="js/libs/aa.js?v5"></script>
  <script type="text/javascript" src="js/swfobject.js"></script>
  <script type="text/javascript" src="js/recorder.js"></script>

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
			$fb_user_id=-1;
		}
		?>
		//defined post wall variables
		var fb_share_title=  '<?=$session->config["fb_share_title"]["value"]?>';
		var fb_share_link=  'https://apps.facebook.com/<?=$session->instance["fb_app_url"].'/fb_share.php?aa_inst_id='.$session->instance['aa_inst_id'] ?>';
		var fb_share_subtitle= '<?=$session->config["fb_share_subtitle"]["value"]?>';
		var fb_share_desc =  '<?=$session->config["fb_share_desc"]["value"]?>';
		var fb_share_img='<?=$session->config["fb_share_img"]["value"]?>';
    var aa_inst_id=0;

    var recorder_status='not ready'; //audio recorder status

    var tag_image='';
		var userHasAuthorized = false;
		
		
    jQuery(document).ready(function() {
			userHasAuthorized = false;
			fb_app_id     = '<?=$session->instance["fb_app_id"]?>';
			fb_canvas_url = '<?=$session->instance["fb_canvas_url"]?>';
			aa_inst_id    = '<?=$session->instance["aa_inst_id"]?>';		



      flush_record_list();

      //init audio 
      init_audio();
	
      /*
      jQuery.jRecorder({ 
         host : 'acceptfile.php?filename='+ fb_user_id+'_<?php echo $session->instance['aa_inst_id']; ?>',  //replace with your server path please       
         callback_started_recording:     function(){callback_started(); },
         callback_stopped_recording:     function(){callback_stopped(); },
         callback_activityLevel:          function(level){callback_activityLevel(level); },
         callback_activityTime:     function(time){callback_activityTime(time); },
         callback_finished_recording:     function(time){ callback_finished_recording() },

         callback_finished_sending:     function(time){ callback_finished_sending() },
         swf_path : 'jRecorder.swf',
      });
      */


      jQuery('#record').click(function(){
         //$.jRecorder.record(30);

         alert(recorder_status);
         alert(Recorder.status);

         if(recorder_status != 'start')
         {
            if( (Recorder.status != "no_microphone_found")
            && (Recorder.status != "microphone_not_connected")
            && (Recorder.status != "microphone_user_request")
         )
         {
            Recorder.record('audio', 'audio.wav');

            recorder_status='start';

            $('#status').html('Aufnahme gestartet');
            document.getElementById('stop').innerHTML = 'Stop';
         }
         }

      });

      jQuery('#stop').click(function(){
         //$.jRecorder.stop();
         alert(recorder_status);

         if(recorder_status == 'start')
         {
            if( (Recorder.status != "no_microphone_found")
            && (Recorder.status != "microphone_not_connected")
            && (Recorder.status != "microphone_user_request")
         )
         {
            Recorder.record('audio');

            recorder_status='stop';
            $('#status').html('Aufnahme gestoppt');

            document.getElementById('stop').innerHTML = 'Abspielen';
            document.getElementById('record').innerHTML = 'Neu aufnehmen';
         }
         }

      });

      jQuery('#send').click(function(e){
         save_tag_callback=function(){
            //$.jRecorder.sendData();
         }

         //for click image tag
         heightOff = $("#header_img").height();
         //$("#_debug").html("offset height: " + heightOff);

         // get the mouse-coords where the user clicked the image
         var xCoord = ( e.pageX - this.offsetLeft );
         var yCoord = ( e.pageY - heightOff );
         authUser( xCoord, yCoord );
         document.getElementById("flashrecarea").style.top = "630px";


      }) ;   

      //init recorder
      var appWidth = 24;
      var appHeight = 24;
      var flashvars = {'event_handler': 'microphone_recorder_events', 'upload_image': 'images/upload.png'};
      var params = {};
      var attributes = {'id': "recorderApp", 'name':  "recorderApp"};
      swfobject.embedSWF("recorder.swf", "flashcontent", appWidth, appHeight, "10.1.0", "", flashvars, params, attributes);
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


    //function callback
    function callback_activityTime(time){
       $('#time').html(time);  
    }
    function callback_finished(){
       $('#status').html('Aufnahme ist fertig');
    }

    function callback_started(){
       $('#status').html('Aufnahme gestartet');
    }
    function callback_error(code){
       $('#status').html('Error, code:' + code);
    }
    function callback_stopped(){
       $('#status').html('Aufnahme gestoppt');
    }
    function callback_finished_recording(){             

       $('#status').html('Aufnahme beendet');

       var html='<div class="alert alert-success span9">';
          html+='<?php __p('Your sound recorded successful'); ?>';
          html+='</div>';
       jQuery("#msg-container").append(html);

       flush_record_list();
    }

    function callback_finished_sending(){
       $('#status').html('Aufnahme gespeichert');
       document.getElementById('savesounds').style.visibility = 'visible'; 
    }
    function callback_activityLevel(level){
       $('#level').html(level);
       /*
       if(level == -1){
          $('#levelbar').css("width",  "2px");
       }
       else {
          $('#levelbar').css("width", (level * 2)+ "px");
       }
       */
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
    }
	</script>
	
	<!-- Show admin panel if user is admin -->
  <?php // Show admin panel, when page admin
     if (is_admin() ) 
     {
        include_once 'admin/admin_panel.php';
     } 
  ?>

  <!-- audio form -->
<form id="uploadForm" name="uploadForm" action="upload.php">
<input name="authenticity_token" value="xxxxx" type="hidden">
<input name="upload_file[parent_id]" value="1" type="hidden">
<input name="format" value="json" type="hidden">
</form>
  <!-- audio form end-->

</body>
</html>
