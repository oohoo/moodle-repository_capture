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
require_once($CFG->dirroot . '/repository/lib.php');

/**
 * A repository plugin to allow user record audio or video and upload it in the 
 * Moodle file system
 */
class repository_capture extends repository
{

    private $mimetypes = array();
    private $repo_url;

    /**
     * Constructor
     *
     * @global stdClass $CFG
     * @global moodle_page $PAGE
     * @param int $repositoryid
     * @param stdClass $context
     * @param array $options
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array(), $readonly = 0)
    {
        global $CFG, $PAGE;

        parent::__construct($repositoryid, $context, $options, $readonly);

        $this->record_video = get_config('capture', 'record_video');
        if ($this->record_video == '')
        {
            $this->record_video = 1;
            set_config('record_video', $this->record_video, 'capture');
        }
        $this->record_audio = get_config('capture', 'record_audio');
        if ($this->record_audio == '')
        {
            $this->record_audio = 1;
            set_config('record_audio', $this->record_audio, 'capture');
        }
        $this->record_photo = get_config('capture', 'record_photo');
        if ($this->record_photo == '')
        {
            $this->record_photo = 1;
            set_config('record_photo', $this->record_photo, 'capture');
        }

        $this->record_ffmpeg = get_config('capture', 'record_ffmpeg');
        if ($this->record_ffmpeg == '')
        {
            $this->record_ffmpeg = 'ffmpeg';
            set_config('record_ffmpeg', $this->record_ffmpeg, 'capture');
        }
        $this->record_quality = get_config('capture', 'record_quality');
        if ($this->record_quality == '')
        {
            $this->record_quality = 70;
            set_config('record_quality', $this->record_quality, 'capture');
        }
        $this->record_fps = get_config('capture', 'record_fps');
        if ($this->record_fps == '')
        {
            $this->record_fps = 15;
            set_config('record_fps', $this->record_fps, 'capture');
        }

        $this->record_video = get_config('capture', 'record_video');
        $this->record_audio = get_config('capture', 'record_audio');
        $this->record_photo = get_config('capture', 'record_photo');
        $this->record_ffmpeg = get_config('capture', 'record_ffmpeg');
        $this->record_fps = get_config('capture', 'record_fps');
        $this->record_quality = get_config('capture', 'record_quality');
        $this->repo_url = $CFG->wwwroot . '/repository/capture';

        //Data file sent
        $this->datafile = optional_param('datafile', null, PARAM_BASE64);
    }

    /**
     * Print a upload form
     * @return array
     */
    public function print_login()
    {
        $ret = $this->get_listing();

        return $ret;
    }

    /**
     * Return a upload form
     * @global stdClass $CFG
     * @return array
     */
    public function get_listing($path = '', $page = '')
    {
        global $CFG;
        $ret = array();
        $ret['nologin'] = true;
        $ret['nosearch'] = true;
        $ret['norefresh'] = true;
        $ret['list'] = array();
        $ret['dynload'] = false;
        $ret['upload'] = array('label' => get_string('attachment', 'repository'), 'id' => 'repo-form');
        $ret['allowcaching'] = false; // indicates that result of get_listing() can be cached in filepicker.js

        return $ret;
    }

    /**
     * Tells how the file can be picked from this repository
     * @return int
     */
    public function supported_returntypes()
    {
        return FILE_INTERNAL;
    }

    /**
     * File types supported by url downloader plugin
     *
     * @return array
     */
    public function supported_filetypes()
    {
        //New context call from moodle 2.5
        if(class_exists('context_course'))
        {
            $context = context_course::instance(1);
        }
        else
        {
            $context = get_context_instance(CONTEXT_COURSE, 1);
        }
        
        $filetypes = array();
        if(has_capability("repository/capture:view", $context))
        {
            if ($this->record_video == 1 && has_capability("repository/capture:video", $context))
            {
                $filetypes[] = 'video';
            }
            if ($this->record_audio == 1 && has_capability("repository/capture:audio", $context))
            {
                $filetypes[] = 'audio';
            }
            if ($this->record_photo == 1 && has_capability("repository/capture:photo", $context))
            {
                $filetypes[] = 'image';
            }
        }
        return $filetypes;
    }

    /**
     * Customize the form in the repository settings
     * @param moodleform $mform The moodle form passed by reference
     * @param string $classname The class name of the form
     */
    public static function type_config_form($mform, $classname = 'repository')
    {
        parent::type_config_form($mform, $classname);
        $record_video = get_config('capture', 'record_video');
        if ($record_video == '')
        {
            $record_video = 1;
        }
        $record_audio = get_config('capture', 'record_audio');
        if ($record_audio == '')
        {
            $record_audio = 1;
        }
        $record_photo = get_config('capture', 'record_photo');
        if ($record_photo == '')
        {
            $record_photo = 1;
        }
        $record_ffmpeg = get_config('capture', 'record_ffmpeg');
        if ($record_ffmpeg == '')
        {
            $record_ffmpeg = 'ffmpeg';
        }
        $record_quality = get_config('capture', 'record_quality');
        if ($record_quality == '')
        {
            $record_quality = 70;
        }
        $record_fps = get_config('capture', 'record_fps');
        if ($record_fps == '')
        {
            $record_fps = 15;
        }

        $record_video_grp = array();
        $record_video_grp[] = &$mform->createElement('radio', 'record_video', null, get_string('radio_no', 'repository_capture'), 0);
        $record_video_grp[] = &$mform->createElement('radio', 'record_video', null, get_string('radio_yes', 'repository_capture'), 1);
        $mform->addGroup($record_video_grp, 'record_video', get_string('record_video', 'repository_capture'), "<br/>", false);
        $mform->addHelpButton('record_video', 'record_video', 'repository_capture');

        $record_audio_grp = array();
        $record_audio_grp[] = &$mform->createElement('radio', 'record_audio', null, get_string('radio_no', 'repository_capture'), 0);
        $record_audio_grp[] = &$mform->createElement('radio', 'record_audio', null, get_string('radio_yes', 'repository_capture'), 1);
        $mform->addGroup($record_audio_grp, 'record_audio', get_string('record_audio', 'repository_capture'), "<br/>", false);
        $mform->addHelpButton('record_audio', 'record_audio', 'repository_capture');

        $record_photo_grp = array();
        $record_photo_grp[] = &$mform->createElement('radio', 'record_photo', null, get_string('radio_no', 'repository_capture'), 0);
        $record_photo_grp[] = &$mform->createElement('radio', 'record_photo', null, get_string('radio_yes', 'repository_capture'), 1);
        $mform->addGroup($record_photo_grp, 'record_photo', get_string('record_photo', 'repository_capture'), "<br/>", false);
        $mform->addHelpButton('record_photo', 'record_photo', 'repository_capture');

        $mform->addElement('text', 'record_ffmpeg', get_string('record_ffmpeg', 'repository_capture'), array('size' => 100, 'maxlength' => 255));
        $mform->setDefault('record_ffmpeg', $record_ffmpeg);
        $mform->addHelpButton('record_ffmpeg', 'record_ffmpeg', 'repository_capture');

        $mform->addElement('text', 'record_quality', get_string('record_quality', 'repository_capture'), array('size' => 10, 'maxlength' => 3));
        $mform->setDefault('record_quality', $record_quality);
        $mform->addHelpButton('record_quality', 'record_quality', 'repository_capture');

        $mform->addElement('text', 'record_fps', get_string('record_fps', 'repository_capture'), array('size' => 10, 'maxlength' => 2));
        $mform->setDefault('record_fps', $record_fps);
        $mform->addHelpButton('record_fps', 'record_fps', 'repository_capture');

        $mform->setType('record_video', PARAM_INT);
        $mform->addRule('record_video', null, 'required');
        $mform->setType('record_audio', PARAM_INT);
        $mform->addRule('record_audio', null, 'required');
        $mform->setType('record_photo', PARAM_INT);
        $mform->addRule('record_photo', null, 'required');
        $mform->setType('record_ffmpeg', PARAM_TEXT);
        $mform->addRule('record_ffmpeg', null, 'required');
        $mform->setType('record_quality', PARAM_INT);
        $mform->addRule('record_quality', null, 'required');
        $mform->setType('record_fps', PARAM_INT);
        $mform->addRule('record_fps', null, 'required');
    }

    /**
     * Validate Admin Settings Moodle form
     *
     * @static
     * @param moodleform $mform Moodle form (passed by reference)
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $errors array of ("fieldname"=>errormessage) of errors
     * @return array array of errors
     */
    public static function type_form_validation($mform, $data, $errors)
    {
        if ($data['record_video'] === '')
        {
            $errors['record_video'] = get_string('err_required', 'form');
        }
        if ($data['record_audio'] === '')
        {
            $errors['record_audio'] = get_string('err_required', 'form');
        }
        if ($data['record_photo'] === '')
        {
            $errors['record_photo'] = get_string('err_required', 'form');
        }

        if ($data['record_ffmpeg'] == '')
        {
            $errors['record_ffmpeg'] = get_string('err_required', 'form');
        }
        else
        {
            //Check if the path is already executable
            if (!is_executable(str_replace('"', '', $data['record_ffmpeg'])))
            {
                if (PHP_OS == 'WINNT')
                {
                    $res = shell_exec("where {$data['record_ffmpeg']}");
                }
                else
                {
                    $res = shell_exec("which {$data['record_ffmpeg']}");
                }
                //If empty then error
                if ($res == '')
                {
                    $errors['record_ffmpeg'] = get_string('err_record_ffmpeg_exec', 'repository_capture');
                }
            }
        }

        if ($data['record_quality'] == '')
        {
            $errors['record_quality'] = get_string('err_required', 'form');
        }
        else if ($data['record_quality'] < 20 || $data['record_quality'] > 100)
        {
            $errors['record_quality'] = get_string('err_record_quality_range', 'repository_capture');
        }

        if ($data['record_fps'] == '')
        {
            $errors['record_fps'] = get_string('err_required', 'form');
        }
        else if ($data['record_fps'] < 10 || $data['record_fps'] > 60)
        {
            $errors['record_fps'] = get_string('err_record_fps_range', 'repository_capture');
        }
        return $errors;
    }

    /**
     * Names of the plugin settings
     *
     * @return array
     */
    public static function get_type_option_names()
    {
        return array('record_video', 'record_audio', 'record_photo', 'record_ffmpeg', 'record_quality', 'record_fps', 'pluginname');
    }

    /**
     * Generage the template of the form
     * @global stdClass $CFG
     * @global moodle_page $PAGE
     * @return string The form template
     */
    public function get_upload_template()
    {
        global $CFG, $PAGE;

        $repoUrl = $CFG->wwwroot . '/repository/capture/';

        $html = '';

        //Add this css condition because chrome bugs with visibility:hidden and iFrames...
        $html .= html_writer::tag('style', '.yui3-panel-hidden #repository_capture_iframe{display: none;}', array('type' => 'text/css'));

        //This iFrame contains the repository tool
        $html .= html_writer::tag('iframe', '', array('id' => 'repository_capture_iframe', 'name' => 'repository_capture_iframe', 'src' => $repoUrl . 'repository.php', 'style' => 'width:100%; height:99%; border:0;'));

        $html .= html_writer::start_tag('div', array('class' => 'fp-content-top', 'style' => 'display:none;'));
        $html .= html_writer::start_tag('form', array('method' => 'post'));
        $html .= html_writer::start_tag('table');
        $html .= html_writer::start_tag('tbody');
        $html .= html_writer::start_tag('tr', array('class' => 'fp-file-data'));
        $html .= html_writer::start_tag('td', array('class' => 'mdl-right'));
        $html .= html_writer::end_tag('td');
        $html .= html_writer::start_tag('td', array('class' => 'mdl-left'));
        $html .= html_writer::tag('textarea', '', array('type' => 'text', 'id' => 'repo_upload_file_data', 'name' => 'repo_upload_file_data'));
        $html .= html_writer::empty_tag('input', array('type' => 'text', 'id' => 'repo_upload_file_width', 'name' => 'repo_upload_file_width'));
        $html .= html_writer::empty_tag('input', array('type' => 'text', 'id' => 'repo_upload_file_height', 'name' => 'repo_upload_file_height'));
        $html .= html_writer::empty_tag('input', array('type' => 'checkbox', 'id' => 'repo_upload_audioonly', 'name' => 'repo_upload_audioonly', 'value' => 1));
        $html .= html_writer::empty_tag('input', array('type' => 'checkbox', 'id' => 'repo_upload_photoonly', 'name' => 'repo_upload_photoonly', 'value' => 1));
        $html .= html_writer::end_tag('td');
        $html .= html_writer::end_tag('tr');
        $html .= html_writer::start_tag('tr', array('class' => 'fp-file'));
        $html .= html_writer::start_tag('td', array('class' => 'mdl-right'));
        $html .= html_writer::end_tag('td');
        $html .= html_writer::start_tag('td', array('class' => 'mdl-left'));
        $html .= html_writer::empty_tag('input', array('type' => 'text', 'value' => 'repository_capture.mp4'));
        $html .= html_writer::end_tag('td');
        $html .= html_writer::end_tag('tr');
        $html .= html_writer::start_tag('tr', array('class' => 'fp-saveas'));
        $html .= html_writer::start_tag('td', array('class' => 'mdl-right'));
        $html .= html_writer::end_tag('td');
        $html .= html_writer::start_tag('td', array('class' => 'mdl-left'));
        $html .= html_writer::empty_tag('input', array('type' => 'text', 'id' => 'repo_upload_file_saveas'));
        $html .= html_writer::end_tag('td');
        $html .= html_writer::end_tag('tr');
        $html .= html_writer::start_tag('tr', array('class' => 'fp-setauthor'));
        $html .= html_writer::start_tag('td', array('class' => 'mdl-right'));
        $html .= html_writer::end_tag('td');
        $html .= html_writer::start_tag('td', array('class' => 'mdl-left'));
        $html .= html_writer::empty_tag('input', array('type' => 'text', 'id' => 'repo_upload_file_setauthor'));
        $html .= html_writer::end_tag('td');
        $html .= html_writer::end_tag('tr');
        $html .= html_writer::start_tag('tr', array('class' => 'fp-setlicense'));
        $html .= html_writer::start_tag('td', array('class' => 'mdl-right'));
        $html .= html_writer::end_tag('td');
        $html .= html_writer::start_tag('td', array('class' => 'mdl-left'));
        $html .= html_writer::empty_tag('select', array('id' => 'repo_upload_file_setlicense'));
        $html .= html_writer::end_tag('td');
        $html .= html_writer::end_tag('tr');
        $html .= html_writer::end_tag('tbody');
        $html .= html_writer::end_tag('table');
        $html .= html_writer::end_tag('form');
        $html .= html_writer::start_tag('div', array('class' => 'fp-upload-btn-div'));
        $html .= html_writer::tag('button', 'submit', array('class' => 'fp-upload-btn', 'id' => 'fp-upload-btn'));
        $html .= html_writer::end_tag('div'); //end fp-upload-btn-div
        $html .= html_writer::end_tag('div'); //end fp-content-top

        return $html;
    }

    /**
     * Process uploaded file
     * @return array|bool
     */
    public function upload($saveas_filename, $maxbytes)
    {
        global $USER, $CFG;


        $datafile = optional_param('repo_upload_file_data', '', PARAM_RAW);
        $itemid = optional_param('itemid', 0, PARAM_INT);
        $audioonly = optional_param('repo_upload_audioonly', false, PARAM_BOOL);
        $photoonly = optional_param('repo_upload_photoonly', false, PARAM_BOOL);
        $width = optional_param('record_width', 320, PARAM_INT);
        $height = optional_param('record_height', 240, PARAM_INT);
        $saveas = optional_param('title', 'video_' . str_replace(' ', '_', fullname($USER)), PARAM_TEXT);
        $license = optional_param('license', $CFG->sitedefaultlicense, PARAM_TEXT);
        $author = optional_param('author', '', PARAM_TEXT);
        $types = optional_param_array('accepted_types', '*', PARAM_RAW);
        $overwriteexisting = optional_param('overwrite', false, PARAM_BOOL);

        return $this->process_capture($itemid, $types, $overwriteexisting, $datafile, $saveas, $license, $author, $audioonly, $photoonly, $width, $height);
    }

    /**
     * Do the actual processing of the "uploaded file"
     * @param int $itemid optional the ID for this item within the file area
     * @param string $datafile The datafile content (zip file of the video)
     * @param string $types If overwrite existing file
     * @param boolean $overwriteexisting If overwrite existing file
     * @param bool $audioonly optional if true, convert in mp3 instead of video
     * @param bool $photoonly optional if true, convert in jpg instead of video
     * @param int $width optional The width of the video
     * @param int $height optional The height of the video
     * @return object containing details of the file uploaded
     */
    public function process_capture($itemid, $types, $overwriteexisting, $datafile, $saveas, $license, $author, $audioonly = false, $photoonly = false, $width = 320, $height = 240)
    {
        global $USER, $CFG;

        $fs = get_file_storage();
        //New context call from moodle 2.5
        if(class_exists('context_user'))
        {
            $context = context_user::instance($USER->id);
        }
        else
        {
            $context = get_context_instance(CONTEXT_USER, $USER->id);
        }

        //Get mime types
        if ((is_array($types) and in_array('*', $types)) or $types == '*')
        {
            $this->mimetypes = '*';
        }
        else
        {
            foreach ($types as $type)
            {
                $this->mimetypes[] = mimeinfo('type', $type);
            }
        }

        $record = new stdClass();
        $record->filearea = 'draft';
        $record->component = 'user';
        $record->filepath = '/';
        $record->itemid = $itemid;
        $record->license = $license;
        $record->author = $author;
        $extension = '.mp4';
        if ($audioonly)
        {
            $extension = '.mp3';
        }
        else if ($photoonly)
        {
            $extension = '.jpg';
        }

        $record->filename = clean_param($saveas . $extension, PARAM_FILE);
        $record->contextid = $context->id;
        $record->userid = $USER->id;

        $ds = DIRECTORY_SEPARATOR;

        //Check if the main folder in the the temp exists
        if (!is_dir($CFG->tempdir . $ds . 'capture'))
        {
            mkdir($CFG->tempdir . $ds . 'capture');
        }
        //Create the video
        $folder = $CFG->tempdir . $ds . 'capture' . $ds . time() . $ds;

        mkdir($folder);
        //Create the zip from the data
        file_put_contents($folder . 'video.zip', base64_decode($datafile));
        //Extract the zip
        $zip = new ZipArchive();
        $zip->open($folder . 'video.zip');
        $zip->extractTo($folder . 'video' . $ds);
        $zip->close();

        //Check if the path is already executable
        if (!is_executable(str_replace('"', '', $this->record_ffmpeg)))
        {
            $res = '';
            if (PHP_OS == 'WINNT')
            {
                $res = shell_exec("where {$this->record_ffmpeg}");
            }
            else
            {
                $res = shell_exec("which {$this->record_ffmpeg}");
            }
            //If empty then error
            if ($res == '')
            {
                throw new moodle_exception('err_record_ffmpeg_exec', 'repository_capture');
            }
        }
        //If it is a video
        if (!$audioonly && !$photoonly)
        {
            shell_exec("{$this->record_ffmpeg} -y -r {$this->record_fps} -f image2  -i \"{$folder}video{$ds}img%06d.jpg\" -i \"{$folder}video{$ds}audio.wav\"  -acodec aac -strict experimental \"{$folder}{$record->filename}\" 2>&1");
        }
        else if (!$photoonly)// Else it is a sound
        {
            shell_exec("{$this->record_ffmpeg} -y -i \"{$folder}video{$ds}audio.wav\" \"{$folder}{$record->filename}\" 2>&1");
        }
        else
        {
            //Picture mode, copy the only image in the result place
            copy($folder . 'video' . $ds . 'img000001.jpg', $folder . $record->filename);
        }
        
        //Check If file exists
        if(!is_file($folder . $record->filename) || !is_readable($folder . $record->filename))
        {
            throw new moodle_exception('err_record_file_not_exists', 'repository_capture');
        }

        //Check mime type
        if ($this->mimetypes != '*')
        {
            // check filetype
            $filemimetype = file_storage::mimetype($folder . $record->filename, $record->filename);
            if (!in_array($filemimetype, $this->mimetypes))
            {
                throw new moodle_exception('invalidfiletype', 'repository', '', get_mimetype_description(array('filename' => $folder . $record->filename)));
            }
        }

        //Check if the filename already exists
        if (repository::draftfile_exists($record->itemid, $record->filepath, $record->filename))
        {
            if ($overwriteexisting)
            {
                repository::delete_tempfile_from_draft($record->itemid, $record->filepath, $record->filename);
            }
            else
            {
                $existingfilename = $record->filename;
                $unused_filename = repository::get_unused_filename($record->itemid, $record->filepath, $record->filename);
                $record->filename = $unused_filename;
                $stored_file = $fs->create_file_from_pathname($record, $folder . $existingfilename);
                $event = array();
                $event['event'] = 'fileexists';
                $event['newfile'] = new stdClass;
                $event['newfile']->filepath = $record->filepath;
                $event['newfile']->filename = $unused_filename;
                $event['newfile']->url = moodle_url::make_draftfile_url($record->itemid, $record->filepath, $unused_filename)->out(false) . (!$audioonly && !$photoonly ? '?d=' . $width . 'x' . $height : '');

                $event['existingfile'] = new stdClass;
                $event['existingfile']->filepath = $record->filepath;
                $event['existingfile']->filename = $existingfilename;
                $event['existingfile']->url = moodle_url::make_draftfile_url($record->itemid, $record->filepath, $existingfilename)->out(false) . (!$audioonly && !$photoonly ? '?d=' . $width . 'x' . $height : '');
                return $event;
            }
        }
        //Create the file
        $stored_file = $fs->create_file_from_pathname($record, $folder . $record->filename);

        return array(
            'url' => moodle_url::make_draftfile_url($record->itemid, $record->filepath, $record->filename)->out(false) . (!$audioonly &&!$photoonly ? '?d=' . $width . 'x' . $height : ''),
            'id' => $record->itemid,
            'file' => $record->filename);
    }

    /**
     * Delete all files in the folder temp capture
     * @global stdClass $CFG
     * @return bool
     */
    public function cron()
    {
        global $CFG;

        $ds = DIRECTORY_SEPARATOR;

        //Delete all files in this folder
        if (is_dir($CFG->tempdir . $ds . 'capture'))
        {
            remove_dir($CFG->tempdir . $ds . 'capture', true);
        }

        return true;
    }

}

/**
 * Capture plugin cron task
 */
function repository_capture_cron()
{
    $instances = repository::get_instances(array('type' => 'capture'));
    foreach ($instances as $instance)
    {
        $instance->cron();
    }
}