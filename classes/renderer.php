<?php

namespace mod_tabulaassignment\output;

defined('MOODLE_INTERNAL') || die;

require_once(dirname(__FILE__) . '/../locallib.php');


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
      'classes'     => $this->classes,
      'displaytext' => $this->displaytext,
      'title'       => $this->title,
      'url'         => $this->url,
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
    $css[] = 'fcl-course-link list-group-item';
    if (!$tabulaassignment->opened) {
      $css[] = 'dimmed';
    }
    $this->classes = implode(' ', $css);
    $filteroptions = array();
    $filteroptions['escape'] = false;
    $this->displaytext = $tabulaassignment->name;
    $this->title = $tabulaassignment->name;
    $this->url = $tabulaassignment->studentLink;
  }
}


class mod_tabulaassignment_assignments_renderer extends \plugin_renderer_base {

  protected function render_assignments(tabulaassignment $tabulaassignment) {
    $data = $tabulaassignment->export_for_template($this);
    return $this->render_from_template('mod_tabulaassignment/list_assignments', $data);
  }

}