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
 * Url module admin settings and defaults
 *
 * @package    mod_tabulaassignment
 * @copyright  2018 Learning Support Systems, University of Warwick
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

if ($ADMIN->fulltree) {
    require_once("$CFG->libdir/resourcelib.php");
    
    $settings->add(new admin_setting_configtext('mod_tabulaassignment/apigraceperiod',
                                    get_string('hoursgrace', 'tabulaassignment'),
                                    '', 48, PARAM_INT));
    
    $settings->add( new admin_setting_configtext('mod_tabulaassignment/apiusername',
      get_string('tabula_username', 'mod_tabulaassignment'),
      get_string('tabula_username_desc', 'mod_tabulaassignment'), '') );

    $settings->add( new admin_setting_configpasswordunmask('mod_tabulaassignment/apipassword',
      get_string('tabula_password',  'mod_tabulaassignment'),
      get_string('tabula_password_desc',  'mod_tabulaassignment'), '') );
}
