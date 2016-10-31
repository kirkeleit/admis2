<?php
  class Autentisering extends CI_Controller {
    var $UABruker;
    var $CI;

    function __construct(){
        $this->CI =& get_instance();
    }

    function InitAutentisering() {
      $this->CI =& get_instance();
      if (!$this->CI->session->userdata('PersonID') and ($this->CI->uri->segment(1) != 'login')) {
        redirect('/login/auth/?gotourl='.urlencode($this->CI->uri->uri_string()));
      }
    }

  }
?>
