<div class="page-header">
  <h1>Budsjett</h1>
</div>

<?php
  if (in_array('304',$this->session->userdata('UAP'))) {
?>
<?php echo form_open(); ?>
Budsjettår: <input type="year" name="BudsjettAr" value="<?php echo date("Y"); ?>" class="form-control" />
<br />
<br />

<div class="panel panel-default">
  <div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Konto</th>
<?php
  $i = 0;
  foreach ($Aktiviteter as $Aktivitet) {
?>
          <th><?php echo $Aktivitet['AktivitetID']." ".$Aktivitet['Navn']; ?></th>
<?php
  }
?>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="<?php echo (1+sizeof($Aktiviteter)); ?>"><b>Inntekter</b></td>
        </tr>
<?php
  foreach ($KontoerINN as $Konto) {
?>
        <tr>
          <td><span title="<?php echo $Konto['Beskrivelse']; ?>"><?php echo $Konto['KontoID']." ".$Konto['Navn']; ?></span></td>
<?php
  foreach ($Aktiviteter as $Aktivitet) {
?>
          <td><input type="hidden" name="Konto[<?php echo $i; ?>]" value="<?php echo $Konto['KontoID']; ?>" /><input type="hidden" name="Aktivitet[<?php echo $i; ?>]" value="<?php echo $Aktivitet['AktivitetID']; ?>" /><input type="number" name="Budsjett[<?php echo $i; ?>]" value="<?php echo $Budsjett[$Konto['KontoID']][$Aktivitet['AktivitetID']]; ?>" step="100" style="width:80px;" class="form-control" /></td>
<?php
    $i++;
  }
?>
        </tr>
<?php
  }
?>
        <tr>
          <td colspan="<?php echo (1+sizeof($Aktiviteter)); ?>">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="<?php echo (1+sizeof($Aktiviteter)); ?>"><b>Utgifter</b></td>
        </tr>
<?php
  foreach ($KontoerUT as $Konto) {
?>
  <tr>
    <td><?php echo $Konto['KontoID']." ".$Konto['Navn']; ?></td>
<?php
  foreach ($Aktiviteter as $Aktivitet) {
?>
    <td><input type="hidden" name="Konto[<?php echo $i; ?>]" value="<?php echo $Konto['KontoID']; ?>" /><input type="hidden" name="Aktivitet[<?php echo $i; ?>]" value="<?php echo $Aktivitet['AktivitetID']; ?>" /><input type="number" name="Budsjett[<?php echo $i; ?>]" value="<?php echo $Budsjett[$Konto['KontoID']][$Aktivitet['AktivitetID']]; ?>" step="100" style="width:80px;" /></td>
<?php
    $i++;
  }
?>
  </tr>
<?php
  }
?>
    </table>
  </div>
</div>
<input type="submit" value="Lagre" name="BudsjettLagre" class="btn btn-primary" />
<?php echo form_close(); ?>
<?php
  } else {
    echo "Ingen tilgang.";
  }
?>
