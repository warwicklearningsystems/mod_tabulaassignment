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
 * The main warwicklabel configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_warwicklabel
 * @copyright  2016 Your Name <your@email.address>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form
 *
 * @package    mod_warwicklabel
 * @copyright  2016 Your Name <your@email.address>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_tabulaassignment_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG, $COURSE;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));
        
        //MOO-2202 metadata loading
        require_once(dirname(__FILE__).'/db/metadata.php');
        
        /*MOO-2140 changes to include default module code*/
        $defaultcodes = tabulaassignment_get_default_code($COURSE->id);
        
        $moduleCode = substr($defaultcodes->moduleCode, 0, 5);     
        
        $autopopulateoptions = array(
                0 => get_string('no'),
                1 => get_string('yes'),
            );
        $mform->addElement('select', 'defaultcodes', get_string('defaultcodes', 'tabulaassignment'), $autopopulateoptions);
        $mform->addHelpButton('defaultcodes', 'defaultcodes', 'tabulaassignment');
        
        $str = 'autoupdate';

        // Adding the standard "name" field.
        $options = ['size' => 5, 'maxlength' => 6, 'pattern'=>"[A-Za-z]{2}[A-Za-z0-9]{3}", 'title'=>"Please enter the course code as in AANNN", 'required'];
        $mform->addElement('text', 'modulecode', get_string('modulecode', 'tabulaassignment'), $options);
        $mform->setType('modulecode', PARAM_ALPHANUMEXT);
        $mform->addHelpButton('modulecode', 'modulecode', 'tabulaassignment');
        
        /*MOO-2140 changes to include default module code */
        /*MOO-2202 Changes centralizing defaultdata as an instance of metadata*/
        if (((is_null($defaultcodes->moduleCode))) || (!(isset($defaultcodes)))){
            $mform->setDefault('autoupdate',1);
            $mform->setDefault('defaultcodes', 0);
        } else{
            $mform->setDefault('defaultcodes', 1);
            $mform->setDefault('modulecode', $defaultcodes->moduleCode);
        }
        /*MOO-2140 changes to default module code: disable the field if defaultcode exists */
        $mform->disabledIf('modulecode', 'defaultcodes', 'eq', 1);

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons(true, false);

    }
}