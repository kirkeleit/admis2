<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  class Penger extends CI_Controller {

    public function index() {
      redirect('/penger/resultat');
    }

    public function resultat() {
      $this->load->model('Penger_model');
      $data['Aktiviteter'] = $this->Penger_model->aktiviteter();
      $data['Resultat'] = $this->Penger_model->resultat();
      $this->template->load('standard','penger/resultat',$data);
    }

    public function budsjett() {
      $this->load->model('Penger_model');
      if ($this->input->post('BudsjettLagre')) {
        //$this->Penger_model->lagrebudsjett($this->input->post());
      }
      $data['Aktiviteter'] = $this->Penger_model->aktiviteter();
      $data['KontoerINN'] = $this->Penger_model->kontoer(0);
      $data['KontoerUT'] = $this->Penger_model->kontoer(1);
      $data['Budsjett'] = $this->Penger_model->budsjett(2016);
      $this->template->load('standard','penger/budsjett',$data);
    }

    public function utgifter() {
      $this->load->model('Penger_model');
      $data['Aktiviteter'] = $this->Penger_model->aktiviteter();
      $data['Kontoer'] = $this->Penger_model->kontoer(1);

      if ($this->input->post('UtgiftFilter')) {
        if ($this->input->post('FilterAr')) {
          $filter['Ar'] = $this->input->post('FilterAr');
        }
        if ($this->input->post('FilterManed')) {
          $filter['Maned'] = $this->input->post('FilterManed');
        }
        if ($this->input->post('FilterAktivitetID')) {
          $filter['AktivitetID'] = $this->input->post('FilterAktivitetID');
        }
        if ($this->input->post('FilterKontoID')) {
          $filter['KontoID'] = $this->input->post('FilterKontoID');
        }
      } else {
        $filter['Ar'] = date('Y');
      }
      $data['Utgifter'] = $this->Penger_model->utgifter($filter);
      $this->template->load('standard','penger/utgifter',$data);
    }

  }
