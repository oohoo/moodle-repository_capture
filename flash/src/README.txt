--------------------------------------------------------------------------------
--------------------------------- Video Recorder -------------------------------
--------------------------------------------------------------------------------

Author: Nicolas Bretin
Version: 1.0

--------------------------------------------------------------------------------
Description
--------------------------------------------------------------------------------

Inspired by an original idea of leelib (https://github.com/zeropointnine/leelib).
I needed more options and the result given by leelib was too big 
(around 20Mb for 10 secondes of recording)
This flash application allows to record audio and video WITHOUT a streaming 
server.
The trick: The flash capture each picture of your camera and display it.
At the end, a Zip file of all pictures + the sound is sent to the server which
will convert the serie of images in a real video (with ffmpeg for example).

Functionalities are:
 - Record a video
 - Record a sound
 - Record only one picture (photo mode)
 - You can play and save only one part of your video/sound (Video editing!)


--------------------------------------------------------------------------------
Prerequisites
--------------------------------------------------------------------------------

You will need a server to save and convert your file.
See the test.php file as example! It contains almost all functionalities of the
recorder.

--------------------------------------------------------------------------------

For more informations or comments, contact me => nicolas.bretin at gmail.com
