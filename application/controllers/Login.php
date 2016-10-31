<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  class Login extends CI_Controller {

    public function index() {
      redirect('/login/auth');
    }

    public function auth() {
      if ($this->input->post('Brukernavn') and $this->input->post('Passord')) {
        $brukere = $this->db->query("SELECT PersonID FROM Brukere WHERE (Brukernavn='".$this->input->post('Brukernavn')."') AND (Passord=MD5('".$this->input->post('Passord')."')) LIMIT 1");
        if ($bruker = $brukere->row()) {
          $this->db->query("UPDATE Brukere SET DatoSistInnlogget=Now() WHERE PersonID=".$bruker->PersonID." LIMIT 1");
          $this->session->set_userdata('PersonID',$bruker->PersonID);

          $BrukerUAP = array('100');
          $roller = $this->db->query("SELECT UAP FROM BrukerRoller WHERE (RolleID=4) LIMIT 1");
          foreach ($roller->result() as $rolle) {
            $UAPs = unserialize($rolle->UAP);
            foreach ($UAPs as $UAP) {
              if (!in_array($UAP, $BrukerUAP)) {
                array_push($BrukerUAP,$UAP);
              }
            }
          }
          unset($roller);
          $roller = $this->db->query("SELECT UAP FROM PersonXRoller,BrukerRoller WHERE (PersonXRoller.PersonID=".$bruker->PersonID.") AND (PersonXRoller.RolleID=BrukerRoller.RolleID)");
          foreach ($roller->result() as $rolle) {
            $UAPs = unserialize($rolle->UAP);
            foreach ($UAPs as $UAP) {
              if (!in_array($UAP, $BrukerUAP)) {
                array_push($BrukerUAP,$UAP);
              }
            }
          }
          unset($roller);
          $this->session->set_userdata('UAP',$BrukerUAP);
          if ($this->input->post('GotoURL')) {
            redirect(urldecode($this->input->post('GotoURL')));
          } else {
            redirect('/');
          }
        }
      } else {
        $this->load->view('login');
      }
    }

    public function logout() {
      $this->session->sess_destroy();
      redirect('/login/auth');
    }

  }
