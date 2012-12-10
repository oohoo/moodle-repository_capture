<?php

/**
 * *************************************************************************
 * *                            Capture                                   **
 * *************************************************************************
 * @package     repository_capture                                        **
 * @subpackage  capture                                                   **
 * @name        Capture                                                   **
 * @copyright   oohoo.biz                                                 **
 * @link        http://oohoo.biz                                          **
 * @author      Nicolas Bretin                                            **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************ */

$string['btn_audio'] = 'Record an audio';
$string['btn_help'] = 'Recording Help';
$string['btn_help_help'] = 'Recording instructions:<br>
<ol>
<li>Use the recording button to record your sound or video</li>
<li>You can replay the recording with the play button</li>
<li>Use the two triangular handles to reduce the recording to only one part of your video</li>
<li>After finishing editing the recording, click on the Save button to send your recording (Processing can take a will)</li>
</ol>
';
$string['btn_photo'] = 'Take a picture';
$string['btn_play'] = 'Play/Pause';
$string['btn_record'] = 'Record';
$string['btn_record_photo'] = 'Take a picture';
$string['btn_reset'] = 'Try again';
$string['btn_save'] = 'Save';
$string['btn_settings'] = 'Recording settings';
$string['btn_stop'] = 'Stop';
$string['btn_video'] = 'Record a video';
$string['configplugin'] = 'Configuration for Capture plugin';
$string['default_filename'] = 'Recording';
$string['download'] = 'Download';
$string['err_no_hardware'] = 'Camera or microphone is missing. Please connect your device and refresh the page (depending of your browser, you may need to restart your browser)';
$string['err_record_ffmpeg_exec'] = 'Execution of this ffmpeg path failed. Please check the path and try again.';
$string['err_record_fps_range'] = 'Value must be in the range 10 - 60';
$string['err_record_quality_range'] = 'Value must be in the range 20 - 100';
$string['pluginname'] = 'Capture';
$string['pluginname_help'] = 'Record an audio or video directly in Moodle and upload it in the Moodle file system.';
$string['capture:audio'] = 'Use Capture audio mode in file picker';
$string['capture:photo'] = 'Use Capture photo mode in file picker';
$string['capture:video'] = 'Use Capture video mode in file picker';
$string['capture:view'] = 'Use Capture in file picker';
$string['radio_no'] = 'No';
$string['radio_yes'] = 'Yes';
$string['record_audio'] = 'Allow recording audio';
$string['record_audio_help'] = 'Select yes if you want to allow users to record audio with the microphone';
$string['record_ffmpeg'] = 'FFMPEG exec path';
$string['record_ffmpeg_help'] = 'The path of ffmpeg (or avconv depending of your system). Generaly <b>ffmpeg</b> is enough but on some server, it needs the full path like <b>"C:\Program Files\ffmpeg\bin\ffmpeg.exe"</b> (quotes are important if there is spaces in the path)';
$string['record_fps'] = 'The framerate';
$string['record_fps_help'] = 'Set the framerate in frames per second. <b>Default = 15</b>.<br>
<b>Note</b>: More there is frames per seconds, more the video size will be big and the rendering slow. Max FPS 60';
$string['record_photo'] = 'Allow taking picture from the webcam';
$string['record_photo_help'] = 'Select yes if you want to allow users to take a picture from the webcam';
$string['record_quality'] = 'Record Quality (in %)';
$string['record_quality_help'] = 'Default 70%. 100% is the best quality.<br>
<b>Note</b>: More the quality is high, the more time it will take to encode the video';
$string['record_video'] = 'Allow recording video';
$string['record_video_help'] = 'Select yes if you want to allow users to record video with the webcam and microphone';
$string['saveas'] = 'File name: ';
$string['setauthor'] = 'Author: ';
$string['setlicense'] = 'Choose license: ';
$string['title_audio'] = 'Audio';
$string['title_audio_help'] = 'Click on Record an audio to use your microphone to add a new mp3 file';
$string['title_info'] = 'Informations';
$string['title_info_help'] = 'Complete these informations in order to save the file';
$string['title_photo'] = 'Photo';
$string['title_photo_help'] = 'Choose your picture size and click on Take a picture to use your webcam to add a new jpg file';
$string['title_video'] = 'Video';
$string['title_video_help'] = 'Choose your video size and click on Record a video to use your webcam and microphone to add a new mp4 file';
$string['video_conversion_processing'] = 'Processing...';
$string['video_height'] = ' - Height: ';
$string['video_width'] = 'Width: ';