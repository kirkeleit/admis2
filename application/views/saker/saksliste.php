<div class="panel panel-default">
  <div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>#</th>
          <th>Registrert</th>
          <th>Medlem</th>
          <th>Tittel</th>
          <th>Tid</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
<?php
  if (isset($Saksliste)) {
    $TotaltTidsbehov = 0;
    foreach ($Saksliste as $Sak) {
      if ($Sak['SaksAr'] > 0) {
        $TotaltTidsbehov += $Sak['Tidsbehov'];
      }
?>
        <tr>
          <td><?php echo anchor('/saker/sak/'.$Sak['SakID'],($Sak['SaksAr'] > '0' ? $Sak['SaksAr']."/".$Sak['SaksNummer'] : '&nbsp;')); ?></td>
          <td><?php echo date('d.m.Y',strtotime($Sak['DatoRegistrert'])); ?></td>
          <td><?php echo $Sak['PersonNavn']; ?></td>
          <td><?php echo anchor('/saker/sak/'.$Sak['SakID'],$Sak['Tittel']); ?></td>
          <td><?php echo $Sak['Tidsbehov']; ?></td>
          <td><?php echo $Sak['Status']; ?></td>
        </tr>
<?php
    }
?>
        <tr>
          <td colspan="4">&nbsp;</td>
          <td><?php echo date('H:i',mktime(0,$TotaltTidsbehov)); ?></td>
          <td>&nbsp;</td>
        </tr>
<?php
  } else {
?>
        <tr>
          <td colspan="5">Ingen saker i utvalg.</td>
        </tr>
<?php
  }
?>
      </tbody>
    </table>
  </div>
</div>
<a href="http://admis2.bomlork.no/index.php/Saker/sakslistetoslack">Send saksliste til slack</a>
