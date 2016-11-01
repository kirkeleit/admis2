<?php
  if (isset($Referat['Møte'])) {
    $Møte = $Referat['Møte'];
    if ($Møte['MotetypeID'] == 1) {
      $Møte['MotetypeNavn'] = "Rådsmøte";
    }
?>
<h2 class="text-uppercase"><?php echo $Møte['MotetypeNavn']." ".date('d.m.Y',strtotime($Møte['DatoPlanlagtStart'])); ?></h2>
<blockquote>
<p>
  <strong>Tid:</strong> <?php echo date('H:i',strtotime($Møte['DatoPlanlagtStart']))."-".date('H:i',strtotime($Møte['DatoPlanlagtSlutt'])); ?><br />
  <strong>Sted:</strong> <?php echo $Møte['Lokasjon']; ?><br />
  <strong>Saker:</strong> <?php echo sizeof($Referat['Saker']); ?> stk<br />
  <strong>Deltakere:</strong> <?php echo $Møte['Deltakere']; ?><br />
</p>
</blockquote>
<br />
<?php
  }
?>

<?php
  if (isset($Referat['Saker'])) {
    foreach ($Referat['Saker'] as $Sak) {
?>
<h4><?php echo $Sak['SaksAr']."/".$Sak['SaksNummer'].": ".$Sak['Tittel']; ?></h4>
<p>
  <strong>Registrert dato: </strong><?php echo date('d.m.Y',strtotime($Sak['DatoRegistrert'])); ?><br />
  <strong>Registrert av: </strong><?php echo $Sak['PersonNavn']; ?></p>
<p>
<strong><?php echo $Sak['Saksbeskrivelse']; ?></strong>
</p>
<?php
      if (isset($Sak['Saksnotater'])) {
        foreach ($Sak['Saksnotater'] as $Notat) {
?>
<p>
<em><?php if ($Notat['Notatstype'] == 0) { echo "Kommentar"; } elseif ($Notat['Notatstype'] == 1) { echo "Notat"; } elseif ($Notat['Notatstype'] == 2) { echo "<span class=\"bg-success\">Vedtak</span>"; } ?><?php echo " den ".date('d.m.Y H:i',strtotime($Notat['DatoRegistrert'])).":"; ?></em><br />
<?php echo nl2br($Notat['Notat']); ?></p>
<?php
        }
      }
?>
<hr />
<?php
    }
  }
?>
