<html>
<head>
<script src="js/jquery.min.js"> </script>
<script src="js/jRecorder.js"> </script>
</head>


<body> 
 
   <script>
   
   $.jRecorder(
     
     { 
        host : 'acceptfile.php?filename=test.wav' ,  //replace with your server path please
        
        callback_started_recording:     function(){callback_started(); },
        callback_stopped_recording:     function(){callback_stopped(); },
        callback_activityLevel:          function(level){callback_activityLevel(level); },
        callback_activityTime:     function(time){callback_activityTime(time); },
        
        callback_finished_sending:     function(time){ callback_finished_sending() },
        
        
        swf_path : 'jRecorder.swf',
     
     }
     
   
        
   
   );
 </script>
<input type="button" id="record" value="Record" style="color:red">  
<input type="button" id="stop" value="Stop">
<input type="button" id="send" value="Send Data">

</body>
</html>


 <script type="text/javascript">

                  $('#record').click(function(){
                      $.jRecorder.record(30);
                  })
            
                  $('#stop').click(function(){
                     $.jRecorder.stop();
                  })
                  
                  $('#send').click(function(){
                     $.jRecorder.sendData();
                  })
 </script>
		
