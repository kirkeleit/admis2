<?php
  class Saker_model extends CI_Model {

    var $SakStatus = array(0 => "Under behandling", 1 => "Ferdig behandlet");
    var $MoteType = array(1 => "Rådsmøte");

    function saksliste($filter = NULL) {
      $sql = "SELECT SakID,SaksAr,SaksNummer,DatoRegistrert,PersonID,(SELECT Fornavn FROM Personer WHERE (PersonID=s.PersonID) LIMIT 1) AS PersonNavn,Tittel,(SELECT COUNT(*) FROM SakNotater WHERE (SakID=s.SakID)) AS Notater,IF((SELECT COUNT(*) FROM SakNotater WHERE (SakID=s.SakID) AND (Notatstype=2)) > 0,1,0) AS StatusID,Tidsbehov FROM Saker s WHERE (DatoSlettet Is Null)";
      if ($filter != NULL) {
        if (isset($filter['StatusID'])) {
          $sql = $sql." HAVING (StatusID='".$filter['StatusID']."')";
        }
      }
      $sql = $sql." ORDER BY SaksAr,SaksNummer,DatoRegistrert";
      $rsaker = $this->db->query($sql);
      foreach ($rsaker->result_array() as $sak) {
        $sak['Status'] = $this->SakStatus[$sak['StatusID']];
        $saker[] = $sak;
        unset($sak);
      }
      if (isset($saker)) {
        return $saker;
      }
    }

    function sak($ID) {
      $rsaker = $this->db->query("SELECT SakID,SaksAr,SaksNummer,DatoRegistrert,PersonID,(SELECT Fornavn FROM Personer WHERE (PersonID=s.PersonID) LIMIT 1) AS PersonNavn,Tittel,Saksbeskrivelse,(SELECT COUNT(*) FROM SakNotater WHERE (SakID=s.SakID)) AS Notater,IF((SELECT COUNT(*) FROM SakNotater WHERE (SakID=s.SakID) AND (Notatstype=2)) > 0,1,0) AS StatusID,Tidsbehov FROM Saker s WHERE (SakID=".$ID.") ORDER BY SaksAr,SaksNummer,DatoRegistrert");
      foreach ($rsaker->result_array() as $sak) {
        $sak['Status'] = $this->SakStatus[$sak['StatusID']];
        return $sak;
      }
    }

    function lagresak($ID=null,$sak) {
      $sak['DatoEndret'] = date('Y-m-d H:i:s');
      if ($ID == null) {
        $sak['DatoRegistrert'] = $sak['DatoEndret'];
        $this->db->query($this->db->insert_string('Saker',$sak));
        $sak['SakID'] = $this->db->insert_id();
      } else {
        $this->db->query($this->db->update_string('Saker',$sak,'SakID='.$ID));
        $sak['SakID'] = $ID;
      }
      return $sak;
    }

    function slettsak($ID) {
      $this->db->query("UPDATE Saker SET DatoEndret=Now(),DatoSlettet=Now() WHERE SakID=".$ID." LIMIT 1");
    }

    function tildelsaksnummer($ID) {
      $this->db->query("UPDATE Saker SET SaksAr=Year(Now()) WHERE SakID=".$ID." LIMIT 1");
      $rsaker = $this->db->query("SELECT * FROM Saker WHERE (SaksAr=Year(Now()))");
      $nr = $rsaker->num_rows();
      $this->db->query("UPDATE Saker SET SaksNummer='".$nr."' WHERE SakID=".$ID." LIMIT 1");
      $this->postsaktoslack($ID);
    }

    function saksnotater($ID) {
      $rnotater = $this->db->query("SELECT NotatID,SakID,DatoRegistrert,PersonID,(SELECT Fornavn FROM Personer WHERE (PersonID=n.PersonID) LIMIT 1) AS PersonNavn,Notat,IF(Notatstype=2,'Vedtak','Notat') AS Notatstype FROM SakNotater n WHERE (SakID=".$ID.") ORDER BY DatoRegistrert");
      foreach ($rnotater->result_array() as $notat) {
        $notater[] = $notat;
      }
      if (isset($notater)) {
        return $notater;
      }
    }

    function lagrenotat($notat) {
      $this->db->query($this->db->insert_string('SakNotater',array('SakID' => $notat['SakID'], 'MoteID' => $notat['MoteID'], 'DatoRegistrert' => date('Y-m-d H:i:s'), 'DatoEndret' => date('Y-m-d H:i:s'), 'PersonID' => $notat['PersonID'], 'Notat' => $notat['Notat'], 'Notatstype' => $notat['Notatstype'])));
    }

    function referat($MøteID) {
      $rMøter = $this->db->query("SELECT MoteID,DatoPlanlagtStart,DatoPlanlagtSlutt,Lokasjon,MotetypeID,Deltakere FROM Moter WHERE (MoteID=".$MøteID.") LIMIT 1");
      $data['Møte'] = $rMøter->row_array();
      $rSaksnotater = $this->db->query("SELECT SakID FROM SakNotater WHERE (MoteID=".$MøteID.") GROUP BY SakID");
      foreach ($rSaksnotater->result_array() as $rSaksnotat) {
        $rSaker = $this->db->query("SELECT SakID,SaksAr,SaksNummer,Tittel,Saksbeskrivelse,DatoRegistrert,PersonID,(SELECT CONCAT(Fornavn,' ',Etternavn) FROM Personer WHERE (PersonID=s.PersonID) LIMIT 1) AS PersonNavn FROM Saker s WHERE (SakID=".$rSaksnotat['SakID'].") LIMIT 1");
        if ($rSak = $rSaker->row_array()) {
          $rNotater = $this->db->query("SELECT NotatID,DatoRegistrert,PersonID,Notat,Notatstype FROM SakNotater WHERE (SakID=".$rSak['SakID'].") ORDER BY DatoRegistrert ASC");
          $rSak['Saksnotater'] = $rNotater->result_array();
          $Saker[] = $rSak;
          unset($Sak);
        }
      }
      if (isset($data)) {
        $data['Saker'] = $Saker;
        return $data;
      }
    }

    function moter() {
      $rMøter = $this->db->query("SELECT MoteID,MotetypeID,DatoPlanlagtStart,DatoPlanlagtSlutt,Lokasjon FROM Moter WHERE (DatoSlettet Is Null) ORDER BY DatoPlanlagtStart ASC");
      foreach ($rMøter->result_array() as $Møte) {
        $Møte['Motetype'] = $this->MoteType[$Møte['MotetypeID']];
        $Møter[] = $Møte;
      }
      return $Møter;
    }

    function mote($ID) {
      $rmoter = $this->db->query("SELECT MoteID,MotetypeID,DatoPlanlagtStart,DatoPlanlagtSlutt,Lokasjon FROM Moter WHERE (MoteID=".$ID.") LIMIT 1");
      if ($mote = $rmoter->row_array()) {
        return $mote;
      }
    }

    function lagremote($ID=null,$data) {
      $data['DatoEndret'] = date('Y-m-d H:i:s');
      if ($ID == null) {
        $data['DatoRegistrert'] = $data['DatoEndret'];
        $this->db->query($this->db->insert_string('Moter',$data));
        $data['MoteID'] = $this->db->insert_id();
      } else {
        $this->db->query($this->db->update_string('Moter',$data,'MoteID='.$ID));
        $data['MoteID'] = $ID;
      }
      return $data;
    }

    function slettmote($ID) {
      $this->db->query("UPDATE Moter SET DatoEndret=Now(),DatoSlettet=Now() WHERE MoteID=".$ID." LIMIT 1");
    }

    function postsakslistetoslack() {
      $TotaltTidsbehov = 0;
      $tekst = "*Saksliste pr ".date("d.m.Y").":*\n";
      $rSaker = $this->db->query("SELECT SakID,SaksAr,SaksNummer,DatoRegistrert,Tittel,Saksbeskrivelse,Tidsbehov,IF((SELECT COUNT(*) FROM SakNotater WHERE (SakID=s.SakID) AND (Notatstype=2)) > 0,1,0) AS StatusID FROM Saker s ORDER BY DatoRegistrert ASC");
      foreach ($rSaker->result_array() as $Sak) {
        if (($Sak['StatusID'] == 0) and ($Sak['SaksAr'] > 0)) {
          $TotaltTidsbehov += $Sak['Tidsbehov'];
          $tekst .= ">*".$Sak['SaksAr']."/".$Sak['SaksNummer'].":* ".$Sak['Tittel']." (".$Sak['Tidsbehov']." minutter)\n";
        }
      }
      $tekst .= "Totalt tidsbehov: ".date('H:i',mktime(0,$TotaltTidsbehov));
      $data = "payload=" . json_encode(array(
        "text" => $tekst
      ));
      if (isset($data)) {
        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_URL, "https://hooks.slack.com/services/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");
        curl_setopt($ch, CURLOPT_URL, "https://hooks.slack.com/services/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");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
      }
    }

    function postsaktoslack($SakID) {
      $saker = $this->db->query("SELECT SakID,SaksAr,SaksNummer,DatoRegistrert,Tittel,Saksbeskrivelse FROM Saker WHERE (SakID=".$SakID.") LIMIT 1");
      if ($sak = $saker->row_array()) {
        $tekst = "_Ny sak til behandling:_\n";
        $tekst .= ">*".$sak['SaksAr']."/".$sak['SaksNummer'].": ".$sak['Tittel']."*\n";
        $tekst .= ">>>".$sak['Saksbeskrivelse']."\n";
        $data = "payload=" . json_encode(array(
          "text" => $tekst
        ));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://hooks.slack.com/services/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");
        //curl_setopt($ch, CURLOPT_URL, "https://hooks.slack.com/services/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");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
      }
    }

  }
