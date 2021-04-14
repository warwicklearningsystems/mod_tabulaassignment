<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file keeps track of upgrades to the warwicklabel module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * @package    mod_warwicklabel
 * @copyright  2016 Your Name <your@email.address>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute warwicklabel upgrade from the given old version
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_tabulaassignment_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.
    
     /*
     * MOO-2202 changes to enable recycle bin functionality
     */
    if ($oldversion < 2021041300){
        $default = "";
        
        $table = new xmldb_table('tabulaassignment');
        $field = new xmldb_field('intro');
        $field1 = new xmldb_field('introformat');
        $field->set_attributes(XMLDB_TYPE_CHAR, '15', XMLDB_UNSIGNED, false, false, $default);
        $field1->set_attributes(XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, false, false, "0");
        if (!$dbman->field_exists($table, $field)){
            $dbman->add_field($table, $field);
            $dbman->add_field($table, $field1);
        }
    }

    return true;
}
