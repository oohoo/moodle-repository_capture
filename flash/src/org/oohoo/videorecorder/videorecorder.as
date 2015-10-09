/**
 * *************************************************************************
 * *                            Video Recorder                            **
 * *************************************************************************
 * @package     video_recorder                                            **
 * @subpackage  video_recorder                                            **
 * @name        Video Recorder                                            **
 * @author      Nicolas Bretin                                            **
 * *************************************************************************
 * ************************************************************************ */

package org.oohoo.videorecorder
{
	import flash.media.Camera;
	import flash.media.Microphone;
	import flash.utils.ByteArray;
	import flash.utils.getTimer;
	import flash.display.BitmapData;
	import flash.external.ExternalInterface;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import flash.net.FileReference;
	import mx.graphics.codec.JPEGEncoder;
	import leelib.util.flvEncoder.MicRecorderUtil;
	import org.bytearray.waveencoder.WaveEncoder;
	import org.aszip.zip.ASZip;
	import org.aszip.compression.CompressionMethod;
	import org.aszip.saving.Method;
	import mx.utils.Base64Encoder;
	import flash.display.Sprite;
	import flash.media.Video;
	import flash.display.Stage;
	import flash.media.Sound;
	import flash.events.SampleDataEvent;
	import flash.media.SoundChannel;
	import flash.events.Event;
	
	public class videorecorder
	{
		//The stage is needed to display video elements
		public var stage:Stage;
		//The video element in the flash 
		public var video:Video;
		
		//The prefix added to each functions for the JS call
		public var prefixJS:String;
		
		//The recording devices
		public var camera:Camera;
		public var microphone:Microphone;
		
		//The Recording params
		public var recordWidth:Number;
		public var recordHeight:Number;
		public var recordFPS:Number;
		public var recordQuality:Number;
		public var recordAudioOnly:Boolean;
		public var recordPhotoOnly:Boolean;
		
		//The containing devices
		public var frames:Array;
		public var micUtil:MicRecorderUtil;
		public var audio:ByteArray;
		public var audiopart:ByteArray;
		
		//The startTime recording
		public var startTime:Number;
		//The TimeoutID. Used to stop the frames capture
		private var timeoutId:Number;
		
		
		//The playTimeoutID is used to play the video
		private var playTimeoutId:Number;
		//The sound and soundchannel of the playing
		private var sound:Sound;
		private var soundchannel:SoundChannel;
		//The playing sprite
		public var playOutput:Sprite;
		
		//The jpegEncoder
		private var jpgenc:JPEGEncoder;
		//And the Zip Tool
		private var zipfile:ASZip;

		/**
		 * The constructor
		 * @param Number w The width
		 * @param Number h The height
		 * @param Number fps The framerate
		 * @param Number quality The quality of the video
		 * @param String prefix The prefix of the JS calls
		 * @param Boolean audioonly If true, only record audio
		 * @param Boolean photoonly If true, only take a picture
		 **/
		public function videorecorder(w:Number=320, h:Number=240, fps:Number=15, quality:Number=50, prefix:String='', audioonly:Boolean=false, photoonly:Boolean=false)
		{
			recordWidth = w;
			recordHeight = h;
			recordFPS = fps;
			recordQuality = quality;
			prefixJS = prefix;
			recordAudioOnly = audioonly;
			recordPhotoOnly = photoonly;
		}
		
		/**
		 * Init the recorder for recording a new Video
		 * @param Stage stg The stage Element
		 * @param Video vid The video Element
		 * @return boolean Return true if initiat successfully
		 **/
		public function initForRecording(stg:Stage, vid:Video):Boolean
		{
			stage = stg;
			video = vid;
			
			if(recordAudioOnly)
			{
				video.visible = false;
			}
			if(!recordPhotoOnly)
			{
				video.alpha = 0.5;
			}
			
			//If the hardware is correct continue else stop here
			if(checkAndSetHardware())
			{
				frames = new Array();
				if(!recordPhotoOnly)
				{
					micUtil = new MicRecorderUtil(microphone);
				}
				else
				{
					micUtil = null;
				}
				return true;
			}
			else
			{
				return false;
			}
		}
		
		/**
		 * Check if the hardware is well connected and set it if exists
		 * @return boolean Return true if well connected
		 **/
		public function checkAndSetHardware():Boolean
		{
			var ret:Boolean = true;
			//If audio only  don't look for the camera
			if(!recordAudioOnly)
			{
				if (Camera.names.length > 0)
				{
					camera = Camera.getCamera(); // Get default camera.
					camera.setMode(stage.stageWidth, stage.stageHeight, recordFPS);
					camera.setQuality(0, 100);
				}
				else
				{
					ret = false;
				}
			}
			else
			{
				camera = null;
			}
			
			//No need the sound for the photo mode
			if(!recordPhotoOnly)
			{
				if (Microphone.names.length > 0)
				{
					microphone = Microphone.getMicrophone(); // Get default micropohone.
					microphone.setUseEchoSuppression(true);
					microphone.setSilenceLevel(10, 1000);
					microphone.gain = 100;
					microphone.rate = 44;
					microphone.setLoopBack(true);
					microphone.setLoopBack(false);
				}
				else
				{
					ret = false;
				}
			}
			else
			{
				microphone = null;
			}
			
			return ret;
		}
		
		//-------------------- RECORDING ----------------------------------------

		/**
		 * Start the Recording
		 **/
		public function startRecording()
		{
			//Reset one more time the frames
			frames = new Array();
			//Put the video on a transparency normal
			video.alpha = 1;
			
			if(!recordPhotoOnly)
			{
				//Start the microphone
				micUtil.record();
			}
			//Launch the timer
			startTime = getTimer();
			//And start capturing the frames
			captureFrame();
		}
		
		/**
		 * The function that capture each frame
		 **/
		private function captureFrame():void
		{
			// create a bitmap of the size of the video
			var b:BitmapData = new BitmapData(recordWidth, recordHeight, false, 0x0);
			//And copy the content of the video
			b.draw(video);
			//Add the bitmap to the array
			frames.push(b);
			
			//The current time
		 	var sec:Number = int(frames.length / recordFPS*1000)/1000;
			
			//Send event to the JS to update the timer
			ExternalInterface.call(prefixJS+"videorecorder_updateTimer", sec);
			
			//If photo mode stop after the first frame
			if(!recordPhotoOnly)
			{
				// schedule next captureFrame
				var elapsedMs:int = getTimer() - startTime;
				var nextMs:int = (frames.length / recordFPS) * 1000;
				var deltaMs:int = nextMs - elapsedMs;
				if (deltaMs < 10) deltaMs = 10;
				timeoutId = setTimeout(captureFrame, deltaMs);
			}
			else
			{
				stopRecording();
			}
		}
		
		/**
		 * Stop the Recording
		 **/
		public function stopRecording()
		{
			//Stop the frame capture
			clearTimeout(timeoutId);
			if(!recordPhotoOnly)
			{
				//Put the video on a transparency 0.5
				video.alpha = 0.5;
			}
		
			//Give a little bite more time to the microphone
			setTimeout(stopRecording2, 200);
			//Send info to the JS
			ExternalInterface.call(prefixJS+"videorecorder_setTotalTime", int(frames.length / recordFPS*1000)/1000);
		}
		
		/**
		 * Stop the recording
		 **/
		private function stopRecording2()
		{
			if(!recordPhotoOnly)
			{
				//Stop the Mic
				micUtil.stop();
			}
			//Put the micro data in audio
			audio = micUtil.byteArray;
			
			//If picture mode display right now the picture!
			if(recordPhotoOnly)
			{
				playVideo(0,0);
			}
		}
		
		//-------------------- PLAYBACK ----------------------------------------
		
		/**
		 * Play a video from a specific start (in secondes) to a specific end (in seconds too)
		 * @param Number start The start time in secondes. milliseconds in decimals
		 * @param Number end The end time in secondes. milliseconds in decimals
		 **/
		public function playVideo(start:Number, end:Number)
		{
			//Always stop the video before in case of double play
			stopVideo(true);
			
			//The displayed output
			playOutput = new Sprite();
			if(recordAudioOnly)
			{
				playOutput.visible = false;
			}
			
			//Hide the video element that display the Camera
			video.visible = false;
			
			//Set the startframe to 0
			var startframe:int = 0;
			//If the start is not 0 then the start frame will be the nearest frame of the time 
			if(start > 0)
			{
				startframe = int(start/(1000/recordFPS));
			}
			if(startframe >= frames.length-1)
			{
				startframe = 0;
			}
			//Set the end time to the nearest frame of the time 
			var endframe:int = int(end/(1000/recordFPS));
			//If the end frame is out of bounds set it to the last frame
			if(endframe > frames.length-1)
			{
				endframe = frames.length-1;
			}
			else if(endframe < startframe)
			{
				endframe = frames.length-1;
			}
			
			//For the sound, before playing it, it will be needed to go to the right position. so
			audio.position = 0;
			//Goto the right audio position
			//So the starting byte is the length in bytes of the audio recording for one frame, multiplied by the number of frame to go to the start frame
			var playtime:uint = uint((audio.length/(frames.length))*startframe);
			while(audio.bytesAvailable > 0 && audio.position < playtime)
			{
				//Read the audio to go to the right position
				audio.readFloat();
			}
			
			//Create the sound handler that will play the sound 
			sound = new Sound();
			sound.addEventListener(SampleDataEvent.SAMPLE_DATA, playSoundHandler);
			
			//Launch the timer starttime. Substract the number of frames before the startframe to the timer
			startTime = getTimer() - int(startframe*(1000/recordFPS));
			//And start displaying the frames
			playFrame(startframe, endframe);
		}
		
		/**
		 * This function stop the video and reinit all functions.
		 * @param Boolean startNew If startnew == true, don't send the JS event 
		 * @param Boolean pauseMode If pauseMode == true, keep displaying the picture of the moment
		 **/
		public function stopVideo(startNew:Boolean=false, pauseMode:Boolean=false)
		{
			clearTimeout(playTimeoutId);
			if(soundchannel != null)
			{
				soundchannel.stop();
			}
			sound = null;
			soundchannel = null;
			if(!startNew)
			{
				//Send info to the JS
				ExternalInterface.call(prefixJS+"videorecorder_endPlay");
			}
			if(!pauseMode)
			{
				if(playOutput != null && stage.contains(playOutput))
				{
					//Hide the output
					stage.removeChild(playOutput);
				}
				//And display the camera
				if(!recordAudioOnly)
				{
					video.visible = true;
				}
				if(!recordPhotoOnly)
				{
					//Display the camera with transparence to notice the difference
					video.alpha = 0.5;
				}
			}
		}
		
		/**
		 * This function is call to display each frame
		 * @param Sprite output The sprite that will show the images
		 * @param int currentframe The current frame to display
		 * @param int endframe The last frame to display
		 * @param Number oldtimeout The timeout length of the previous frame call. Used to calculate the next timeout
		 * @param int timer The timer time of the last picture displayed. Used to calculate the next timeout
		 * @param Sound sound The sound object
		 * @param SoundChannel The sound Channel
		 **/
		private function playFrame(currentframe:int, endframe:int)
		{
			var b:BitmapData;
			//If it is not the last frame +1
			if(currentframe <= endframe)
			{
				//If the soundChannel is null then start the sound
				if(soundchannel == null)
				{
					soundchannel = sound.play();
				}
				//Get the frame
				b = frames[currentframe];
				//Send info to the JS
				ExternalInterface.call(prefixJS+"videorecorder_playFrame", getTimer() - startTime, frames.length*(1000/recordFPS));
				//Start drawing the frame
				playOutput.graphics.beginBitmapFill(b, null, false, false);
				playOutput.graphics.drawRect(0,0,video.width, video.height);
				playOutput.graphics.endFill();
				//Display the frame
				stage.addChild(playOutput);
				
				//If recordPhotoOnly stop right now
				if(!recordPhotoOnly)
				{
					// schedule next frame
					var elapsedMs:int = getTimer() - startTime;
					var nextMs:int = ((currentframe+1) / recordFPS) * 1000;
					var deltaMs:int = nextMs - elapsedMs;
					if (deltaMs < 10) deltaMs = 10;
					//Call the next frame
					setTimeout(playFrame, deltaMs, currentframe+1, endframe);
				}
				else
				{
					stopVideo(false, true);
				}
			}
			else
			{
				stopVideo();
			}
		}
		
		/**
		 * This function is called by the Sound object. It will play the audio recording by segment
		 * @param SampleDataEvent e The sound Event
		 **/
		private function playSoundHandler(e:SampleDataEvent):void
		{
			//If there is bytes availables
			if(audio.bytesAvailable <= 0)
			{
				return;
			}
			//Loop for each block
			for(var i:int = 0; i < 8192; i++)
			{
				//Read the sample
				var sample:Number = 0;
				if(audio.bytesAvailable > 0)
				{
					sample = audio.readFloat();
				}
				//And play it in the stream (Play it two times because it is stereo)
				e.data.writeFloat(sample);
				e.data.writeFloat(sample);
			}
		}
		
		//-------------------- SAVING ----------------------------------------
		
		/**
		 * Call this function to save the Images and sound from the given intervalle
		 * @param Number start The start time in secondes. milliseconds in decimals
		 * @param Number end The end time in secondes. milliseconds in decimals
		 **/
		public function saveImages(start:Number, end:Number):void
		{
			//Set the startframe to 0
			var startframe:int = 0;
			//If the start is not 0 then the start frame will be the nearest frame of the time 
			if(start > 0)
			{
				startframe = int(start/(1000/recordFPS));
			}
			if(startframe >= frames.length-1)
			{
				startframe = 0;
			}
			//Set the end time to the nearest frame of the time 
			var endframe:int = int(end/(1000/recordFPS));
			//If the end frame is out of bounds set it to the last frame
			if(endframe > frames.length-1)
			{
				endframe = frames.length-1;
			}
			else if(endframe < startframe)
			{
				endframe = frames.length-1;
			}
			
			//Init the jpeg encoder with compression quality
			jpgenc = new JPEGEncoder(recordQuality);
			//Init the zip tool compression
			zipfile = new ASZip(CompressionMethod.GZIP);
			
			//Generate the audio part
			audiopart = getAudioPart(startframe, endframe);
			
			//If there is more than one frame
			if(endframe-startframe+1 > 0)
			{
				if(recordAudioOnly)
				{
					 //Call a periodic function to not freeze the interface
					setTimeout(saveImage, 40, 1, startframe, startframe, 1);
				}
				else
				{
					//Call a periodic function to not freeze the interface
					setTimeout(saveImage, 40, 1, startframe, endframe, endframe-startframe+1);
				}
			}
			else
			{
				
				setTimeout(saveImage, 40, 1, startframe, endframe, 1);
			}
		}
		
		/**
		 * This function is call periodically for each images. Use of the setTimeOut to avoid flash to freeze
		 * @param int start The first frame
		 * @param int endframe The last frame 
		 **/
		private function getAudioPart(startframe:int, endframe:int):ByteArray
		{
			//If the save takes all frames return the original audio
			if(startframe == 0 && endframe == frames.length-1)
			{
				return audio;
			}
			else
			{
				var a:ByteArray = new ByteArray();
				
				//reset the position of the audio
				audio.position = 0;
				//The start time is
				var starttime:uint = uint((audio.length/(frames.length))*startframe);
				var endtime:uint = uint((audio.length/(frames.length))*endframe);
				while(audio.bytesAvailable > 0 && audio.position < starttime)
				{
					//Read the audio to go to the right position
					audio.readFloat();
				}
				while(audio.bytesAvailable > 0 && audio.position < endtime)
				{
					//Read the audio to go to the right position
					a.writeFloat(audio.readFloat());
				}
				
				return a;
			}
		}
		
		/**
		 * This function is call periodically for each images. Use of the setTimeOut to avoid flash to freeze
		 * @param int filenum The file number in the zip
		 * @param int i The current frame
		 * @param int endframe The last frame 
		 * @param int totalframes The total number of frames to convert
		 **/
		private function saveImage(filenum:int, i:int, endframe:int, totalframes:int)
		{
			//Get the frame
			var btm:BitmapData = frames[i];
			
			//Conversion of the bitmap in jpeg
			var imgByteArray:ByteArray  = jpgenc.encode(btm);
			
			var imageNumber:String = addLeadingZero(filenum);
			
			//And add the image to the zip file
			zipfile.addFile(imgByteArray,"img"+imageNumber+".jpg");
			
			//Send info to the JS that a frame has been converted
			ExternalInterface.call(prefixJS+"videorecorder_updateConversion", totalframes, filenum);
			
			//If not the last frame, call the next image conversion
			if(i < endframe)
			{
				setTimeout(saveImage, 40, filenum+1, i+1, endframe, totalframes);
			}
			else //If it is the last frame
			{
				if(!recordPhotoOnly)
				{
					//Save the sound
					var waveEncode:WaveEncoder = new WaveEncoder(1);
					//It is an audio with one Channel 16 bits and 44KHz
					var waveFile:ByteArray = waveEncode.encode(audiopart, 1, 16, 44100);
					//Add the wave file to the zip
					zipfile.addFile(waveFile,"audio.wav");
					//Call the JS 
					ExternalInterface.call(prefixJS+"videorecorder_addAudio");
				}
				
				//Get the zip file
				var fileRef:FileReference = new FileReference();
				var zipBytes:ByteArray = zipfile.saveZIP(Method.LOCAL);
				
				//Encode the zip in base 64
				var zipData:Base64Encoder = new Base64Encoder();
				zipData.encodeBytes(zipBytes);
				
				ExternalInterface.call(prefixJS+"videorecorder_sendFileData", zipData.toString());
			}
		}
		
		/**
		 * Add zero to the begining of the number for a file name
		 * @param int num The number
		 * @return string Return the int with zeroes
		 **/
		private function addLeadingZero(num:int):String
		{
			var leadingZeroes:String="000000";
			
			var intToString:String=String(num); 
			
			leadingZeroes=leadingZeroes.substr(0,leadingZeroes.length-intToString.length);
			
			return leadingZeroes+intToString;
		}
	}
}
