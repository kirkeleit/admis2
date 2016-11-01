<!doctype html>
<html lang="no">
<head>
  <meta http-equiv="cache-control" content="no-cache" />
  <meta http-equiv="pragma" content="no-cache" />
  <meta charset="utf-8" />
  <title>Møteskjerm</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <style type="text/css">
    #Klokke {
      background-color: white;
      position: absolute;
      top: 0px;
      left: 0px;
      height: 80px;
      width: 240px;
      border-right: 2px solid #8B0000;
      border-bottom: 2px solid #8B0000;
      font-family: 'Courier new';
      text-align: center;
      border-bottom-right-radius: 10px;
    }

    #KlokkeTid {
      font-weight: bold;
      font-size: 2.8em;
    }

    #Logo {
      background-color: white;
      position: absolute;
      height: 70px;
      left: 0px;
      bottom: 0px;
      width: 240px;
      border-right: 2px solid #8B0000;
      border-top: 2px solid #8B0000;
      border-top-right-radius: 10px;
    }

    #Saksliste {
      background-color: white;
      position: absolute;
      top: 82px;
      left: 0px;
      bottom: 72px;
      width: 230px;
      border-right: 2px solid #8B0000;
    }

    #Sak {
      background-color: white;
      position: fixed;
      top: 57px;
      left: 242px;
      right: 0px;
      bottom: 0px;
      overflow: auto;
      padding: 15px;
      font-size: 1.3em;
      font-family: Verdana;
    }

    #SaksTittel {
      position: fixed;
      top: 0px;
      left: 242px;
      right: 0px;
      height: 50px;
      border-bottom: 2px solid #8B0000;
      background: white;
      font-weight: bold;
      font-size: 2.4em;
      font-family: Verdana;
      padding-top: 5px;
      padding-left: 30px;
      overflow: hidden;
    }
  </style>
  <script>
    function OppdaterKlokke() {
      var ManedNavn = ["jan", "feb", "mar", "apr", "mai", "jun", "jul", "aug", "sep", "okt", "nov", "des"];
      var DagNavn = ["S&#248;ndag", "Mandag", "Tirsdag", "Onsdag", "Torsdag", "Fredag", "L&#248;rdag"];

      var currentTime = new Date();
      var currentHours = currentTime.getHours();
      var currentMinutes = currentTime.getMinutes();
      var currentSeconds = currentTime.getSeconds();

      tidTime = (currentHours < 10 ? "0" : "") + currentHours;
      tidMinutter = ( currentMinutes < 10 ? "0" : "" ) + currentMinutes;
      tidSekunder = ( currentSeconds < 10 ? "0" : "" ) + currentSeconds;

      $("#KlokkeTid").html(tidTime+":"+tidMinutter+":"+tidSekunder);
      $("#KlokkeDato").html(DagNavn[currentTime.getDay()] + " " + currentTime.getDate() + " " + ManedNavn[currentTime.getMonth()] + " " + currentTime.getFullYear());
    }

    $(document).ready(function() {
      OppdaterKlokke();
      setInterval("OppdaterKlokke()", 1000);
    });
  </script>
</head>
<body>

<?php if (isset($Mote)) { ?>

<div id="Klokke">
<span id="KlokkeTid">##:##:##</span>
<span id="KlokkeDato">##.##.####</span>
</div>

<div id="Saksliste">
<ul>
<?php
  foreach ($Saksliste as $SaksInfo) {
    if ($SaksInfo['SaksNummer'] > 0) {
?>
  <li><a href="<?php echo site_url('/saker/moteskjerm/?mid='.$Mote['MoteID'].'&sid='.$SaksInfo['SakID']); ?>"><?php echo $SaksInfo['SaksAr']."/".$SaksInfo['SaksNummer']; ?></a></li>
<?php
    }
  }
?>
</ul>
</div>

<?php if (isset($Sak)) { ?>
<?php echo form_open('/saker/moteskjerm/?mid='.$Mote['MoteID'].'&sid='.$Sak['SakID']); ?>
<div id="SaksTittel"><?php echo $Sak['SaksAr']."/".$Sak['SaksNummer'].": ".$Sak['Tittel']; ?></div>
<div id="Sak">

<p><?php echo nl2br($Sak['Saksbeskrivelse']); ?></p>

<table>
<?php if (isset($Notater)) { ?>
<?php foreach ($Notater as $Notat) { ?>
    <tr>
      <td colspan="3"><hr></td>
    </tr>
    <tr>
      <td style="vertical-align:top;width:170px;font-size:1em;background-color:#F8F8FF;border-right:1px solid gray"><b><?php echo date("d.m.Y",strtotime($Notat['DatoRegistrert'])); ?></b><br /><?php echo $Notat['PersonNavn']; ?></td>
      <td><?php echo nl2br($Notat['Notat']); ?></td>
    </tr>
<?php } ?>
<?php } ?>
</table>

<br><br>

<input type="hidden" name="SakID" value="<?php echo $Sak['SakID']; ?>" />
<input type="hidden" name="MoteID" value="<?php echo $Mote['MoteID']; ?>" />
<fieldset>
  <legend>Nytt notat/vedtak</legend>

  <p>
    <label for="">Notatstype:</label>
    <select name="Notatstype" id="Notatstype">
      <option value="1">Notat</option>
      <option value="2">Vedtak</option>
    </select>
  </p>

  <p>
    <label for="Notat">Notat:</label>
    <textarea name="Notat" id="Notat"></textarea>
  </p>

  <p>
    <label>&nbsp;</label>
    <input type="submit" value="Lagre" name="LagreNotat" />
  </p>
</fieldset>
</div>
<?php } else { ?>
<?php echo form_open('/saker/moteskjerm/?mid='.$Mote['MoteID']); ?>
<div id="Sak">
  <p>
    <label>Start:</label>
    <?php echo $Mote['DatoPlanlagtStart']; ?>
  </p>

  <p>
    <label>Slutt:</label>
    <?php echo $Mote['DatoPlanlagtSlutt']; ?>
  </p>
  <p>
    <label>Saker:</label>
    <?php echo sizeof($Saksliste); ?>
  </p>
  <p>
    <label>Lokasjon:</label>
    <?php echo $Mote['Lokasjon']; ?>
  </p>
  <p>
    <label>Deltakere:</label>
    <textarea name="Deltakere"></textarea>
  </p>

  <p>
    <label>&nbsp;</label>
    <input type="submit" value="Lagre" name="MøteLagre" />&nbsp;<input type="submit" value="Start møte" name="MøteStart" />&nbsp;<input type="submit" value="Avslutt møte" name="MøteSlutt" />
  </p>
</div>
<?php } ?>
<?php echo form_close(); ?>

<div id="Logo"><img src="/grafikk/Bomlo_hj.jpg" width="230" /></div>
<?php } ?>

</body>
</html>
