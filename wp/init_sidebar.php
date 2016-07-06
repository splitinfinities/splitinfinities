<?php
/**
 * Determines whether or not to display the sidebar based on an array of conditional tags or page components.
 *
 * If any of the is_* conditional tags or is_page_template(template_file) checks return true, the sidebar will NOT be displayed.
 *
 * @link http://roots.io/the-roots-sidebar/
 *
 * @param array list of conditional tags (http://codex.wordpress.org/Conditional_Tags)
 * @param array list of page components. These will be checked via is_page_template()
 *
 * @return boolean True will display the sidebar, False will not
 */
class NEIGHBORHOOD_Sidebar {
  private $conditionals;
  private $components;

  public $display = false;

  function __construct($conditionals = array(), $components = array()) {
    $this->conditionals = $conditionals;
    $this->components    = $components;

    $conditionals = array_map(array($this, 'check_conditional_tag'), $this->conditionals);
    $components    = array_map(array($this, 'check_page_template'), $this->components);

    if (in_array(true, $conditionals) || in_array(true, $components)) {
      $this->display = true;
    }
  }

  private function check_conditional_tag($conditional_tag) {
    $conditional_arg = is_array($conditional_tag) ? $conditional_tag[1] : false;
    $conditional_tag = $conditional_arg ? $conditional_tag[0] : $conditional_tag;

    if (function_exists($conditional_tag)) {
      return $conditional_arg ? $conditional_tag($conditional_arg) : $conditional_tag();
    } else {
      return false;
    }
  }

  private function check_page_template($page_template) {
    return is_page_template($page_template) || NEIGHBORHOOD_Wrapping::$base . '.php' === $page_template;
  }
}
