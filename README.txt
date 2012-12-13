--------------------------------------------------------------------------------
--------------------------------- OOHOO CAPTURE --------------------------------
--------------------------------------------------------------------------------

--------------------------------------------------------------------------------
Description
--------------------------------------------------------------------------------

This repository allows users to record video, sound and take a photo
WITHOUT a streaming server.
This works as any other Moodle 2.x repository. It must first be enabled.
Once enabled, it will show up in the file picker. Simply click on the "Capture"
repository and choose whether you want to record a sound, video or a picture taken
with your webcam.

Once you are done recording, you can edit your video by trimming the beginning
and the end.

Note: You must install FFMPEG on your Moodle server inorder for this plugin 
to work.

--------------------------------------------------------------------------------
Prerequisites
--------------------------------------------------------------------------------

Server side:
 - FFMPEG Recent version or AVCONV. In order for the conversion to work, ffmpeg or avconv must have
   the audio codec "aac".
 - And nothing else!

Client side: 
 - Flash Player - latest version
 - Javascript activated
 - Microphone for video and audio recording
 - Camera for video and photo

--------------------------------------------------------------------------------
Installation
--------------------------------------------------------------------------------

 1. Rename the folder to 'capture'
 2. Copy the folder tab to moodle/repository
 3. Install the plugin
 4. Activate it in:
    -> Site administration
      -> Plugins
        -> Repositories
          -> Manage repositories
 5. Set the default fields for the repository.

Note: You can customize the access to each functionality (audio, video, photo)
with capabilities:
  - repository/capture:view    - The general access
  - repository/capture:video
  - repository/capture:audio
  - repository/capture:photo

--------------------------------------------------------------------------------

For more informations, please go to the online documentation => http://oohoo.biz
