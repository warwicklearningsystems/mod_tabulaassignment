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
          'id'                  => $this->id,
          'classes'             => $this->classes,
          'displaytext'         => $this->displaytext,
          'title'               => $this->title,
          'studentUrl'          => $this->studentUrl,
          'duedate'             => $this->duedate,
          'summaryUrl'          => $this->summaryUrl,
          'submissionFormText'  => $this->submissionFormText,
          'imminentdue'         => $this->imminentdue,
          'openDate'            => $this->openDate,
          'closed'              => $this->closed,
          'wordCount'           => $this->wordCount,
          'fileAttachmentTypes' => $this->fileAttachmentTypes,
          'insertword'          => $this->insertword,
          'expired'             => $this->expired,
          'opendatelegend'      => $this->opendatelegend,
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
      if (!$tabulaassignment->opened){
          $css[] = 'dimmed';
      }   
      
      $currentdte = date('Y-m-d');
      $dterangemin = date('Y-m-d', strtotime($currentdte .'+7 days'));
      
      $this->id = 'TaskID' .$tabulaassignment->id;
      
      if ($tabulaassignment->summative == 1){
          $this->summative = " (Summative) ";
      } else{
          $this->summative = " (Formative) ";
      }
      
      if (!(empty($tabulaassignment->closeDate))){
          $dt = \DateTime::createFromFormat(\DateTime::ISO8601, $tabulaassignment->closeDate);
          $this->duedate = $dt->format('l jS \of F Y h:i A');
          $this->displaytext = $tabulaassignment->name .$this->summative;
          if ($tabulaassignment->closed == 1){
              $this->insertword = " - was due on ";
          } else{
              $this->insertword = " - due on ";
          }
          
      } else{
          $this->displaytext = $tabulaassignment->name .$this->summative;
          $this->insertword = " - open-ended ";
          $this->duedate = "";
      }
      
      $this->closed = $tabulaassignment->closed;
      
      if (($tabulaassignment->closed) && ($tabulaassignment->graceperiod > 0)){
          $dt = \DateTime::createFromFormat(\DateTime::ISO8601, $tabulaassignment->closeDate);
          $expiredate = $dt->format("Y/m/d H:i:s");
          $secondsToAdd = $tabulaassignment->graceperiod * (60 * 60);
          
          if ((strtotime($expiredate) + $secondsToAdd) <= (strtotime(date("Y-m-d H:i:s")))){
              $this->expired = 1;
          } else{
              $this->expired = 0;
          }
      } else{
          $this->expired = 0;
      }
      
      $this->imminentdue = $tabulaassignment->imminentdue;
      $this->classes = implode(' ', $css);
      $this->title = $tabulaassignment->name;
      $this->studentUrl = $tabulaassignment->studentUrl; 
      $this->summaryUrl = $tabulaassignment->summaryUrl;
      
      if (isset($tabulaassignment->submissionFormText)){
          $this->submissionFormText = $tabulaassignment->submissionFormText;
      } else{
          $this->submissionFormText = "";
      }
      
      if (!($tabulaassignment->openEnded)){
          if (($tabulaassignment->closeDate <= $dterangemin)){
              $this->imminentdue = 1;
          }
      } 
      
      if (isset($tabulaassignment->fileAttachmentTypes)){
          $this->fileAttachmentTypes = $tabulaassignment->fileAttachmentTypes;
      } else{
          $this->fileAttachmentTypes = "";
      }
      
      if (($tabulaassignment->openDate) < $currentdte){
          $this->opendatelegend = " Opened on";
      } else{
          $this->opendatelegend = " Opens on";
      }
      
      if (isset($tabulaassignment->openDate)){
         $dtopen = \DateTime::createFromFormat(\DateTime::ISO8601, $tabulaassignment->openDate);
         $this->openDate = $dtopen->format('l jS \of F Y');
      }
      
      if ((isset($tabulaassignment->wordCountMax)) || (isset($tabulaassignment->wordCountMax))){
          $this->wordCount = $tabulaassignment->wordCountMin .' - ' .$tabulaassignment->wordCountMax;
      }
      else{
          $this->wordCount = "";
      }
      
      $this->openEnded = $tabulaassignment->openEnded;
  }
}


class renderer extends \plugin_renderer_base {

  public function render_assignments(tabulaassignment $tabulaassignment) {
    $data = $tabulaassignment->export_for_template($this);
    return $this->render_from_template('mod_tabulaassignment/list-assignments', $data);
  }

}