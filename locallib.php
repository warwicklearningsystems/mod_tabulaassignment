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
 * Internal library of functions for module warwicklabel
 *
 * All the warwicklabel specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod_warwicklabel
 * @copyright  2016 Your Name <your@email.address>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/filelib.php');

/**
 * Retrieves assignment data from Tabula API
 *
 * @param string Module code
 * @return object information on assignments
 */
function get_tabula_assignment_data($modulecode) {

  $assignments = array();

  // https://warwick.ac.uk/services/its/servicessupport/web/tabula/api/coursework/assignments/list-assignments

  // id - uuid
  // academicYear - academic year code
  // name - Name
  // studentURL - Student URL for submissions
  // openDate - open date
  // closeDate - close date
  // opened - opened? true/false
  // closed - closed? true/false
  // openEnded - open ended assignment

  if($modulecode != '') {

      $academicyear = current_academic_year();
      
    $url = 'https://tabula.warwick.ac.uk/api/v1/module/' . $modulecode . '/assignments?academicYear=' .$academicyear;

    $username = get_config('mod_tabulaassignment', 'apiusername');
    $password = get_config('mod_tabulaassignment', 'apipassword');

    $curldata = download_file_content($url, array('Authorization' => 'Basic ' .
      (string)base64_encode( $username . ":" . $password )), false, true);
    
    $assignmentfound = false;
    
    if($curldata->status == 200) {
        
        $tabulaassignments = json_decode($curldata->results);
        
        if($tabulaassignments->success == true) {
            $currentdte = date('Y-m-d');
            $defaultdte = date('Y-m-d', strtotime($currentdte .'+4 weeks'));
            $ongoingAssessment = 1;
            
            $assignmentfound = true;
            $emptyvalue = "N/A";
            
            foreach($tabulaassignments->assignments as $assignment) {
                if (!(empty($assignment->closeDate))){
                    $ongoingAssessment = 0;
                    $closedt = \DateTime::createFromFormat(\DateTime::ISO8601, $assignment->closeDate);
                    $closingdte = $closedt->format('Y-m-d');
                } else{
                    $ongoingAssessment = 1;
                }
                
                if ((($ongoingAssessment == 0) && ($closingdte <= $defaultdte) )|| ($ongoingAssessment == 1)){
                    $a = new stdClass();
                    $a->id = $assignment->id;
                    $a->name = $assignment->name;
                    $a->studentUrl = $assignment->studentUrl;
                    $a->ongoingAssignment = 1;
                    $a->ongoingAssignment = $ongoingAssessment;
                    if (!(empty($assignment->closeDate))){
                        $a->closeDate = $assignment->closeDate;
                        $a->ongoingAssignment = 0;                      
                    }
                    $a->opened = $assignment->opened;
                    $a->summaryUrl = $assignment->summaryUrl;
                    if ((isset($assignment->submissionFormText)) || !(empty($assignment->submissionFormText))) {
                        $a->submissionFormText = $assignment->submissionFormText;                       
                    } else{
                        $a->submissionFormText = $emptyvalue;                     
                    }
                    
                    if ((isset($assignment->wordCountMax)) || !(empty($assignment->wordCountMax))){
                        $a->wordCountMax = $assignment->wordCountMax;                        
                    } else {
                        $a->wordCountMax = 'Unspecified';                      
                    }
                    $a->openDate = $assignment->openDate;
                    
                    $assignments[] = $a;
                }
            }
        }
      
    }

  }


  return $assignments;
}