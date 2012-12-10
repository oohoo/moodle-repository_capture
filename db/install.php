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

/**
 * Create a default instance of the Capture repository
 *
 * @return bool A status indicating success or failure
 */
function xmldb_repository_capture_install()
{
    global $CFG;
    $result = true;
    require_once($CFG->dirroot . '/repository/lib.php');
    $captureplugin = new repository_type('capture', array(), true);
    if (!$id = $captureplugin->create(true))
    {
        $result = false;
    }
    return $result;
}
