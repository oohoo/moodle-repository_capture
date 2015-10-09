<?php
if(isset($_REQUEST['bindata']))
{
	$filename = "result".rand(10000,99999);
	//Save the File content
	file_put_contents('result/'.$filename.'.zip', base64_decode($_REQUEST['bindata']));
	file_put_contents('result/'.$filename.'.txt', $_REQUEST['bindata']);
	
	$zip = new ZipArchive();
	$zip->open('result/'.$filename.'.zip');
	$zip->extractTo('result/'.$filename.'/');
	$zip->close();
	
	$dir = dirname(__FILE__);
	$files = scandir($dir.'/result/'.$filename.'/');
	
	$firstImage = $files[3];
	$firstNum = explode('.', $firstImage);
	$firstNum = $firstNum[0];
	$firstNum = substr($firstNum, 3);
	
	$ds = DIRECTORY_SEPARATOR;
	$ffmpeg = "\"C:\\Program Files\\ffmpeg\\bin\\ffmpeg\" ";
	
	$res = shell_exec("$ffmpeg -y -framerate 15 -f image2 -start_number $firstNum -i \"result$ds$filename{$ds}img%06d.jpg\" -i \"result$ds$filename{$ds}audio.wav\" \"$dir{$ds}result$ds$filename.mp4\" 2>&1");
	$res = shell_exec("$ffmpeg -y -framerate 15 -f image2 -start_number $firstNum -i \"result$ds$filename{$ds}img%06d.jpg\" -i \"result$ds$filename{$ds}audio.wav\" -ac 2 -acodec vorbis -ab 64k -strict -2 \"$dir{$ds}result$ds$filename.ogg\"2>&1");
	$res = shell_exec("$ffmpeg -y -framerate 15 -f image2 -start_number $firstNum -i \"result$ds$filename{$ds}img%06d.jpg\" -i \"result$ds$filename{$ds}audio.wav\" \"$dir{$ds}result$ds$filename.mov\" 2>&1");
	//echo '<pre>'.$res.'</pre>';
	echo '<a href="'.dirname($_SERVER['REQUEST_URI'])."/result/$filename".'">The video Folder! </a>';
	
	/**
	array_map('unlink', glob('result/'.$filename.'/*.jpg'));
	unlink('result/'.$filename.'/audio.wav');
	rmdir('result/'.$filename.'/');
	unlink('result/'.$filename.'.zip');*/
	die;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  dir="ltr" lang="fr" xml:lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>test Capture</title>
	<meta charset="utf-8">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js"></script>
	<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/base/jquery-ui.css"  rel="stylesheet" type="text/css" media="screen"/>
	<script type="text/javascript">
function getFlashMovieObject(movieName)
{
	if (window.document[movieName]) 
	{
		return window.document[movieName];
	}
	if (navigator.appName.indexOf("Microsoft Internet")==-1)
	{
		if (document.embeds && document.embeds[movieName])
			return document.embeds[movieName]; 
	}
	else // if (navigator.appName.indexOf("Microsoft Internet")!=-1)
	{
		return document.getElementById(movieName);
	}
}
	</script>
	<style type="text/css">
	#slider-time-begin-end, #slider-time-begin-end .ui-slider-range
	{
		background:0;
		border:0;
	}
	#slider-time-begin-end .ui-slider-handle
	{
		width: 0px;
		height: 0px;
		border-style: solid;
		border-width: 0 10px 20px 10px;
		border-color: transparent transparent #c4c4c4 transparent;
		background: transparent;
	}
	</style>
</head>
<body>

<div>
<object type="application/x-shockwave-flash" data="videorecorder.swf?prefixjs=test_" width="320" height="240" name="videorecorder" id="videorecorder" style="outline: none;">
	<param name="allowScriptAccess" value="always" />
	<param name="allowFullScreen" value="true" />
	<param name="wmode" value="#FFFFFF"> 
	<param name="movie" value="videorecorder.swf?prefixjs=test_" />
	<param name="quality" value="high" />
</object>
</div>
<div>
	<span>Time <b><i><span id="timePassed"></span></i></b></span>
</div>
Video Width and Height: <br />
<input type="text" name="recordwidth" id="recordwidth" size="10" value="320"/>
<input type="text" name="recordheight" id="recordheight" size="10" value="240"/>
<input type="button" onclick="update_recordsize();" value="Send Width and Height"/><br />
<input type="button" onclick="videorecorder.initRecorder();" value="Init the player - Video Mode"/><br />
<input type="button" onclick="videorecorder.initRecorder(true);" value="Init the player - Audio Mode"/><br />
<input type="button" onclick="videorecorder.initRecorder(false, true);" value="Init the player - Photo Mode"/><br />

<input type="button" onclick="videorecorder.startRecording();" value="Start Recording"/><br />
<input type="button" onclick="videorecorder.stopRecording();" value="Stop Recording"/><br />

Trunc video: <br />
<input type="text" id="time-begin-end" style="border: 0; color: #f6931f; font-weight: bold;" readonly="readonly" autocomplete="off" />
<input type="hidden" id="time-current" autocomplete="off" />
<div id="slider-time" style="width:500px"></div>
<input type="hidden" id="time-begin" autocomplete="off" />
<input type="hidden" id="time-end" autocomplete="off" />
<div id="slider-time-begin-end" style="width:500px; height:10px;"></div>
<input type="button" onclick="playVideo();" value="Play this trunc"/><br />
<input type="button" onclick="videorecorder.stopVideo(true)" value="Stop playing"/><br />

Saving process: <br />
<div id="progressbar-save" style="width:500px"></div>
<input type="button" onclick="videorecorder.saveRecording($('#time-begin').val(),$('#time-end').val());" value="Save Recording"/><br />
<br />
<div><a href="" id="file-saved-link"></a>
<div id="video-html-player"></div>
</div>

test...<br/>
<form method="post">
<textarea name="bindata" id="bindata"></textarea>
<input type="submit"/>
</form>


	<script type="text/javascript">
		var videorecorder;
		var totalTime = 0;
		
		$(function()
		{
			$( "#slider-time" ).slider({
				min: 0,
				max: 1000,
				slide: function( event, ui ) {
					$( "#time-current" ).val( ui.value);
				}
			});
			$( "#slider-time-begin-end" ).slider({
				range: true,
				min: 0,
				max: 1000,
				values: [ 0, 1000 ],
				slide: function( event, ui ) {
					$( "#time-begin-end" ).val( ui.values[ 0 ]/1000 + " - " + ui.values[ 1 ]/1000 );
					$( "#time-begin" ).val( ui.values[ 0 ] );
					$( "#time-end" ).val( ui.values[ 1 ] );
				}
			});
			$( "#progressbar-save" ).progressbar(
			{
				value: 0
			});
		});
		
		function playVideo()
		{
			videorecorder.stopVideo();
			
			var startTime = $('#time-begin').val();
			if ( $('#time-current').val() > $('#time-begin').val())
			{
				startTime = $('#time-current').val();
			}
			videorecorder.playVideo(startTime,$('#time-end').val());
		}
		
		//Run this function when player is ready
		function test_videorecorder_ready()
		{
			if (videorecorder == undefined)
			{
				videorecorder = getFlashMovieObject('videorecorder');
			}
		}
		
		function update_recordsize()
		{
			videorecorder.setRecordSize($('#recordwidth').val(), $('#recordheight').val());
			$(videorecorder).attr('width', $('#recordwidth').val());
			$(videorecorder).attr('height', $('#recordheight').val());
		}
		
		function test_videorecorder_nohardware()
		{
			console.log("No Hardware");
		}
		function test_videorecorder_updateTimer(secondes)
		{
		console.log('UPDATE');
			$('#timePassed').html(secondes);
		}
		function test_videorecorder_updateConversion(length, pos)
		{
			console.log("Convert Total = "+length+" - Position = "+pos);
			$( "#progressbar-save" ).progressbar("option", "value", pos*100/length);
		}
		function test_videorecorder_addAudio()
		{
			console.log("Add Audio to zip file");
		}
		function test_videorecorder_saveFile(linkResult)
		{
			console.log("Save Zip File...");
			$( "#progressbar-save" ).progressbar("option", "value", 100);
			
			$("#file-saved-link").attr("href", linkResult);
			$("#file-saved-link").html("Video successfully saved!! Click here to download");
			
			$("#video-html-player").html("<video controls='controls'><source src='"+linkResult+".mp4' type='video/mp4'><source src='"+linkResult+".ogg' type='video/ogg'><source src='"+linkResult+".mov' type='video/mov'></video>");
		}
		function test_videorecorder_setTotalTime(time)
		{
			totalTime = time*1000;
			$( "#slider-time-begin-end").slider( "option", "max", totalTime );
			$( "#slider-time-begin-end").slider( "option", "values", [0,totalTime] );
			$( "#time-begin" ).val( 0 );
			$( "#time-end" ).val( totalTime );
			$( "#slider-time" ).slider( "option", "max", totalTime );
			console.log("Set total time " + time);
		}
		
		function test_videorecorder_playFrame(time, totalLength)
		{
			$('#timePassed').html(time/1000);
			$( "#slider-time" ).slider( "value", time );
		}
		function test_videorecorder_endPlay()
		{
			console.log("END of video playing");
			$('#timePassed').html("");
			
			$( "#slider-time" ).slider( "value", $('#time-begin').val() );
		}
		function test_videorecorder_sendFileData(filedata)
		{console.log(filedata);
			$("#bindata").html(filedata);
		}
	</script>
	
<br />


</body>
</html>