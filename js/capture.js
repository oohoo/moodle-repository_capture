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

//The object repository_capture
var rra = null;
var parent_block = null
	
if(typeof jQuery != 'undefined')
{
    $(function()
    {
        rra = new repository_capture();
        parent_block = $(frameElement).parent();
        
        //----------------------------------------------------------------------
        //Get form info
        $('#setlicense').html($("#repo_upload_file_setlicense", parent_block).html());
        $('#setlicense').val($("#repo_upload_file_setlicense", parent_block).val());
        $('#setauthor').val($("#repo_upload_file_setauthor", parent_block).val());
        
        //----------------------------------------------------------------------
        //Check if there is mp4 and mp3 in accepted files.
        var mp3_ok = false;
        var mp4_ok = false;
        var jpg_ok = false;
        var all_ok = true;
        $('input[name="accepted_types[]"]', parent_block).each(function()
        {
            all_ok = false;
            if($(this).val() == '.mp3')
            {
                mp3_ok = true;
            }
            else if($(this).val() == '.mp4')
            {
                mp4_ok = true;
            }
            else if($(this).val() == '.jpg')
            {
                jpg_ok = true;
            }
        });
        //And hide the blocks
        if(all_ok || mp3_ok)
        {
            $('#block_mp3').show();
        }
        if(all_ok || mp4_ok)
        {
            $('#block_mp4').show();
        }
        if(all_ok || jpg_ok)
        {
            $('#block_jpg').show();
        }
        
        //----------------------------------------------------------------------
        //Prepare the sliders
        rra.slider_time.slider({
            min: 0,
            max: 1000,
            slide: function(event, ui){
                rra.slider_time_slide(event, ui);
            }
        }).removeClass("ui-corner-all").addClass("ui-corner-top");
        
        //Set the handle now it exists
        rra.slider_time_handle = $('.ui-slider-handle', rra.slider_time);
        rra.slider_time_handle.attr('title', ' ');
        rra.slider_time_handle.tooltip({
            content: rra.format_displayed_time(rra.slider_time.slider('option', 'value')),
            position: {
                my: "center bottom", 
                at: "center top"
            },
            show: false,
            hide: false
        });
        
        rra.slider_crop.slider({
            range: true,
            min: 0,
            max: 1000,
            values: [ 0, 1000 ],
            slide: function( event, ui ) {
                rra.slider_crop_slide(event, ui);
            }
        }).removeClass("ui-corner-all").addClass("ui-corner-bottom");
        
        //Set the handle now it exists
        rra.slider_crop_handle1 = $('.ui-slider-handle', rra.slider_crop).first();
        rra.slider_crop_handle2 = $('.ui-slider-handle', rra.slider_crop).last();
        rra.slider_crop_handle1.attr('title', ' ');
        rra.slider_crop_handle2.attr('title', ' ');
        rra.slider_crop_handle1.tooltip({
            content: rra.format_displayed_time(rra.slider_crop.slider('option', 'value')),
            position: {
                my: "center bottom", 
                at: "center top"
            },
            show: false,
            hide: false
        });
        rra.slider_crop_handle2.tooltip({
            content: rra.format_displayed_time(rra.slider_crop.slider('option', 'value2')),
            position: {
                my: "center bottom", 
                at: "center top"
            },
            show: false,
            hide: false
        });
        
        //----------------------------------------------------------------------
        //Prepare the buttons
        rra.btn_record = $('#btn_record').button({
            icons: {
                primary: 'ui-icon-record'
            },
            text: false
        }).click(function(){
            $(this).toggleClass('checked');
            rra.btn_record_click($(this).hasClass('checked'));
        });
        /*.change(function(){
            rra.btn_record_click($(this).is(':checked'));
        });*/
        rra.btn_stop = $('#btn_stop').button({
            icons: {
                primary: 'ui-icon-stop'
            },
            text: false,
            disabled: true
        }).click(function(){
            rra.btn_stop_click();
        });
        rra.btn_play = $('#btn_play').button({
            icons: {
                primary: 'ui-icon-play'
            },
            text: false,
            disabled: true
        }).change(function(){
            rra.btn_play_click($(this).is(':checked'));
        });
        rra.btn_record_photo = $('#btn_record_photo').button({
            icons: {
                primary: 'ui-icon-record'
            },
            text: true,
            disabled: false
        }).click(function(){
            rra.btn_record_photo_click();
        });
        rra.btn_reset = $('#btn_reset').button({
            icons: {
                primary: 'ui-icon-reset'
            },
            text: true,
            disabled: true
        }).click(function(){
            rra.btn_reset_click();
        });
        rra.btn_save = $('#btn_save').button({
            icons: {
                primary: 'ui-icon-save'
            },
            text: true,
            disabled: true
        }).click(function(){
            rra.btn_save_click();
        });
        rra.btn_help = $('#btn_help a').button({
            text: true
        });
        rra.btn_settings = $('#btn_settings').button({
            icons: {
                primary: 'ui-icon-settings'
            },
            text: false,
            disabled: false
        }).click(function(){
            rra.btn_settings_click();
        });
        
        //----------------------------------------------------------------------
        
        //Events for the hiddent input change
        $('#time_begin').change(function()
        {
            rra.slider_crop_handle1.tooltip('option', 'content', rra.format_displayed_time($(this).val()));
        });
        $('#time_current').change(function()
        {
            rra.slider_time_handle.tooltip('option', 'content', rra.format_displayed_time($(this).val()));
        });
        $('#time_end').change(function()
        {
            rra.slider_crop_handle2.tooltip('option', 'content', rra.format_displayed_time($(this).val()));
        });
        
        //----------------------------------------------------------------------
        //Check if the flash object is loaded or not
        if(rra.videorecorder == null)
        {
            rra.videorecorder_ready();
        }
        rra.slider_time.slider('disable');
        rra.slider_crop.slider('disable');
        rra.btn_record.button('enable');
    });
}

//------------------------------------------------------------------------------

/**
 * Create a class that will contains all functions and data for the recording.
 */
function repository_capture()
{
    //The params
    this.videorecorder = null;
    this.total_time = 0;
    this.slider_time = $('#slider_time');
    this.slider_time_handle = null;
    this.slider_crop = $('#slider_crop');
    this.slider_crop_handle1 = null;
    this.slider_crop_handle2 = null;
    this.btn_record = null;
    this.btn_stop = null;
    this.btn_play = null;
    this.btn_record_photo = null;
    this.btn_reset = null;
    this.btn_save = null;
    this.btn_settings = null;
    this.btn_help = null;
    this.ipt_time = $('#ipt_time');
    this.audioonly = false;
    this.photoonly = false;
    this.to_init = false;
    
    /**
     * Init the audio interface
     **/
    this.audio_init = function()
    {
        this.audioonly = true;
        
        $('#record_toolbar').addClass('audioonly');
        
        //Set the height and width to the minimum to display the setting panel if needed
        $('#video_width').val(220);
        $('#video_height').val(140);
        
        this.video_init();
        $("#repo_upload_audioonly", parent_block).prop('checked', true);
    }
    /**
     * Init the photo interface
     **/
    this.photo_init = function()
    {
        this.photoonly = true;
        
        $('#record_toolbar').addClass('photoonly');
        
        //Set the height and width to the minimum to display the setting panel if needed
        $('#video_width').val($('#photo_width').val());
        $('#video_height').val($('#photo_height').val());
        
        this.video_init();
        $("#repo_upload_photoonly", parent_block).prop('checked', true);
    }
    /**
     * Init the video interface
     **/
    this.video_init = function()
    {
        var fps = $('#video_fps').val();
        var quality = $('#video_quality').val();
        var width = $('#video_width').val();
        var height = $('#video_height').val();
        
        //At the video init, hide the first choice menu
        $('#div_capture #div_record_choice').hide();

        //Display the video
        var block_video = $('#div_videorecorder');
        block_video.css('position', 'relative');
        block_video.css('top', 0);
        block_video.css('width', width);
        
        //Load the video recorder
        this.videorecorder_ready();
        if (this.videorecorder != undefined)
        {
            this.to_init = true;
            var w2 = width;
            var h2 = height;
            if (w2 < 220)
            {
                w2 = 220;
            }
            if (h2 < 140)
            {
                h2 = 140;
            }
            
            this.videorecorder.setRecordSize(width, height, fps, quality);
            $(this.videorecorder).attr('width', w2);
            $(this.videorecorder).attr('height', h2);
                
            //Event on the ready video recorder or on the init of timeout
            setTimeout('rra.videorecorder_init()', 500);
        }
    }
    
    /**
     * Call this function if a init is needed
     **/
    this.videorecorder_init = function(force)
    {
        if(force == null)
        {
            force = false;
        }
        if(this.videorecorder != undefined && (this.to_init || force == true))
        {
            this.to_init = false;
            this.videorecorder.initRecorder(this.audioonly, this.photoonly);
        }
    }
    
    /**
     * Run this function when player is ready
     **/
    this.videorecorder_ready = function()
    {
        if (this.videorecorder == undefined)
        {
            this.videorecorder = this.getFlashMovieObject('videorecorder');
            this.videorecorder_init();
        }
    }
    
    /**
     * Called this function when there is no hardware
     **/
    this.videorecorder_nohardware = function()
    {
        this.btn_record.button('disable');
        $('#record_no_harware').show();
    }
    
    /**
     * Return the flash object
     * @param movieName The name of the block
     * @return object The object
     */
    this.getFlashMovieObject = function(movieName)
    {
        var obj = null;
        if (window.document[movieName]) 
        {
            obj = window.document[movieName];
        }
        if (navigator.appName.indexOf("Microsoft Internet")==-1)
        {
            if (document.embeds && document.embeds[movieName])
                obj = document.embeds[movieName]; 
        }
        else // if (navigator.appName.indexOf("Microsoft Internet")!=-1)
        {
            obj = document.getElementById(movieName);
        }
        return obj;
    }
    
    /**
     * Get a time in millisecondes and return it in secondes with one decimal
     * @param time The time in seconds
     * @return The time formatted
     */
    this.format_displayed_time = function(time)
    {
        var t = Math.round(time/1000);
        var min = Math.floor(t/60);
        var sec = t-(min*60);
        if(sec < 10)
        {
            sec = '0'+sec;
        }
        return min+':'+sec;
    }
    
    /**
     * Event for slide on slider time
     * @param event The event object
     * @param ui The ui object (contains value)
     */
    this.slider_time_slide = function(event, ui)
    {
        //Set the current time input
        $('#time_current').val(ui.value).change();
    }
                
    /**
     * Event for slide on slider crop time
     * @param event The event object
     * @param ui The ui object (contains value)
     */
    this.slider_crop_slide = function(event, ui)
    {
        //Set the begin and end time input
        $('#time_begin').val( ui.values[0]).change();
        $('#time_end').val( ui.values[1]).change();
    }
    
    /**
     * Event on click on the record button
     * @param checked If the element is checked
     */
    this.btn_record_click = function(checked)
    {
        //If checked (so in recording, stop the record)
        if(checked)
        {
            this.btn_stop.button('enable');
            this.btn_play.button('disable');
            this.btn_save.button('disable');
            this.btn_settings.button('disable');
            this.videorecorder.startRecording();
            
            this.slider_time.slider('disable');
            this.slider_crop.slider('disable');
        }
        else
        {
            this.btn_stop.button('disable');
            this.btn_play.button('enable');
            this.btn_save.button('enable');
            this.btn_settings.button('enable');
            this.videorecorder.stopRecording();
            
            this.slider_time.slider('enable');
            this.slider_crop.slider('enable');
        }
    }
    
    /**
     * Event on click on the stop button
     */
    this.btn_stop_click = function()
    {
        //If record, then click on stop record
        if(this.btn_record.is(':checked'))
        {
            this.btn_record.click();
        }
        else if(this.btn_play.is(':checked'))//Else if play click en stop play
        {
            this.btn_play.click();
            
            this.slider_time.slider('value', this.slider_crop.slider('values')[0]);
            this.ipt_time.val(this.format_displayed_time(this.total_time));
            $('#time_current').val(this.slider_crop.slider('values')[0]).change();
        }
    }
    
    /**
     * Event on click on the play button
     * @param checked If the element is checked
     */
    this.btn_play_click = function(checked)
    {
        //If checked (so in playing, stop the play)
        if(checked)
        {
            var startTime = $('#time_begin').val();
            if($('#time_current').val() > $('#time_begin').val() && $('#time_current').val() < $('#time_end').val())
            {
                startTime = $('#time_current').val();
            }
            this.btn_stop.button('enable');
            this.btn_record.button('disable');
            this.btn_save.button('disable');
            this.btn_settings.button('disable');
            this.videorecorder.playVideo(startTime, $('#time_end').val());
        }
        else
        {
            this.btn_stop.button('disable');
            this.btn_record.button('enable');
            this.btn_save.button('enable');
            this.btn_settings.button('enable');
            this.videorecorder.stopVideo();
        }
    }
    
    /**
     * Event on click on the record_photo button
     */
    this.btn_record_photo_click = function()
    {
        this.videorecorder.startRecording();
        this.btn_reset.button('enable');
        this.btn_save.button('enable');
    }
    
    /**
     * Event on click on the save button
     */
    this.btn_reset_click = function()
    {
        this.videorecorder_init(true);
        this.btn_reset.button('disable');
        this.btn_save.button('disable');
    }
        
    /**
     * Event on click on the save button
     */
    this.btn_save_click = function()
    {
        this.btn_record.button('disable');
        this.btn_stop.button('disable');
        this.btn_play.button('disable');
        this.btn_record_photo.button('disable');
        this.btn_reset.button('disable');
        this.btn_save.button('disable');
        this.btn_settings.button('disable');
        this.slider_time.slider('disable');
        this.slider_crop.slider('disable');
        
        $('#div_video_conversion').fadeIn();
        $('#video_conversion').progressbar({
            value: 0
        });
        this.videorecorder.saveRecording($('#time_begin').val(), $('#time_end').val());
    }
    
    /**
     * Event on click on the settings button
     */
    this.btn_settings_click = function()
    {
        this.videorecorder.openSettings();
    }
    
    /**
     * Send the current time recorded to the element
     **/
    this.videorecorder_updateTimer = function(time)
    {
        this.ipt_time.val(this.format_displayed_time(time*1000));
    }
    
    /**
     * Set the total time of the new recording
     **/
    this.videorecorder_setTotalTime = function(time)
    {
        this.total_time = time*1000;
        this.slider_crop.slider('option', 'max', this.total_time);
        this.slider_crop.slider('option', 'values', [0, this.total_time]);
        $('#time_begin').val(0).change();
        $('#time_end').val(this.total_time).change();
        this.slider_time.slider('option', 'max', this.total_time);
        this.ipt_time.val(this.format_displayed_time(this.total_time));
    }
    
    /**
     * Called for each frame displayed
     * @param time The current time
     * @param totalLength The total time length
     **/
    this.videorecorder_playFrame = function(time, totalLength)
    {
        this.ipt_time.val(this.format_displayed_time(time)+' / ' + this.format_displayed_time(totalLength));
        this.slider_time.slider("value", time);
        $('#time_current').val(time).change();
    }
    
    /**
     * Called when the video ends
     **/
    this.videorecorder_endPlay = function()
    {
        this.btn_stop.click();
    }
    
    /**
     * Called after each frame converted
     **/
    this.videorecorder_updateConversion = function(length, pos)
    {
        $('#video_conversion').progressbar("option", "value", pos*100/length);
    }
    
    /**
     * Called after the audio is inserted in the zip
     **/
    this.videorecorder_addAudio = function()
    {
        $('#video_conversion').progressbar("option", "value", 100);
    }
    /**
     * Call to give the converted data from the flash
     **/
    this.videorecorder_sendFileData = function(filedata)
    {
        $("#repo_upload_file_data", parent_block).html(filedata);
        
        $("#repo_upload_file_setlicense", parent_block).val($('#setlicense').val());
        $("#repo_upload_file_setauthor", parent_block).val($('#setauthor').val());
        $("#repo_upload_file_saveas", parent_block).val($('#saveas').val());
        
        this.btn_record.button('enable');
        this.btn_play.button('enable');
        this.btn_record_photo.button('enable');
        this.btn_reset.button('enable');
        this.btn_save.button('enable');
        this.btn_settings.button('enable');
        this.slider_time.slider('enable');
        this.slider_crop.slider('enable');
        $('#div_video_conversion').fadeOut();
        //And save the file in moodle
        $('#fp-upload-btn', parent_block).click();
    }
}