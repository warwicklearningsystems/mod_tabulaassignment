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
 * Library of interface functions and constants for module tabulaassignment
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 *
 * All the tabulaassignment specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod_tabulaassignment
 * @copyright  2016 Your Name <your@email.address>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__) . '/classes/output/renderer.php');


/**
 * Example constant, you probably want to remove this :-)
 */
define('tabulaassignment_ULTIMATE_ANSWER', 42);

/* Moodle core API */

/**
 * Returns the information on whether the module supports a feature
 *
 * See {@link plugin_supports()} for more info.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function tabulaassignment_supports($feature) {

    switch($feature) {
        case FEATURE_MOD_INTRO:               return false;
        case FEATURE_SHOW_DESCRIPTION:        return false;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_BACKUP_MOODLE2:          return false;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_NO_VIEW_LINK:            return true; // critical to stop display of link to resource
        case FEATURE_IDNUMBER:                return false;
        default:                              return null;
    }
}

/**
 * Saves a new instance of the tabulaassignment into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $tabulaassignment Submitted data from the form in mod_form.php
 * @param mod_tabulaassignment_mod_form $mform The form instance itself (if needed)
 * @return int The id of the newly inserted tabulaassignment record
 */
function tabulaassignment_add_instance(stdClass $tabulaassignment, mod_tabulaassignment_mod_form $mform = null) {
    global $DB;

  $tabulaassignment->timecreated = time();

    // You may have to add extra stuff in here.

  $tabulaassignment->id = $DB->insert_record('tabulaassignment', $tabulaassignment);

  return $tabulaassignment->id;
}

/**
 * Updates an instance of the tabulaassignment in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $tabulaassignment An object from the form in mod_form.php
 * @param mod_tabulaassignment_mod_form $mform The form instance itself (if needed)
 * @return boolean Success/Fail
 */
function tabulaassignment_update_instance(stdClass $tabulaassignment, mod_tabulaassignment_mod_form $mform = null) {
    global $DB;

    $tabulaassignment->timemodified = time();
    $tabulaassignment->id = $tabulaassignment->instance;

    // You may have to add extra stuff in here.

    $result = $DB->update_record('tabulaassignment', $tabulaassignment);

    return $result;
}

/**
 * This standard function will check all instances of this module
 * and make sure there are up-to-date events created for each of them.
 * If courseid = 0, then every tabulaassignment event in the site is checked, else
 * only tabulaassignment events belonging to the course specified are checked.
 * This is only required if the module is generating calendar events.
 *
 * @param int $courseid Course ID
 * @return bool
 */
function tabulaassignment_refresh_events($courseid = 0) {
    global $DB;

    if ($courseid == 0) {
        if (!$tabulaassignments = $DB->get_records('tabulaassignment')) {
            return true;
        }
    } else {
        if (!$tabulaassignments = $DB->get_records('tabulaassignment', array('course' => $courseid))) {
            return true;
        }
    }

    foreach ($tabulaassignments as $tabulaassignment) {
        // Create a function such as the one below to deal with updating calendar events.
        // tabulaassignment_update_events($tabulaassignment);
    }

    return true;
}

/**
 * Removes an instance of the tabulaassignment from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function tabulaassignment_delete_instance($id) {
    global $DB;

    if (! $tabulaassignment = $DB->get_record('tabulaassignment', array('id' => $id))) {
        return false;
    }

    // Delete any dependent records here.

    $DB->delete_records('tabulaassignment', array('id' => $tabulaassignment->id));

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 *
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @param stdClass $course The course record
 * @param stdClass $user The user record
 * @param cm_info|stdClass $mod The course module info object or record
 * @param stdClass $tabulaassignment The tabulaassignment instance record
 * @return stdClass|null
 */
function tabulaassignment_user_outline($course, $user, $mod, $tabulaassignment) {

    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * It is supposed to echo directly without returning a value.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $tabulaassignment the module instance record
 */
function tabulaassignment_user_complete($course, $user, $mod, $tabulaassignment) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in tabulaassignment activities and print it out.
 *
 * @param stdClass $course The course record
 * @param bool $viewfullnames Should we display full names
 * @param int $timestart Print activity since this timestamp
 * @return boolean True if anything was printed, otherwise false
 */
function tabulaassignment_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link tabulaassignment_print_recent_mod_activity()}.
 *
 * Returns void, it adds items into $activities and increases $index.
 *
 * @param array $activities sequentially indexed array of objects with added 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 */
function tabulaassignment_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@link tabulaassignment_get_recent_mod_activity()}
 *
 * @param stdClass $activity activity record with added 'cmid' property
 * @param int $courseid the id of the course we produce the report for
 * @param bool $detail print detailed report
 * @param array $modnames as returned by {@link get_module_types_names()}
 * @param bool $viewfullnames display users' full names
 */
function tabulaassignment_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 *
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * Note that this has been deprecated in favour of scheduled task API.
 *
 * @return boolean
 */
function tabulaassignment_cron () {
    return true;
}

/**
 * Returns all other caps used in the module
 *
 * For example, this could be array('moodle/site:accessallgroups') if the
 * module uses that capability.
 *
 * @return array
 */
function tabulaassignment_get_extra_capabilities() {
    return array();
}

/* Gradebook API */

/**
 * Is a given scale used by the instance of tabulaassignment?
 *
 * This function returns if a scale is being used by one tabulaassignment
 * if it has support for grading and scales.
 *
 * @param int $tabulaassignmentid ID of an instance of this module
 * @param int $scaleid ID of the scale
 * @return bool true if the scale is used by the given tabulaassignment instance
 */
function tabulaassignment_scale_used($tabulaassignmentid, $scaleid) {
    global $DB;

    //if ($scaleid and $DB->record_exists('tabulaassignment', array('id' => $tabulaassignmentid, 'grade' => -$scaleid))) {
    //    return true;
    //} else {
        return false;
    //}
}

/**
 * Checks if scale is being used by any instance of tabulaassignment.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param int $scaleid ID of the scale
 * @return boolean true if the scale is used by any tabulaassignment instance
 */
function tabulaassignment_scale_used_anywhere($scaleid) {
    global $DB;

    //if ($scaleid and $DB->record_exists('tabulaassignment', array('grade' => -$scaleid))) {
    //    return true;
    //} else {
        return false;
    //}
}

/* File API */

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function tabulaassignment_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * File browsing support for tabulaassignment file areas
 *
 * @package mod_tabulaassignment
 * @category files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info instance or null if not found
 */
function tabulaassignment_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Serves the files from the tabulaassignment file areas
 *
 * @package mod_tabulaassignment
 * @category files
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the tabulaassignment's context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 */
function tabulaassignment_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options=array()) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);

    send_file_not_found();
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 * See get_array_of_activities() in course/lib.php
 *
 * @global object
 * @param object $coursemodule
 * @return cached_cm_info|null
 */
function tabulaassignment_get_coursemodule_info($coursemodule) {
    global $DB, $COURSE, $PAGE;

    if ($ta = $DB->get_record('tabulaassignment', array('id'=>$coursemodule->instance), 'id, name, modulecode, assignmentuuid')) {

        $info = new cached_cm_info();
        
        $lists = array();
        if(empty($ta->name)) {
          $ta->name = 'tabulaassignment{$ta->id}';
          $DB->set_field('tabulaassignment', 'name', $ta->name, array('id'=>$ta->id));
        }
        $info->name = $ta->name;
        $code = $ta->modulecode;
        
        //Moo-2045 retrieve current academic year
        $academicyear = current_academic_year(); 
              
        $cache = cache::make_from_params(cache_store::MODE_SESSION, 'tabulaassignment', 'tabulaassignment-list');
        $lists = $cache->get('cached_data');

        if (check_cache($cache, $code)){
            //cache exists, check if cache has expired...

            if(cache_expired($lists)){
                //if Cache expired purge cache and commit a new fetch from tabula to refresh... data may have changed.
                $cache->purge();
                $tabuladata = get_tabula_assignment_data($ta->modulecode, $academicyear);
                $lists = store_cache($tabuladata, $cache);
            }
            else{
            }
            $tabuladata = sort_data($lists);
            
        } else{ 
            $tabuladata = get_tabula_assignment_data($ta->modulecode, $academicyear);          
            $lists = store_cache($tabuladata, $cache);
            $tabuladata = sort_data($lists);
        }
                
        // Get assignment data from Tabula
        $output = $PAGE->get_renderer('mod_tabulaassignment');
        
        if ((!(empty($tabuladata))) && (sizeof($tabuladata)>0) ){
            $modulename = get_module_name($tabuladata);
        }        

        // Render assignment details
        $info->content = "<h5>Tabula assignments</h5><p>The following assignments are listed in Tabula for module " . $ta->modulecode ;

        if ((!(empty($tabuladata))) && (sizeof($tabuladata)>0)){
            $info->content .= ": " . "$modulename  ($academicyear ) </p>";
        } else {
            $info->content .= "</p>";
        }
        
        $contentempty = "<p style='color:#2647a0; font-style:italic; font-size: 0.8em'>There are no Assignments listed in Tabula for this module </p>";
        // Render each assignment
        if (!(empty($tabuladata))){
            if (sizeof($tabuladata)==0){
                $info->content .= $contentempty;
            }
        }
        
        if (!(empty($tabuladata))){
            foreach($tabuladata as $t){
                $asslink = new \mod_tabulaassignment\output\tabulaassignment($t);
                $info->content .= $output->render_assignments($asslink);
            }
        }

        return $info;
    } else {
        return null;
    }
}
/* Moo 2045 current_academic_year() returns current academic year
 * used as default in Locallib.php
 */
function current_academic_year(){
    $currentyear = date("y");
    $currentmonth = date("m");
    $nextyear = date("y")+1;
    $prevyear = date("y")-1;
    if ($currentmonth > 8){
        $currentyear = ("$currentyear" ."/" ."$nextyear");
    } else{
        $currentyear = ("$prevyear" ."/" ."$currentyear");
    }
    
    return $currentyear;
}

/*
 * MOO-2045 Store_cache() method introduced to store the data retrieved in cache.
 * the data can be retrieved from cache to minimize queries to the Tabula.
 */
function store_cache($tabuladata, $cache){
    global $DB, $COURSE, $PAGE;
    
    if (!(is_null($COURSE->idnumber))){
        $cache->set('cached_data',$tabuladata );
        return $cache->get('cached_data');
    } else{
        return $cache->get('cached_data');
    }  
}
/*
 * get_module_name() returns the name of the module 
 */
function get_module_name($tabuladata){
    if (!(empty($tabuladata))){
       return $tabuladata[0]->moduleName;
   }  
}

/*
 * MOO 2079 set_cache_clear_date() creates an instance of the object DateCache
 * populates the object with expiration date for cache
 * cache to expire at 04:00 following day
 */
function set_cache_clear_date($currentdte){

    require_once(dirname(__FILE__).'/db/DateCache.php');  

        $tasks = new DateCache();
        $tasks->dayofweek = date('l', strtotime( $currentdte));
        $tasks->day = date('d', strtotime( $currentdte))+1;
        $tasks->month = date('m', strtotime( $currentdte));  

    return $tasks;
}

/*
 * MOO-2079 validate_cache checks whether the cache available in store has expired or not.
 * if yes, the cache has expired (expiration is at 4 Am following day)
 */
function cache_expired($lists){
   
    $currentDte = date('Y-m-d H:i:s');
    
    foreach($lists as $itemList){
        
        $myStdClass = json_decode(json_encode($itemList->cache_expiry_date));
        $expiryDate = date("$myStdClass->day" .'-' ."$myStdClass->month" .'-' .date("Y") ." " .$myStdClass->hour .':00:00' );

        $diff = strtotime($expiryDate) - strtotime($currentDte);
        $hours = $diff / ( 60 * 60 );
        if ($hours > 1){
            return 0;
        }
        else{
            return 1;
        }

    }
}
/*
 * MOO 2079 Check-cache() to find if the module code is already in cache....
 * if in cache, pull data from cache. no need to run json.
 */
function check_cache($cache, $code){
    
    $lists = $cache->get('cached_data');
    $val = 0; 
    
    if (!(empty($lists))){
        foreach($lists as $datalist){
            if ($datalist->Code == $code)
                $val = 1;
        }
    } 
    return $val;
}

/*
 * MOO 2079 Convert the Array into a string. Implode will not work as Implode 
 * still will return an array.
 */
function subArraysToString($ar, $sep = ', ') {
    $str = '';
    foreach ($ar as $val) {
        $str .= implode($sep, $val);
        $str .= $sep; // add separator between sub-arrays
    }
    $str = rtrim($str, $sep); // remove last separator
    return $str;
} 

/*
 * MOO 2079 get_endofterm date. Method to determine last day of current term
 * to populate $sortfield which will only be used as a dummy date field only for sorting.
 * to ensure all expired items or closed assignments will move to the bottom   
 */
function get_end_of_term($date, $status){
    
    if (date('m', strtotime( $date)) >= 8){
        $year = date('Y', strtotime( $date))+1;
    } else {
        $year = date('Y', strtotime( $date));
    }   
    //MOO 2079 we alocate a dummy date of 1st August of the end of current academic year for all open ended items
    if ($status == 1){
        return (date_format(date_create($year ."-08-01"),"Y/m/d H:i:s"));
    } else {
        //not open ended assignments...we use a dummy date of 15th August of current academic year for any expired items
        return (date_format(date_create($year ."-08-15"),"Y/m/d H:i:s"));
    }  
}
/*MOO 2079  sort_data() function to sort data in ascending order, 
 * any expired assignments or assessments, should be placed at the end.
 * use a dummy sortfield entered as a date to facilitate the sort
 */
function sort_data($array){
    
    $ord = array();
    $vals = array();
    
    foreach ($array as $key => $value){
        $ord[] = strtotime($value->sortfield);
        $vals[] = strtotime($value->closeDate);
    }
    array_multisort($ord, SORT_ASC, $vals, SORT_DESC, $array);
    return $array;
}