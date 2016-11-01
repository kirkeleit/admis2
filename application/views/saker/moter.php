<div class="panel panel-default">
  <div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Dato</th>
          <th>Møtetype</th>
          <th>Start/slutt</th>
          <th>Lokasjon</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
<?php
  if (isset($Møter)) {
    foreach ($Møter as $Møte) {
?>
        <tr>
          <td><?php echo anchor('saker/mote/'.$Møte['MoteID'],date('d.m.Y',strtotime($Møte['DatoPlanlagtStart']))); ?></td>
          <td><?php echo $Møte['Motetype']; ?></td>
          <td><?php echo date('H:i',strtotime($Møte['DatoPlanlagtStart']))." - ".date('H:i',strtotime($Møte['DatoPlanlagtSlutt'])); ?></td>
          <td><?php echo $Møte['Lokasjon']; ?></td>
          <td>
<?php if (strtotime($Møte['DatoPlanlagtSlutt']) > time()) { ?>
          <a href="<?php echo site_url(); ?>/saker/moteskjerm/?mid=<?php echo $Møte['MoteID']; ?>">Møteskjerm</a>
<?php } ?>
<?php if (strtotime($Møte['DatoPlanlagtSlutt']) < time()) { ?>
          <a href="<?php echo site_url(); ?>/saker/referat/<?php echo $Møte['MoteID']; ?>">Referat</a>
<?php } ?>
          </td>
        </tr>
<?php
    }
  } else {
?>
        <tr>
          <td colspan="5" class="danger">Ingen møter i utvalg.</td>
        </tr>
<?php
  }
?>
      </tbody>
    </table>
  </div>
</div>
