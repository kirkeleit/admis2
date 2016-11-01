<?php echo form_open('/saker/sak/'.$Sak['SakID'],array('class'=>'form-horizontal')); ?>
<input type="hidden" name="SakID" value="<?php echo set_value('SakID',$Sak['SakID']); ?>" />
<div class="panel panel-primary">
  <div class="panel-heading"><b>Saksdetaljer</b></div>
  <div class="panel-body">

    <div class="form-group">
      <label class="col-sm-2 control-label">Saksnummer:</label>
      <div class="col-sm-10">
        <p class="form-control-static"><?php echo ($Sak['SaksAr'] != 0 ? $Sak['SaksAr']."/".$Sak['SaksNummer'] : '&nbsp;'); ?></p>
      </div>
    </div>

    <div class="form-group">
      <label for="Tittel" class="col-sm-2 control-label">Tittel:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="Tittel" id="Tittel" value="<?php echo set_value('Tittel',$Sak['Tittel']); ?>" />
      </div>
    </div>

    <div class="form-group">
      <label for="PersonID" class="col-sm-2 control-label">Innmeldt av:</label>
      <div class="col-sm-10">
        <select name="PersonID" class="form-control" id="PersonID">
          <option value="0" <?php echo set_select('PersonID',0,($Sak['PersonID'] == 0) ? TRUE : FALSE); ?>>(ikke satt)</option>
<?php
  if (isset($Personer)) {
    foreach ($Personer as $Person) {
?>
          <option value="<?php echo $Person['PersonID']; ?>" <?php echo set_select('PersonID',$Person['PersonID'],($Sak['PersonID'] == $Person['PersonID']) ? TRUE : FALSE); ?>><?php echo $Person['Fornavn']." ".$Person['Etternavn']; ?></option>
<?php
    }
  }
?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="Saksbeskrivelse" class="col-sm-2 control-label">Saksbeskrivelse:</label>
      <div class="col-sm-10">
        <textarea name="Saksbeskrivelse" class="form-control" id="Saksbeskrivelse"><?php echo set_value('Saksbeskrivelse',$Sak['Saksbeskrivelse']); ?></textarea>
      </div>
    </div>

    <div class="form-group">
      <label for="Tidsbehov" class="col-sm-2 control-label">Tidsbehov:</label>
      <div class="col-sm-10">
        <input type="number" class="form-control" name="Tidsbehov" id="Tidsbehov" value="<?php echo set_value('Tidsbehov',$Sak['Tidsbehov']); ?>" />
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-2 control-label">Status:</label>
      <div class="col-sm-10">
        <p class="form-control-static"><?php if (isset($Sak['Status'])) { echo $Sak['Status']; } else { echo "&nbsp;"; } ?></p>
      </div>
    </div>
  </div>

  <div class="panel-footer">
    <div class="form-group">
      <div class="btn-group col-sm-offset-2 col-sm-10">
        <input type="submit" class="btn btn-primary" value="Lagre" name="SakLagre" />
        <input type="submit" class="btn btn-danger" value="Slett" name="SakSlett" <?php if (($Sak['SaksNummer'] != 0) or (!isset($Sak['SakID']))) { echo "disabled "; } ?>/>
        <input type="submit" class="btn btn-success" value="Tildel saksnummer" name="TildelSaksnummer" <?php if (($Sak['SaksNummer'] != 0) or (!isset($Sak['SakID']))) { echo "disabled "; } ?>/>
      </div>
    </div>
  </div>
</div>

<?php if ($Sak['SakID'] > 0) { ?>
<div class="panel panel-info">
  <div class="panel-heading">Saksnotater</div>
  <div class="panel-body">

<?php
  if (isset($Notater)) {
    foreach ($Notater as $Notat) {
?>
    <div class="panel panel-default <?php echo ($Notat['Notatstype'] == 'Vedtak' ? 'panel-success' : 'panel-info'); ?>">
      <div class="panel-heading"><?php echo date('d.m.Y H:i',strtotime($Notat['DatoRegistrert'])).", av ".$Notat['PersonNavn']; ?></div>
      <div class="panel-body"><?php echo nl2br($Notat['Notat']); ?></div>
    </div>
<?php
    }
  }
?>

  </div>

  <div class="panel-footer">
    <div class="form-group">
      <label class="col-sm-2 control-label">Type:</label>
      <div class="col-sm-10">
        <select name="Notatstype" id="Notatstype" class="form-control">
          <option value="0">Kommentar</option>
<?php if ($Sak['StatusID'] == 0) { ?>
          <option value="1">Notat</option>
          <option value="2">Vedtak</option>
<?php } ?>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label">Notat:</label>
      <div class="col-sm-10">
        <textarea name="Notat" id="Notat" class="form-control"></textarea>
      </div>
    </div>
    <div class="form-group">
      <div class="btn-group col-sm-offset-2 col-sm-10">
        <input type="submit" class="btn btn-primary" value="Legg til" name="NotatLagre" />
      </div>
    </div>
  </div>
</div>
<?php } ?>
<?php echo form_close(); ?>
