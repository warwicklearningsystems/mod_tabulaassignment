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
 * @package mod_tabulaassignment
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that will be used by the backup_tabulaassignment_activity_task
 */

/**
 * Define the complete tabulaassignment structure for backup, with file and id annotations
 */
class backup_tabulaassignment_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // MOO 2202 Define each element separated
        $tabulaassignment = new backup_nested_element('tabulaassignment', array('id'), array(
            'name', 'modulecode', 'timecreated', 'timemodified', 'assignmentuuid', 'intro', 'introformat'));

        // MOO 2202 Define sources
        $tabulaassignment->set_source_table('tabulaassignment', array('id' => backup::VAR_ACTIVITYID));

        // Define file annotations
        $tabulaassignment->annotate_files('mod_tabulaassignment', 'intro', null); // This file area hasn't itemid

        // Return the root element (tabulaassignment), wrapped into standard activity structure
        return $this->prepare_activity_structure($tabulaassignment);
    }
}
