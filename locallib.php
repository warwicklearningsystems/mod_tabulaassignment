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
require_once(dirname(__FILE__).'/db/DataModel.php'); 
require_once(dirname(__FILE__).'/db/DateCache.php');

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
      $emptyvalue = ""; 
      
      $url = 'https://tabula.warwick.ac.uk/api/v1/module/' . $modulecode . '/assignments?academicYear=' .$academicyear;
      
      $username = get_config('mod_tabulaassignment', 'apiusername');
      $password = get_config('mod_tabulaassignment', 'apipassword');
      $graceperiod = get_config('mod_tabulaassignment', 'apigraceperiod');   
      $currentdte = date('Y-m-d');
      $rangeMaxDte = date('Y-m-d', strtotime($currentdte .'+4 weeks'));
      $rangeMinDte = date('Y-m-d', strtotime($currentdte .'-4 weeks'));
           
      $curldata = download_file_content($url, array('Authorization' => 'Basic ' .
          (string)base64_encode( $username . ":" . $password )), false, true);
      
      if($curldata->status == 200) {
          $tabulaassignments = json_decode($curldata->results);
          
          if($tabulaassignments->success == true) {
              
              foreach($tabulaassignments->assignments as $assignment) {
                  if (!($assignment->openEnded)){
                      $closedt = \DateTime::createFromFormat(\DateTime::ISO8601, $assignment->closeDate);
                      $closingdte = $closedt->format('Y-m-d');
                      $openEnded = 0;
                  } else{
                      $openEnded = 1;
                  }              
                  
                  if (($openEnded) || ((!($openEnded)) && (($closingdte <= $rangeMaxDte) && ($closingdte >= $rangeMinDte) ))){
                      
                      $a = new DataModel();
                      
                      if (!(empty($assignment->module))){
                          $a->Code = $assignment->module->code;
                          $a->moduleName = $assignment->module->name;
                      }
                      
                      $a->graceperiod = $rangeMinDte;
                      
                      if (!(empty($assignment->sitsLinks))){
                          $a->moduleCode = $assignment->sitsLinks[0]->moduleCode;
                      }
                      
                      $a->id = $assignment->id;
                      $a->name = $assignment->name;
                      $a->studentUrl = $assignment->studentUrl;
                      
                      if ($assignment->openEnded){
                          $a->openEnded = 1;
                          $a->sortfield = get_end_of_term($currentdte, 1);
                      } else{
                          $a->openEnded = 0;
                      }
                                            
                      if (!($assignment->openEnded)){
                          $a->closeDate = $assignment->closeDate; 
                          $a->sortfield = $closingdte;
                      }
                      
                      $a->summaryUrl = $assignment->summaryUrl;
                      
                      if ((isset($assignment->submissionFormText)) || !(empty($assignment->submissionFormText))){
                          $a->submissionFormText = $assignment->submissionFormText;
                      }
                      else{
                          $a->submissionFormText = $emptyvalue;
                      }
                      
                      if ((isset($assignment->wordCountMax)) || !(empty($assignment->wordCountMax))){
                          $a->wordCountMax = $assignment->wordCountMax;
                      }
                      else {
                          $a->wordCountMax = '';
                      }
                      
                      $a->opened = $assignment->opened;
                      
                      if($assignment->closed){
                          $a->closed = $assignment->closed;
                          $a->sortfield = get_end_of_term($closingdte, 0);
                      } else{
                          $a->closed = 0;
                      }
                      
                      if ((isset($assignment->submissionFormText)) || !(empty($assignment->submissionFormText))){
                          $a->submissionFormText = $assignment->submissionFormText;
                      }
                      
                      if ((isset($assignment->fileAttachmentTypes)) || !(empty($assignment->fileAttachmentTypes))){
                          $a->fileAttachmentTypes = implode($assignment->fileAttachmentTypes,"; ");
                      } else{
                          $a->fileAttachmentTypes = "";
                      }
                      
                      if (!(empty($assignment->wordCountConventions))){
                          $a->wordCountConventions = $assignment->wordCountConventions;
                      }
                      
                      if (!(empty($assignment->wordCountMin))){
                          $a->wordCountMin = $assignment->wordCountMin;
                      }
                      
                      if (!(empty($assignment->wordCountMax))){
                          $a->wordCountMax = $assignment->wordCountMax;
                      }
                      
                      $a->summative = $assignment->summative;
                      $a->openDate = $assignment->openDate;
                      $a->graceperiod = $graceperiod;
                      $a->cache_expiry_date = set_cache_clear_date($currentdte);
                      $assignments[] = $a;
                }
            }
        }
    }
  }
  
  return $assignments;
}
