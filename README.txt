--------------------------------------------------------------------------------
--------------------------------- OOHOO CAPTURE --------------------------------
--------------------------------------------------------------------------------

--------------------------------------------------------------------------------
Description
--------------------------------------------------------------------------------

This repository allows users to record directly video, sound and take photo 
WITHOUT a streaming server.
Activate the repository, and each time you can upload a file, click on the
repository "Capture" and choose if you want to record a sound with your 
microphone, or a video or a picture taken from your camera.
After recording, you can edit your video to only select the wanted part!

--------------------------------------------------------------------------------
Prerequisites
--------------------------------------------------------------------------------

Server side:
 - FFMPEG Recent version or AVCONV. In order to works ffmpeg or avconv must have
   the audio codec "aac".
 - And nothing else!

Client side: 
 - Flash Player - last version
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

Note: You can customize the access to each functionalities (audio, video, photo)
with capabilities:
  - repository/capture:view    - The general access
  - repository/capture:video
  - repository/capture:audio
  - repository/capture:photo

--------------------------------------------------------------------------------

For more informations, please go to the online documentation => http://oohoo.biz
