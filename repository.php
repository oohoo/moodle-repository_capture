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
require_once(dirname(__FILE__) . '../../../config.php');
require_once($CFG->dirroot . '/repository/capture/lib.php');

/**
 * Display the content of the page
 * @global stdClass $CFG
 * @global moodle_database $DB
 * @global core_renderer $OUTPUT
 * @global moodle_page $PAGE
 * @global stdClass $USER
 */
function display_page()
{
    // CHECK And PREPARE DATA
    global $CFG, $OUTPUT, $PAGE, $DB, $USER;

    require_login(1, true);

    // Set the principal parameters
    if(class_exists('context_course'))
    {
        $context = context_course::instance(1);
    }
    else
    {
        $context = get_context_instance(CONTEXT_COURSE, 1);
    }

    $PAGE->set_pagelayout('embedded');
    $PAGE->set_context($context);

    //Moodle 2.5 JQUERY condition
    if (!method_exists(get_class($PAGE->requires), 'jquery'))
    {
        $PAGE->requires->js(new moodle_url('http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js'));
        $PAGE->requires->js(new moodle_url('http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js'));
        $PAGE->requires->css(new moodle_url('http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/themes/base/jquery-ui.css'));
    }
    else
    {
        $PAGE->requires->jquery();
        $PAGE->requires->jquery_plugin('ui');
        $PAGE->requires->jquery_plugin('ui-css');
    }
    $PAGE->requires->js('/repository/capture/js/capture.js');
    $PAGE->requires->css('/repository/capture/css/capture.css');

    //Set page parameters
    $urlpage = "$CFG->wwwroot/repository/capture/repositiory.php";
    $titlepage = get_string('head_index', 'repository_capture');
    $record_video = get_config('capture', 'record_video');
    $record_audio = get_config('capture', 'record_audio');
    $record_photo = get_config('capture', 'record_photo');

    //**********************
    //*** DISPLAY HEADER ***
    //**********************
    $PAGE->set_url($urlpage);
    $PAGE->set_title($titlepage);
    $PAGE->set_heading($titlepage);

    //Params
    $repoUrl = $CFG->wwwroot . '/repository/capture/';
    $urlFlash = $repoUrl . 'flash/videorecorder.swf?prefixjs=rra.';

    $html = '';

    $html .= html_writer::start_tag('div', array('id' => 'div_capture', 'class' => 'fp-upload-form mdl-align'));

    //Init Block - Choice recording audio or video
    $html .= html_writer::start_tag('div', array('id' => 'div_record_choice'));

    $html .= html_writer::start_tag('div', array('class' => 'block_intro ui-corner-all'));
    $html .= html_writer::tag('h3', get_string('title_info', 'repository_capture') . $OUTPUT->help_icon('title_info', 'repository_capture'));
    $html .= html_writer::tag('span', get_string('saveas', 'repository_capture'), array('class' => 'label_info'));
    $html .= html_writer::empty_tag('input', array('id' => 'saveas', 'type' => 'text', 'value' => get_string('default_filename', 'repository_capture'), 'class' => 'ui-corner-all'));
    $html .= html_writer::empty_tag('br');
    $html .= html_writer::tag('span', get_string('setauthor', 'repository_capture'), array('class' => 'label_info'));
    $html .= html_writer::empty_tag('input', array('id' => 'setauthor', 'type' => 'text', 'class' => 'ui-corner-all'));
    $html .= html_writer::empty_tag('br');
    $html .= html_writer::tag('span', get_string('setlicense', 'repository_capture'), array('class' => 'label_info'));
    $html .= html_writer::tag('select', '', array('id' => 'setlicense', 'class' => 'ui-corner-all'));
    $html .= html_writer::end_tag('div'); //End 

    if ($record_audio == 1 && has_capability("repository/capture:audio", $context))
    {
        $html .= html_writer::start_tag('div', array('id' => 'block_mp3', 'class' => 'block_intro ui-corner-all', 'style' => 'display:none;'));
        $html .= html_writer::tag('h3', get_string('title_audio', 'repository_capture') . $OUTPUT->help_icon('title_audio', 'repository_capture'));
        $html .= html_writer::tag('button', get_string('btn_audio', 'repository_capture'), array('onclick' => 'rra.audio_init();'));
        $html .= html_writer::end_tag('div'); //End 
    }
    if ($record_video == 1 && has_capability("repository/capture:video", $context))
    {
        $html .= html_writer::start_tag('div', array('id' => 'block_mp4', 'class' => 'block_intro ui-corner-all', 'style' => 'display:none;'));
        $html .= html_writer::tag('h3', get_string('title_video', 'repository_capture') . $OUTPUT->help_icon('title_video', 'repository_capture'));
        $html .= html_writer::empty_tag('input', array('id' => 'video_fps', 'type' => 'hidden', 'value' => get_config('capture', 'record_fps')));
        $html .= html_writer::empty_tag('input', array('id' => 'video_quality', 'type' => 'hidden', 'value' => get_config('capture', 'record_quality')));
        $html .= get_string('video_width', 'repository_capture');
        $html .= html_writer::empty_tag('input', array('id' => 'video_width', 'name' => 'video_width', 'type' => 'text', 'value' => 320, 'style' => 'width:40px', 'maxlength' => 4, 'class' => 'ui-corner-all', 'autocomplete' => 'off'));
        $html .= get_string('video_height', 'repository_capture');
        $html .= html_writer::empty_tag('input', array('id' => 'video_height', 'name' => 'video_height', 'type' => 'text', 'value' => 240, 'style' => 'width:40px', 'maxlength' => 4, 'class' => 'ui-corner-all', 'autocomplete' => 'off'));
        $html .= html_writer::empty_tag('br');
        $html .= html_writer::tag('button', get_string('btn_video', 'repository_capture'), array('onclick' => 'rra.video_init();'));
        $html .= html_writer::end_tag('div'); //End 
    }
    if ($record_photo == 1 && has_capability("repository/capture:photo", $context))
    {
        $html .= html_writer::start_tag('div', array('id' => 'block_jpg', 'class' => 'block_intro ui-corner-all', 'style' => 'display:none;'));
        $html .= html_writer::tag('h3', get_string('title_photo', 'repository_capture') . $OUTPUT->help_icon('title_photo', 'repository_capture'));
        $html .= get_string('video_width', 'repository_capture');
        $html .= html_writer::empty_tag('input', array('id' => 'photo_width', 'name' => 'photo_width', 'type' => 'text', 'value' => 320, 'style' => 'width:40px', 'maxlength' => 4, 'class' => 'ui-corner-all', 'autocomplete' => 'off'));
        $html .= get_string('video_height', 'repository_capture');
        $html .= html_writer::empty_tag('input', array('id' => 'photo_height', 'name' => 'photo_height', 'type' => 'text', 'value' => 240, 'style' => 'width:40px', 'maxlength' => 4, 'class' => 'ui-corner-all', 'autocomplete' => 'off'));
        $html .= html_writer::empty_tag('br');
        $html .= html_writer::tag('button', get_string('btn_photo', 'repository_capture'), array('onclick' => 'rra.photo_init();'));
        $html .= html_writer::end_tag('div'); //End 
    }
    $html .= html_writer::end_tag('div'); //end div_record_choice
    //
    //Block recorder
    $html .= html_writer::start_tag('div', array('id' => 'div_videorecorder', 'class' => 'ui-corner-all', 'style' => 'position:absolute;top: -100000px'));

    $html .= html_writer::tag('div', get_string('err_no_hardware', 'repository_capture'), array('id' => 'record_no_harware', 'class' => 'ui-corner-all ui-state-error', 'style' => 'display:none;'));

    $flash_attrs = array(
        "type" => "application/x-shockwave-flash",
        "data" => $urlFlash,
        "width" => 320,
        "height" => 240,
        "name" => "videorecorder",
        "id" => "videorecorder",
        "style" => "outline: none;"
    );
    $html .= html_writer::start_tag('object', $flash_attrs);
    $html .= html_writer::empty_tag('param', array("name" => "allowScriptAccess", "value" => "always"));
    $html .= html_writer::empty_tag('param', array("name" => "allowFullScreen", "value" => "true"));
    $html .= html_writer::empty_tag('param', array("name" => "wmode", "value" => "direct"));
    $html .= html_writer::empty_tag('param', array("name" => "movie", "value" => $urlFlash));
    $html .= html_writer::empty_tag('param', array("name" => "quality", "value" => "high"));
    $html .= html_writer::end_tag('object');

    //Add the sliders control
    $html .= html_writer::start_tag('div', array('id' => 'record_toolbar', 'class' => 'ui-corner-all'));
    $html .= html_writer::empty_tag('input', array('id' => 'time_begin', 'type' => 'hidden', 'autocomplete' => 'off'));
    $html .= html_writer::empty_tag('input', array('id' => 'time_current', 'type' => 'hidden', 'autocomplete' => 'off'));
    $html .= html_writer::empty_tag('input', array('id' => 'time_end', 'type' => 'hidden', 'autocomplete' => 'off'));
    $html .= html_writer::tag('div', '', array('id' => 'slider_time'));
    $html .= html_writer::tag('div', '', array('id' => 'slider_crop'));
    $html .= html_writer::empty_tag('input', array('id' => 'btn_record', 'type' => 'checkbox', 'autocomplete' => 'off'));
    $html .= html_writer::tag('label', get_string('btn_record', 'repository_capture'), array('for' => 'btn_record', 'id' => 'btn_record_l'));
    $html .= html_writer::tag('button', get_string('btn_stop', 'repository_capture'), array('id' => 'btn_stop'));
    $html .= html_writer::empty_tag('input', array('id' => 'btn_play', 'type' => 'checkbox', 'autocomplete' => 'off'));
    $html .= html_writer::tag('label', get_string('btn_play', 'repository_capture'), array('for' => 'btn_play', 'id' => 'btn_play_l'));
    $html .= html_writer::empty_tag('input', array('id' => 'ipt_time', 'type' => 'text', 'readonly' => 'readonly', 'class' => 'ui-corner-all', 'autocomplete' => 'off'));
    $html .= html_writer::tag('button', get_string('btn_record_photo', 'repository_capture'), array('id' => 'btn_record_photo'));
    $html .= html_writer::tag('button', get_string('btn_reset', 'repository_capture'), array('id' => 'btn_reset'));
    $html .= html_writer::tag('button', get_string('btn_save', 'repository_capture'), array('id' => 'btn_save'));
    $html .= html_writer::tag('div', $OUTPUT->help_icon('btn_help', 'repository_capture'), array('id' => 'btn_help'));
    $html .= html_writer::tag('button', get_string('btn_settings', 'repository_capture'), array('id' => 'btn_settings'));
    $html .= html_writer::start_tag('div', array('id' => 'div_video_conversion', 'style' => 'display:none;'));
    $html .= html_writer::tag('div', get_string('video_conversion_processing', 'repository_capture'), array('id' => 'txt_video_conversion'));
    $html .= html_writer::tag('div', '', array('id' => 'video_conversion'));
    $html .= html_writer::end_tag('div'); //End div_video_conversion
    $html .= html_writer::end_tag('div'); //End record_toolbar

    $html .= html_writer::end_tag('div'); //End div_videorecorder

    $html .= html_writer::end_tag('div'); //End div_capture
    //**********************
    //*** DISPLAY HEADER ***
    //**********************

    echo $OUTPUT->header();

    //***********************
    //*** DISPLAY CONTENT ***
    //***********************

    echo $OUTPUT->box($html, 'center clearfix ');

    //**********************
    //*** DISPLAY FOOTER ***
    //**********************
    echo $OUTPUT->footer();
}

display_page();