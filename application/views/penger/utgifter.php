<div class="page-header">
  <h1>Utgifter <a href="<?php echo site_url('/okonomi/nyutgift'); ?>" class="btn btn-default" role="button"><span class="glyphicon glyphicon-plus"></span></a></h1>
</div>

<div class="panel panel-default">
  <div class="panel-heading text-right">
    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#UtgiftFilter"><span class="glyphicon glyphicon-filter" aria-hidden="true"></span></button>
  </div>
  <div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Dato</th>
          <th>Aktivitet</th>
          <th>Konto</th>
          <th>Person</th>
          <th>Innkjøpsordre</th>
          <th>Beskrivelse</th>
          <th>Beløp</th>
        </tr>
      </thead>
      <tbody>
<?php
  $Totalt = 0;
  $Fremtidig = 0;
  if (isset($Utgifter)) {
    foreach ($Utgifter as $Utgift) {
      if (strtotime($Utgift['DatoBokfort']) <= time()) {
?>
        <tr>
          <td><?php echo date('d.m.Y',strtotime($Utgift['DatoBokfort'])); ?></td>
          <td><span title="<?php echo $Utgift['Aktivitet']; ?>"><?php echo $Utgift['AktivitetID']; ?></span></td>
          <td><span title="<?php echo $Utgift['Konto']; ?>"><?php echo $Utgift['KontoID']; ?></span></td>
          <td><span title="<?php echo $Utgift['Person']; ?>"><?php echo ($Utgift['PersonID'] > 0 ? $Utgift['PersonInitialer'] : '&nbsp;'); ?></span></td>
          <td><?php if ($Utgift['InnkjopsordreID'] > 0) { echo anchor('/okonomi/innkjopsordre/'.$Utgift['InnkjopsordreID'],"INO#".$Utgift['InnkjopsordreID']); } else { echo "&nbsp;"; } ?></td>
          <td><?php echo anchor('/okonomi/utgift/'.$Utgift['UtgiftID'],$Utgift['Beskrivelse']); ?></a></td>
          <td class="text-right"><?php echo 'kr '.number_format($Utgift['Belop'],2,',','.'); ?></td>
        </tr>
<?php
        $Totalt = $Totalt + $Utgift['Belop'];
      }
    }
?>
        <tr>
          <td colspan="6">&nbsp;</td>
          <td class="text-right"><b><?php echo 'kr '.number_format($Totalt,2,',','.'); ?></b></td>
        </tr>
        <tr>
          <td colspan="7">&nbsp;</td>
        </tr>
<?php
    foreach ($Utgifter as $Utgift) {
      if (strtotime($Utgift['DatoBokfort']) > time()) {
?>
        <tr>
          <td><?php echo date('d.m.Y',strtotime($Utgift['DatoBokfort'])); ?></td>
          <td><span title="<?php echo $Utgift['Aktivitet']; ?>"><?php echo $Utgift['AktivitetID']; ?></span></td>
          <td><span title="<?php echo $Utgift['Konto']; ?>"><?php echo $Utgift['KontoID']; ?></span></td>
          <td><span title="<?php echo $Utgift['Person']; ?>"><?php echo ($Utgift['PersonID'] > 0 ? $Utgift['PersonInitialer'] : '&nbsp;'); ?></span></td>
          <td><?php if ($Utgift['InnkjopsordreID'] > 0) { echo anchor('/okonomi/innkjopsordre/'.$Utgift['InnkjopsordreID'],"INO#".$Utgift['InnkjopsordreID']); } else { echo "&nbsp;"; } ?></td>
          <td><?php echo anchor('/okonomi/utgift/'.$Utgift['UtgiftID'],$Utgift['Beskrivelse']); ?></a></td>
          <td class="text-right"><?php echo 'kr '.number_format($Utgift['Belop'],2,',','.'); ?></td>
        </tr>
<?php
        $Totalt = $Totalt + $Utgift['Belop'];
      }
    }
?>
        <tr>
          <td colspan="6">&nbsp;</td>
          <td class="text-right"><b><?php echo 'kr '.number_format($Totalt,2,',','.'); ?></b></td>
        </tr>
<?php
  } else {
?>
        <tr>
          <td colspan="7" class="danger">Ingen utgifter i utvalg.</td>
        </tr>
<?php
  }
?>
      </tbody>
    </table>
  </div>
</div>

<?php echo form_open(); ?>
<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="UtgiftFilter" id="UtgiftFilter">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header"><b>Filtrer inntektsliste</b></div>
      <div class="modal-body">
        <div class="form-group">
          <label for="FilterAr">Bokført år:</label>
          <select name="FilterAr" class="form-control">
            <option value="2014">2014</option>
            <option value="2015">2015</option>
            <option value="2016" selected="selected">2016</option>
          </select>
        </div>
        <div class="form-group">
          <label for="FilterManed">Bokført måned:</label>
          <select name="FilterManed" class="form-control">
            <option value=""></option>
<?php
  $ManedNavn = array(1=>'Januar', 2=>'Februar', 3=>'Mars', 4=>'April', 5=>'Mai', 6=>'Juni', 7=>'Juli', 8=>'August', 9=>'September', 10=>'Oktober', 11=>'November', 12 => 'Desember');
  for ($x=1;$x<=12;$x++) {
?>
            <option value="<?php echo $x; ?>"><?php echo $ManedNavn[$x]; ?></option>
<?php
  }
?>
          </select>
        </div>
        <div class="form-group">
          <label for="FilterAktivitetID">Aktivitet:</label>
          <select name="FilterAktivitetID" class="form-control">
            <option value=""></option>
<?php
  if (isset($Aktiviteter)) {
    foreach ($Aktiviteter as $Aktivitet) {
?>
            <option value="<?php echo $Aktivitet['AktivitetID']; ?>"><?php echo $Aktivitet['AktivitetID'].' '.$Aktivitet['Navn']; ?></option>
<?php
    }
  }
?>
          </select>
        </div>
        <div class="form-group">
          <label for="FilterKontoID">Konto:</label>
          <select name="FilterKontoID" class="form-control">
            <option value=""></option>
<?php
  if (isset($Kontoer)) {
    foreach ($Kontoer as $Konto) {
?>
            <option value="<?php echo $Konto['KontoID']; ?>"><?php echo $Konto['KontoID'].' '.$Konto['Navn']; ?></option>
<?php
    }
  }
?>
          </select>
        </div>

      </div>
      <div class="modal-footer">
        <input type="submit" value="Filtrer" name="UtgiftFilter" class="btn btn-primary"/>
      </div>
    </div>
  </div>
</div>
<?php echo form_close(); ?>
