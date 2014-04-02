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
defined('MOODLE_INTERNAL') || die();

/**
 * Execute upgrade from the given old version
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_repository_capture_upgrade($oldversion)
{
    global $DB;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

    if ($oldversion < 2012121201)
    {
        //Updated language files
        upgrade_plugin_savepoint(true, 2012121201, 'repository', 'capture');
    }
    
    if ($oldversion < 2012121301)
    {
        //Updated language files
        upgrade_plugin_savepoint(true, 2012121301, 'repository', 'capture');
    }
    
    if ($oldversion < 2012121700)
    {
        //Corrections on the config form
        upgrade_plugin_savepoint(true, 2012121700, 'repository', 'capture');
    }
    
    if ($oldversion < 2013072200)
    {
        //Patch the problem with chrome speed audio recording
        upgrade_plugin_savepoint(true, 2013072200, 'repository', 'capture');
    }
    
    if ($oldversion < 2014040200)
    {
        //Update Moodle 2.6
        upgrade_plugin_savepoint(true, 2014040200, 'repository', 'capture');
    }

    return true;
}
