<?php
  class Kontakter_model extends CI_Model {

    function personer($filter=null) {
      $sql = "SELECT p.PersonID,Fornavn,Etternavn,Mobilnr,Epost,AdresseID,(SELECT Poststed FROM PostAdresser,PostNummer WHERE (PostAdresser.Postnummer=PostNummer.Postnummer) AND (AdresseID=p.AdresseID) LIMIT 1) As Poststed,TIMESTAMPDIFF(YEAR,DatoFodselsdato,CURDATE()) AS Alder,TIMESTAMPDIFF(YEAR,DatoMedlemsdato,CURDATE()) AS Medlemsar,(SELECT Navn FROM Medlemsgrupper WHERE (GruppeID=p.MedlemsgruppeID) LIMIT 1) AS MedlemsgruppeNavn FROM";
      if (isset($filter['OrganisasjonID'])) {
        $sql .= " Personer p,PersonXOrganisasjon WHERE (PersonXOrganisasjon.PersonID=p.PersonID) AND (PersonXOrganisasjon.OrganisasjonID=".$filter['OrganisasjonID'].")";
      } else {
        $sql .= " Personer p WHERE 1";
      }
      if (isset($filter['ErMedlem'])) {
        $sql = $sql." AND (Medlem=1)";
      }
      if (isset($filter['Fritekst'])) {
        $sql .= " AND ((Fornavn Like '%".$filter['Fritekst']."%') OR (Etternavn Like '%".$filter['Fritekst']."%') OR (Mobilnr Like '%".$filter['Fritekst']."%') OR (Epost Like '%".$filter['Fritekst']."%'))";
      }
      $sql = $sql." ORDER BY Fornavn, Etternavn";
      $rpersoner = $this->db->query($sql);
      foreach ($rpersoner->result_array() as $person) {
        $personer[] = $person;
        unset($person);
      }
      if (isset($personer)) {
        return $personer;
      }
    }

    function medlemmer($filter=null) {
      if ($filter == null) {
        return $this->personer(array('ErMedlem' => 1));
      } else {
        $filter['ErMedlem'] = 1;
        return $this->personer($filter);
      }
    }

    function person($ID) {
      $rpersoner = $this->db->query("SELECT PersonID,Fornavn,Etternavn,DatoFodselsdato,Mobilnr,Epost,AdresseID,Medlem,Initialer,Relasjonsnummer,DatoMedlemsdato,MedlemsgruppeID,KovaPrimKey,DeaktiverKovaSync FROM Personer WHERE (PersonID=".$ID.") LIMIT 1");
      if ($person = $rpersoner->row_array()) {
        $person['Adresser'] = $this->personadresser($person['PersonID']);
        $person['Medlemsgrupper'] = $this->medlemsgrupper($person['PersonID']);
        return $person;
      }
    }

    private function personadresser($ID) {
      $radresser = $this->db->query("SELECT PostAdresser.AdresseID,Adresse1,Adresse2,PostAdresser.Postnummer,Poststed FROM PostAdresseXPerson,PostAdresser,PostNummer WHERE (PostAdresser.AdresseID=PostAdresseXPerson.AdresseID) AND (PostAdresseXPerson.PersonID=".$ID.") AND (PostNummer.Postnummer=PostAdresser.Postnummer)");
      foreach ($radresser->result_array() as $adresse) {
        $adresser[] = $adresse;
        unset($adresse);
      }
      if (isset($adresser)) {
        return $adresser;
      }
    }

    public function geodata() {
      $radresser = $this->db->query("SELECT PersonID,(SELECT Fornavn FROM Personer WHERE (Personer.PersonID=xp.PersonID) LIMIT 1) AS Fornavn,Lengdegrad,Breddegrad FROM PostAdresser,PostAdresseXPerson xp WHERE (PostAdresser.AdresseID=xp.AdresseID) AND (Lengdegrad>0) AND (Breddegrad>0)");
      foreach ($radresser->result_array() as $adresse) {
        $adresser[] = $adresse;
        unset($adresse);
      }
      if (isset($adresser)) {
        return $adresser;
      }
    }

    function lagreperson($ID = null,$person) {
      $person['DatoEndret'] = date("Y-m-d H:i:s");
      if ($ID == null) {
        $person['DatoRegistrert'] = $person['DatoEndret'];
        $this->db->query($this->db->insert_string('Personer',$person));
        $person['PersonID'] = $this->db->insert_id();
      } else {
        $this->db->query($this->db->update_string('Personer',$person,'PersonID='.$ID));
        $person['PersonID'] = $ID;
      }
      if ($this->db->affected_rows() > 0) {
        $this->session->set_flashdata('Infomelding','Personen er oppdatert.');
      }
      return $person;
    }

    function lagreadresseperson($ID = null,$PersonID,$adresse) {
      if ($ID == null) {
        $this->db->query($this->db->insert_string('PostAdresser',$adresse));
        $adresse['AdresseID'] = $this->db->insert_id();
        $this->db->query($this->db->insert_string('PostAdresseXPerson',array('AdresseID'=>$adresse['AdresseID'],'PersonID'=>$PersonID)));
      } else {
        $this->db->query($this->db->update_string('PostAdresser',$adresse,'AdresseID='.$ID));
        $adresse['AdresseID'] = $ID;
      }
      $this->db->query($this->db->update_string('Personer',array('AdresseID'=>$adresse['AdresseID']),'PersonID='.$PersonID));
      return $adresse;
    }

    function lagreadresseorganisasjon($ID = null,$OrganisasjonID,$adresse) {
      if ($ID == null) {
        $this->db->query($this->db->insert_string('PostAdresser',$adresse));
        $adresse['AdresseID'] = $this->db->insert_id();
        $this->db->query($this->db->insert_string('PostAdresseXOrganisasjon',array('AdresseID'=>$adresse['AdresseID'],'OrganisasjonID'=>$OrganisasjonID)));
      } else {
        $this->db->query($this->db->update_string('PostAdresser',$adresse,'AdresseID='.$ID));
        $adresse['AdresseID'] = $ID;
      }
      $this->db->query($this->db->update_string('Organisasjoner',array('AdresseID'=>$adresse['AdresseID']),'OrganisasjonID='.$OrganisasjonID));
      return $adresse;
    }

    function slettperson($ID) {
      $this->db->query("DELETE FROM Personer WHERE PersonID=".$ID." LIMIT 1");
    }

    function lagreorganisasjon($ID=null,$organisasjon) {
      $organisasjon['DatoEndret'] = date("Y-m-d H:i:s");
      if ($ID == null) {
        $organisasjon['DatoRegistrert'] = $organisasjon['DatoEndret'];
        $this->db->query($this->db->insert_string('Organisasjoner',$organisasjon));
        $organisasjon['OrganisasjonID'] = $this->db->insert_id();
      } else {
        $this->db->query($this->db->update_string('Organisasjoner',$organisasjon,'OrganisasjonID='.$ID));
        $organisasjon['OrganisasjonID'] = $ID;
      }
      if ($this->db->affected_rows() > 0) {
        $this->session->set_flashdata('Infomelding','Organisasjonen er oppdatert.');
      }
      return $organisasjon;
    }

    function organisasjoner() {
      $rorganisasjoner = $this->db->query("SELECT OrganisasjonID,Navn,Orgnummer,Epost,Telefonnr,(SELECT Poststed FROM PostAdresser,PostNummer WHERE (PostAdresser.Postnummer=PostNummer.Postnummer) AND (AdresseID=o.AdresseID) LIMIT 1) As Poststed FROM Organisasjoner o ORDER BY Navn");
      foreach ($rorganisasjoner->result_array() as $organisasjon) {
        $organisasjoner[] = $organisasjon;
        unset($organisasjon);
      }
      if (isset($organisasjoner)) {
        return $organisasjoner;
      }
    }

    function organisasjon($ID) {
      $rorganisasjoner = $this->db->query("SELECT OrganisasjonID,Navn,Orgnummer,Epost,Telefonnr FROM Organisasjoner WHERE (OrganisasjonID=".$ID.") LIMIT 1");
      if ($organisasjon = $rorganisasjoner->row_array()) {
        $organisasjon['Adresser'] = $this->organisasjonadresser($organisasjon['OrganisasjonID']);
        return $organisasjon;
      }
    }

    private function organisasjonadresser($ID) {
      $radresser = $this->db->query("SELECT PostAdresser.AdresseID,Adresse1,Adresse2,PostAdresser.Postnummer,Poststed FROM PostAdresseXOrganisasjon,PostAdresser,PostNummer WHERE (PostAdresser.AdresseID=PostAdresseXOrganisasjon.AdresseID) AND (PostAdresseXOrganisasjon.OrganisasjonID=".$ID.") AND (PostNummer.Postnummer=PostAdresser.Postnummer)");
      foreach ($radresser->result_array() as $adresse) {
        $adresser[] = $adresse;
        unset($adresse);
      }
      if (isset($adresser)) {
        return $adresser;
      }
    }

    function slettorganisasjon($ID) {
      $this->db->query("DELETE FROM Organisasjoner WHERE OrganisasjonID=".$ID." LIMIT 1");
    }

    function personkobleorganisasjon($OrganisasjonID,$PersonID) {
      $this->db->query("INSERT INTO PersonXOrganisasjon (OrganisasjonID,PersonID) VALUES ($OrganisasjonID,$PersonID)");
    }

    function medlemsgrupper($PersonID=null) {
      if ($PersonID == null) {
        $sql = "SELECT GruppeID,Navn,Beskrivelse,Medlemsliste,(IF(Medlemsliste=0,(SELECT COUNT(*) FROM PersonXMedlemsgruppe WHERE (GruppeID=g.GruppeID)),(SELECT COUNT(PersonID) FROM Personer WHERE (MedlemsgruppeID=g.GruppeID)))) AS Antall,Alarmgruppe,DatoEndret FROM Medlemsgrupper g ORDER BY Navn";
      } else {
        $sql = "SELECT g.GruppeID,Navn,Beskrivelse,Medlemsliste,(SELECT COUNT(*) FROM PersonXMedlemsgruppe WHERE (GruppeID=g.GruppeID)) AS Antall,Alarmgruppe FROM PersonXMedlemsgruppe,Medlemsgrupper g WHERE (PersonXMedlemsgruppe.PersonID=".$PersonID.") AND (PersonXMedlemsgruppe.GruppeID=g.GruppeID) ORDER BY Navn";
      }
      $rgrupper = $this->db->query($sql);
      foreach ($rgrupper->result_array() as $gruppe) {
        $grupper[] = $gruppe;
        unset($gruppe);
      }
      if (isset($grupper)) {
        return $grupper;
      }
    }

    function medlemsgruppe($ID) {
      $medlemsgrupper = $this->db->query("SELECT GruppeID,Navn,Beskrivelse,Alarmgruppe,Medlemsliste FROM Medlemsgrupper WHERE (GruppeID=".$ID.") LIMIT 1");
      if ($gruppe = $medlemsgrupper->row_array()) {
        $rkompetanse = $this->db->query("SELECT Kompetanse.KompetanseID,TypeID,Navn,Gyldighet FROM Kompetanse,MedlemsgruppeKompetanseKrav WHERE (MedlemsgruppeKompetanseKrav.KompetanseID=Kompetanse.KompetanseID) AND (MedlemsgruppeKompetanseKrav.GruppeID=".$gruppe['GruppeID'].")");
        foreach ($rkompetanse->result_array() as $kompetanse) {
          $kompetansekrav[] = $kompetanse;
          unset($kompetanse);
        }
        unset($rkompetanse);
        if (isset($kompetansekrav)) { $gruppe['Kompetansekrav'] = $kompetansekrav; }
        if ($gruppe['Medlemsliste'] == 0) {
          $rpersoner = $this->db->query("SELECT Personer.PersonID,Fornavn,Etternavn,Mobilnr FROM PersonXMedlemsgruppe,Personer WHERE (Personer.PersonID=PersonXMedlemsgruppe.PersonID) AND (PersonXMedlemsgruppe.GruppeID=".$gruppe['GruppeID'].") ORDER BY Fornavn, Etternavn");
        } else {
          $rpersoner = $this->db->query("SELECT PersonID,Fornavn,Etternavn,Mobilnr FROM Personer WHERE (MedlemsgruppeID=".$gruppe['GruppeID'].") ORDER BY Fornavn, Etternavn");
        }
        foreach ($rpersoner->result_array() as $person) {
          $person['Kompetanseliste'] = array();
          $rkompetanse = $this->db->query("SELECT KompetanseID,DatoGodkjent,TIMESTAMPDIFF(MONTH,DatoGodkjent,Now()) AS Varighet,(SELECT Gyldighet FROM Kompetanse WHERE (Kompetanse.KompetanseID=PersonXKompetanse.KompetanseID) LIMIT 1) AS Gyldighet FROM PersonXKompetanse WHERE (PersonID=".$person['PersonID'].")");
          foreach ($rkompetanse->result_array() as $kompetanse) {
            if (($kompetanse['Gyldighet'] == 0) or ($kompetanse['Varighet'] <= $kompetanse['Gyldighet'])) {
            array_push($person['Kompetanseliste'],$kompetanse['KompetanseID']);
            $person['Kompetanse'][$kompetanse['KompetanseID']] = $kompetanse['DatoGodkjent'];
            }
            unset($kompetanse);
          }
          unset($rkompetanse);
          $personer[] = $person;
          unset($person);
        }
        if (isset($personer)) {
          $gruppe['Personer'] = $personer;
        }
      }
      return $gruppe;
    }

    function medlemsgruppelagre($ID=null,$gruppe) {
      $gruppe['DatoEndret'] = date('Y-m-d H:i:s');
      if ($ID == null) {
        $gruppe['DatoRegistrert'] = $gruppe['DatoEndret'];
        $this->db->query($this->db->insert_string('Medlemsgrupper',$gruppe));
        $gruppe['GruppeID'] = $this->db->insert_id();
      } else {
        $this->db->query($this->db->update_string('Medlemsgrupper',$gruppe,'GruppeID='.$ID));
        $gruppe['GruppeID'] = $ID;
      }
      return $gruppe;
    }

    function koblekompetansemedlemsgruppe($KompetanseID,$GruppeID) {
      $this->db->query("INSERT INTO MedlemsgruppeKompetanseKrav (KompetanseID,GruppeID) VALUES (".$KompetanseID.",".$GruppeID.")");
    }

    function fjernkompetansemedlemsgruppe($KompetanseID,$GruppeID) {
      echo $KompetanseID.":".$GruppeID;
      $this->db->query("DELETE FROM MedlemsgruppeKompetanseKrav WHERE KompetanseID=".$KompetanseID." AND GruppeID=".$GruppeID);
    }

    function koblepersonmedlemsgruppe($PersonID,$GruppeID) {
      $this->db->query("INSERT INTO PersonXMedlemsgruppe (PersonID,GruppeID) VALUES (".$PersonID.",".$GruppeID.")");
    }

    function fjernpersonmedlemsgruppe($PersonID,$GruppeID) {
      $this->db->query("DELETE FROM PersonXMedlemsgruppe WHERE PersonID=".$PersonID." AND GruppeID=".$GruppeID);
    }

    function vervliste() {
      $rvervliste = $this->db->query("SELECT VervID,Navn,UnderlagtVervID,(SELECT Navn FROM Verv uv WHERE (uv.VervID=v.UnderlagtVervID) LIMIT 1) AS UnderlagtVervNavn,(SELECT Fornavn FROM Personer WHERE (Personer.PersonID=(SELECT PersonID FROM VervXPerson xv WHERE (xv.VervID=v.VervID) AND (Nestleder=0) AND (DatoSlutt='0000-00-00') LIMIT 1)) LIMIT 1) AS LederNavn,(SELECT Fornavn FROM Personer WHERE (Personer.PersonID=(SELECT PersonID FROM VervXPerson xv WHERE (xv.VervID=v.VervID) AND (Nestleder=1) AND (DatoSlutt='0000-00-00') LIMIT 1)) LIMIT 1) AS NestlederNavn,(SELECT DatoStart FROM VervXPerson xv WHERE (xv.VervID=v.VervID) AND (Nestleder=0) AND (DatoSlutt='0000-00-00') LIMIT 1) AS DatoLederFra,(SELECT DatoStart FROM VervXPerson xv WHERE (xv.VervID=v.VervID) AND (Nestleder=1) AND (DatoSlutt='0000-00-00') LIMIT 1) AS DatoNestlederFra,ValgPeriode FROM Verv v ORDER BY Indeks");
      foreach ($rvervliste->result_array() as $verv) {
        $vervliste[] = $verv;
      }
      return $vervliste;
    }

    function verv($ID) {
      $rvervliste = $this->db->query("SELECT VervID,Navn,UnderlagtVervID,ValgPeriode FROM Verv WHERE (VervID=".$ID.") LIMIT 1");
      if ($verv = $rvervliste->row_array()) {
        return $verv;
      }
    }

    function lagreverv($ID=null,$verv) {
      $verv['DatoEndret'] = date('Y-m-d H:i:s');
      if ($ID == null) {
        $verv['DatoRegistrert'] = $verv['DatoEndret'];
        $this->db->query($this->db->insert_string('Verv',$verv));
        $verv['VervID'] = $this->db->insert_id();
      } else {
        $this->db->query($this->db->update_string('Verv',$verv,'VervID='.$ID));
        $verv['VervID'] = $ID;
      }
      return $verv;
    }

    function vervhistorikk($ID) {
      $rhistorikk = $this->db->query("SELECT PersonID,(SELECT Fornavn FROM Personer WHERE (Personer.PersonID=VervXPerson.PersonID) LIMIT 1) AS PersonNavn,DatoStart,DatoSlutt,Nestleder FROM VervXPerson WHERE (VervID=".$ID.") ORDER BY DatoStart DESC");
      foreach ($rhistorikk->result_array() as $periode) {
        $historikk[] = $periode;
      }
      return $historikk;
    }

    var $KompetanseType = array(0 => 'RK-kurs',1 => 'RK-kompetanse',2 => 'Sertifikat',3 => 'Intern oppplæring',4 => 'Språk',5 => 'Sivil kompetanse');
    function kompetanse($PersonID) {
      $rkompetanse = $this->db->query("SELECT Kompetanse.KompetanseID,TypeID,Navn,DatoGodkjent,Kommentar,Gyldighet FROM Kompetanse,PersonXKompetanse WHERE (Kompetanse.KompetanseID=PersonXKompetanse.KompetanseID) AND (PersonXKompetanse.PersonID=".$PersonID.") ORDER BY DatoGodkjent");
      foreach ($rkompetanse->result_array() as $kompetanse) {
        $kompetanse['Type'] = $this->KompetanseType[$kompetanse['TypeID']];
        $kompetanseliste[] = $kompetanse;
      }
      if (isset($kompetanseliste)) {
        return $kompetanseliste;
      }
    }

  }
?>
