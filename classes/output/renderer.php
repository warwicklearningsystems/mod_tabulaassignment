<?php

namespace mod_tabulaassignment\output;

defined('MOODLE_INTERNAL') || die;

require_once(dirname(__FILE__) . '/../../locallib.php');


abstract class list_item implements \renderable, \templatable {

  /** @var string CSS for the list item link */
  public $classes;
  /** @var string Display text for the list item link */
  public $displaytext;
  /** @var string Text to display when the list item link is hovered */
  public $title;
  /** @var string Link to the Course index page */
  public $url;

  /**
   * An abstract constructor
   * Decendant classes should define the class properties.
   *
   * @param mixed $object A object from which to derive the class properties
   */
  abstract public function __construct($object);

  /**
   * Export the object data for use by a template
   *
   * @param renderer_base $output A renderer_base object
   * @return array $data Template-ready data
   */
  public function export_for_template(\renderer_base $output) {

      $data = array(
        'classes'             => $this->classes,
        'displaytext'         => $this->displaytext,
        'title'               => $this->title,
        'url'                 => $this->url,
        'duedate'             => $this->duedate,
        'summaryUrl'          => $this->summaryUrl,
        'submissionFormText'  => $this->submissionFormText,
        'wordCountMax'        => $this->wordCountMax,
        'openDate'            => $this->openDate,
        'wordCountMax'        => $this->wordCountMax,
      );
    
    return $data;
  }
}

class tabulaassignment extends list_item implements \templatable, \renderable {

  /**
   * Constructor
   * Defines class properties for course link list items.
   *
   * @param \stdClass $course A moodle course object
   */
  public function __construct($tabulaassignment) {
    //$css[] = 'list-group-item';
    $css[] = '';

    if (!$tabulaassignment->opened) {
      $css[] = 'dimmed';
    }          
        if (!$tabulaassignment->ongoingAssignment){
            $dt = \DateTime::createFromFormat(\DateTime::ISO8601, $tabulaassignment->closeDate);
            $this->duedate = $dt->format('Y-m-d');
            $this->displaytext = $tabulaassignment->name . " due on " . $this->duedate;
        } else{
            $this->displaytext = $tabulaassignment->name . " (On going Assignment) ";
            $this->duedate = "Open Ended";
        }
        
        $this->classes = implode(' ', $css);
        $this->title = $tabulaassignment->name;
        $this->url = $tabulaassignment->studentUrl; 
        $this->summaryUrl = $tabulaassignment->summaryUrl;
        $this->submissionFormText = $tabulaassignment->submissionFormText;
        $this->wordCountMax = $tabulaassignment->wordCountMax;
        if (isset($tabulaassignment->openDate)){
            $dtopen = \DateTime::createFromFormat(\DateTime::ISO8601, $tabulaassignment->openDate);
            $this->openDate = $dtopen->format('Y-m-d');
        }
        $this->wordCountMax = $tabulaassignment->wordCountMax;

  }
}


class renderer extends \plugin_renderer_base {

  public function render_assignments(tabulaassignment $tabulaassignment) {
    $data = $tabulaassignment->export_for_template($this);
    return $this->render_from_template('mod_tabulaassignment/list-assignments', $data);
  }

}