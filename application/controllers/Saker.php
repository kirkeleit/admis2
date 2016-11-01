<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  class Saker extends CI_Controller {

    public function index() {
      redirect('/saker/saksliste');
    }

    public function saksliste() {
      $this->load->model('Saker_model');
      $data['Saksliste'] = $this->Saker_model->saksliste(array('StatusID' => '0'));
      $this->template->load('standard','saker/saksliste',$data);
    }

    public function sakslistetoslack() {
      $this->load->model('Saker_model');
      $this->Saker_model->postsakslistetoslack();
      redirect('/saker/saksliste');
    }

    public function saksarkiv() {
      $this->load->model('Saker_model');
      $data['Saksliste'] = $this->Saker_model->saksliste(array('StatusID' => '1'));
      $this->template->load('standard','saker/saksliste',$data);
    }

    public function nysak() {
      $this->load->model('Kontakter_model');
      $data['Sak'] = null;
      $data['Personer'] = $this->Kontakter_model->medlemmer();
      $this->template->load('standard','saker/sak',$data);
    }

    public function sak() {
      $this->load->model('Saker_model');
      $this->load->model('Kontakter_model');
      if ($this->input->post('SakLagre')) {
        $ID = $this->input->post('SakID');
        $data['PersonID'] = $this->input->post('PersonID');
        $data['Tittel'] = $this->input->post('Tittel');
        $data['Saksbeskrivelse'] = $this->input->post('Saksbeskrivelse');
        $data['Tidsbehov'] = $this->input->post('Tidsbehov');
        $sak = $this->Saker_model->lagresak($ID,$data);
        redirect('/saker/sak/'.$sak['SakID']);
      } elseif ($this->input->post('SakSlett')) {
        $this->Saker_model->slettsak($this->input->post('SakID'));
        redirect('/saker/saksliste');
      } elseif ($this->input->post('NotatLagre')) {
        $data['SakID'] = $this->input->post('SakID');
        $data['MoteID'] = 0;
        $data['Notatstype'] = $this->input->post('Notatstype');
        $data['Notat'] = $this->input->post('Notat');
        $data['PersonID'] = $this->session->userdata('PersonID');
        $this->Saker_model->lagrenotat($data);
        redirect('/saker/sak/'.$this->input->post('SakID'));
      } elseif ($this->input->post('TildelSaksnummer')) {
        $this->Saker_model->tildelsaksnummer($this->input->post('SakID'));
        redirect('/saker/sak/'.$this->input->post('SakID'));
      } else {
        $data['Sak'] = $this->Saker_model->sak($this->uri->segment(3));
        $data['Notater'] = $this->Saker_model->saksnotater($this->uri->segment(3));
        $data['Personer'] = $this->Kontakter_model->medlemmer();
        $this->template->load('standard','saker/sak',$data);
      }
    }

    public function referat() {
      $this->load->model('Saker_model');
      $data['MøteID'] = $this->uri->segment(3);
      $data['Referat'] = $this->Saker_model->referat($data['MøteID']);
      $this->template->load('utskrift','saker/referat',$data);
    }

    public function moter() {
      $this->load->model('Saker_model');
      $data['Møter'] = $this->Saker_model->moter();
      $this->template->load('standard','saker/moter',$data);
    }

    public function mote() {
      $this->load->model('Saker_model');
      if ($this->input->post('MoteLagre')) {
        $ID = $this->input->post('MoteID');
        $data['DatoPlanlagtStart'] = date('Y-m-d H:i',strtotime($this->input->post('DatoPlanlagt')." ".$this->input->post('DatoPlanlagtStart')));
        $data['DatoPlanlagtSlutt'] = date('Y-m-d H:i',strtotime($this->input->post('DatoPlanlagt')." ".$this->input->post('DatoPlanlagtSlutt')));
        $data['Lokasjon'] = $this->input->post('Lokasjon');
        $data['MotetypeID'] = $this->input->post('MotetypeID');
        $data = $this->Saker_model->lagremote($ID,$data);
        redirect('/saker/mote/'.$data['MoteID']);
      } elseif ($this->input->post('MoteSlett')) {
        $this->Saker_model->slettmote($this->input->post('MoteID'));
        redirect('/saker/moter');
      } else {
        $data['Mote'] = $this->Saker_model->mote($this->uri->segment(3));
        $this->template->load('standard','saker/mote',$data);
      }
    }


    public function moteskjerm() {
      $this->load->model('Saker_model');
      if ($this->input->post('LagreNotat')) {
        $data['SakID'] = $this->input->post('SakID');
        $data['MoteID'] = $this->input->post('MoteID');
        $data['Notatstype'] = $this->input->post('Notatstype');
        $data['Notat'] = $this->input->post('Notat');
        $data['PersonID'] = $this->session->userdata('PersonID');
        $this->Saker_model->lagrenotat($data);
        unset($data);
      } elseif ($this->input->post('MøteLagre')) {
        $data['Deltakere'] = $this->input->post('Deltakere');
        $this->Saker_model->lagremote($this->input->get('mid'),$data);
        unset($data);
      } elseif ($this->input->post('MøteStart')) {
        $data['DatoStart'] = date('Y-m-d H:i:s');
        $this->Saker_model->lagremote($this->input->get('mid'),$data);
        unset($data);
      } elseif ($this->input->post('MøteSlutt')) {
        $data['DatoSlutt'] = date('Y-m-d H:i:s');
        $this->Saker_model->lagremote($this->input->get('mid'),$data);
        unset($data);
      }
      $data['Mote'] = $this->Saker_model->mote($this->input->get('mid'));
      $data['Saksliste'] = $this->Saker_model->saksliste(array('StatusID' => '0'));
      if ($this->input->get('sid')) {
        $data['Sak'] = $this->Saker_model->sak($this->input->get('sid'));
        $data['Notater'] = $this->Saker_model->saksnotater($this->input->get('sid'));
      }
      $this->load->view('saker/moteskjerm',$data);
    }

  }
