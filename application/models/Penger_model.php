<?php
  class Penger_model extends CI_Model {

    function aktiviteter() {
      $resultat = $this->db->query("SELECT * FROM RegnskapsAktiviteter ORDER BY AktivitetID ASC");
      foreach ($resultat->result() as $rad) {
        $aktivitet['AktivitetID'] = $rad->AktivitetID;
        $aktivitet['Navn'] = $rad->Navn;
        $aktiviteter[] = $aktivitet;
        unset($aktivitet);
      }
      return $aktiviteter;
    }

    function kontoer($type = 0) {
      $resultat = $this->db->query("SELECT * FROM RegnskapsKontoer WHERE (Type=".$type.") ORDER BY KontoID ASC");
      foreach ($resultat->result() as $rad) {
        $konto['KontoID'] = $rad->KontoID;
        $konto['Navn'] = $rad->Navn;
        $konto['Beskrivelse'] = $rad->Beskrivelse;
        $konto['Type'] = $rad->Type;
        $kontoer[] = $konto;
        unset($konto);
      }
      return $kontoer;
    }

    function resultat($ar = 2016) {
      $kontoer = $this->db->query("SELECT * FROM RegnskapsKontoer ORDER BY KontoID ASC");
      foreach ($kontoer->result() as $konto) {
        $data[$konto->KontoID]['Navn'] = $konto->Navn;
        $data[$konto->KontoID]['Type'] = $konto->Type;
        $data[$konto->KontoID]['Bokfort'] = 0;
        $data[$konto->KontoID]['Budsjett'] = 0;

        $i = 0;
        $aktiviteter = $this->db->query("SELECT * FROM RegnskapsAktiviteter ORDER BY AktivitetID ASC");
        foreach ($aktiviteter->result() as $aktivitet) {
          if ($konto->Type == 0) {
            $resultat = $this->db->query("SELECT SUM(Belop) AS Belop FROM Inntekter WHERE (KontoID='".$konto->KontoID."') AND (AktivitetID='".$aktivitet->AktivitetID."') AND (Year(DatoBokfort) = ".$ar.")");
            if ($rad = $resultat->row()) {
              $data[$konto->KontoID][$i]['Bokfort'] = $rad->Belop;
              $data[$konto->KontoID]['Bokfort'] = $data[$konto->KontoID]['Bokfort'] + $rad->Belop;
            } else {
              $data[$konto->KontoID][$i]['Bokfort'] = 0;
            }
            $resultat2 = $this->db->query("SELECT SUM(Belop) AS Belop,(SUM(Belop)/12) AS Belop2 FROM Budsjett WHERE (KontoID='".$konto->KontoID."') AND (AktivitetID='".$aktivitet->AktivitetID."') AND (BudsjettAr = ".$ar.")");
            if ($rad2 = $resultat2->row()) {
              $data[$konto->KontoID][$i]['Budsjett'] = $rad2->Belop;
              $data[$konto->KontoID]['Budsjett'] = $data[$konto->KontoID]['Budsjett'] + $rad2->Belop;
              //$data[$konto->ID][$i]['BudsjettHIA'] = $rad2->Belop2 * date('m');
              //$data[$konto->ID][$i]['ResultatHIA'] = $rad->Belop - ($rad2->Belop2 * date('m'));
              $data[$konto->KontoID][$i]['Resultat'] = $rad->Belop - $rad2->Belop;
            }
          } elseif ($konto->Type == 1) {
            $resultat = $this->db->query("SELECT SUM(Belop) AS Belop FROM Utgifter WHERE (KontoID='".$konto->KontoID."') AND (AktivitetID='".$aktivitet->AktivitetID."') AND (Year(DatoBokfort) = ".$ar.")");
            if ($rad = $resultat->row()) {
              $data[$konto->KontoID][$i]['Bokfort'] = $rad->Belop;
              $data[$konto->KontoID]['Bokfort'] = $data[$konto->KontoID]['Bokfort'] + $rad->Belop;
            } else {
              $data[$konto->KontoID][$i]['Bokfort'] = 0;
              $data[$konto->KontoID]['Bokfort'] = 0;
            }
            $resultat2 = $this->db->query("SELECT SUM(Belop) AS Belop,(SUM(Belop)/12) AS Belop2 FROM Budsjett WHERE (KontoID='".$konto->KontoID."') AND (AktivitetID='".$aktivitet->AktivitetID."') AND (BudsjettAr = ".$ar.")");
            if ($rad2 = $resultat2->row()) {
              $data[$konto->KontoID][$i]['Budsjett'] = $rad2->Belop;
              $data[$konto->KontoID]['Budsjett'] = $data[$konto->KontoID]['Budsjett'] + $rad2->Belop;
              //$data[$konto->ID][$i]['BudsjettHIA'] = $rad2->Belop2 * date('m');
              //$data[$konto->ID][$i]['ResultatHIA'] = ($rad2->Belop2 * date('m')) - $rad->Belop;
              $data[$konto->KontoID][$i]['Resultat'] = $rad2->Belop - $rad->Belop;
            }
          }
          $i++;
        }
      }
      return $data;
    }

    function budsjett($ar = 2016) {
      $kontoer = $this->db->query("SELECT * FROM RegnskapsKontoer ORDER BY KontoID ASC");
      foreach ($kontoer->result() as $konto) {
        $aktiviteter = $this->db->query("SELECT * FROM RegnskapsAktiviteter ORDER BY AktivitetID ASC");
        foreach ($aktiviteter->result() as $aktivitet) {
          $resultat = $this->db->query("SELECT * FROM Budsjett WHERE (KontoID='".$konto->KontoID."') AND (AktivitetID='".$aktivitet->AktivitetID."') AND (BudsjettAr='".$ar."')");
          if ($rad = $resultat->row()) {
            $data[$konto->KontoID][$aktivitet->AktivitetID] = $rad->Belop;
          } else {
            $data[$konto->KontoID][$aktivitet->AktivitetID] = "";
          }
        }
      }
      return $data;
    }

    function utgifter($filter = NULL) {
      $sql = "SELECT * FROM Utgifter WHERE 1";
      if (isset($filter['Ar'])) {
        $sql = $sql." AND (Year(DatoBokfort)='".$filter['Ar']."')";
      }
      if (isset($filter['Maned'])) {
        $sql = $sql." AND (Month(DatoBokfort)='".$filter['Maned']."')";
      }
      if (isset($filter['AktivitetID'])) {
        $sql = $sql." AND (AktivitetID='".$filter['AktivitetID']."')";
      }
      if (isset($filter['KontoID'])) {
        $sql = $sql." AND (KontoID='".$filter['KontoID']."')";
      }
      $sql = $sql." ORDER BY DatoBokfort ASC";
      $resultat = $this->db->query($sql);
      foreach ($resultat->result() as $rad) {
        $utgift['UtgiftID'] = $rad->UtgiftID;
        $utgift['DatoRegistrert'] = $rad->DatoRegistrert;
        $utgift['DatoBokfort'] = $rad->DatoBokfort;
        $utgift['PersonID'] = $rad->PersonID;
        $personer = $this->db->query("SELECT * FROM Medlemmer,Personer WHERE (Medlemmer.PersonID=Personer.PersonID) AND (Personer.PersonID=".$rad->PersonID.") LIMIT 1");
        if ($person = $personer->row()) {
          $utgift['PersonInitialer'] = $person->Initialer;
          $utgift['Person'] = $person->Fornavn." ".$person->Etternavn;
        } else {
          $utgift['PersonInitialer'] = "&nbsp;";
          $utgift['Person'] = "&nbsp;";
        }
        unset($personer);
        $utgift['AktivitetID'] = $rad->AktivitetID;
        $aktiviteter = $this->db->query("SELECT * FROM RegnskapsAktiviteter WHERE (AktivitetID='".$rad->AktivitetID."')");
        if ($aktivitet = $aktiviteter->row()) {
          $utgift['Aktivitet'] = $aktivitet->Navn;
          unset($aktivitet);
        } else {
          $utgift['Aktivitet'] = "n/a";
        }
        unset($aktiviteter);
        $utgift['KontoID'] = $rad->KontoID;
        $kontoer = $this->db->query("SELECT * FROM RegnskapsKontoer WHERE (KontoID='".$rad->KontoID."')");
        if ($konto = $kontoer->row()) {
          $utgift['Konto'] = $konto->Navn;
          unset($konto);
        } else {
          $utgift['Konto'] = "n/a";
        }
        unset($kontoer);
        $utgift['InnkjopsordreID'] = $rad->InnkjopsordreID;
        $utgift['Beskrivelse'] = $rad->Beskrivelse;
        $utgift['Belop'] = $rad->Belop;
        $utgifter[] = $utgift;
        unset($utgift);
      }
      if (isset($utgifter)) {
        return $utgifter;
      }
    }

  }
