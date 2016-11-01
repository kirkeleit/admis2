<?php echo form_open('/saker/mote/'.$Mote['MoteID'],array('class'=>'form-horizontal')); ?>
<input type="hidden" name="MoteID" value="<?php echo set_value('MoteID',$Mote['MoteID']); ?>" />
<div class="panel panel-primary">
  <div class="panel-heading"><b>Møteinformasjon</b></div>
  <div class="panel-body">

    <div class="form-group">
      <label class="col-sm-2 control-label">Møte ID:</label>
      <div class="col-sm-10">
        <p class="form-control-static"><?php echo $Mote['MoteID']; ?></p>
      </div>
    </div>

    <div class="form-group">
      <label for="DatoPlanlagt" class="col-sm-2 control-label">Dato:</label>
      <div class="col-sm-10">
        <input type="date" class="form-control" name="DatoPlanlagt" id="DatoPlanlagt" value="<?php echo set_value('DatoPlanlagt',date('Y-m-d',strtotime($Mote['DatoPlanlagtStart']))); ?>" />
      </div>
    </div>

    <div class="form-group">
      <label for="DatoPlanlagtStart" class="col-sm-2 control-label">Start:</label>
      <div class="col-sm-10">
        <input type="time" class="form-control" name="DatoPlanlagtStart" id="DatoPlanlagtStart" value="<?php echo set_value('DatoPlanlagtStart',date('H:i',strtotime($Mote['DatoPlanlagtStart']))); ?>" />
      </div>
    </div>

    <div class="form-group">
      <label for="DatoPlanlagtSlutt" class="col-sm-2 control-label">Slutt:</label>
      <div class="col-sm-10">
        <input type="time" class="form-control" name="DatoPlanlagtSlutt" id="DatoPlanlagtSlutt" value="<?php echo set_value('DatoPlanlagtSlutt',date('H:i',strtotime($Mote['DatoPlanlagtSlutt']))); ?>" />
      </div>
    </div>

    <div class="form-group">
      <label for="MotetypeID" class="col-sm-2 control-label">Type:</label>
      <div class="col-sm-10">
        <select name="MotetypeID" class="form-control" id="MotetypeID">
          <option value="1" <?php echo set_select('MotetypeID',0,($Mote['MotetypeID'] == 1) ? TRUE : FALSE); ?>>Rådsmøte</option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="Lokasjon" class="col-sm-2 control-label">Lokasjon:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="Lokasjon" id="Lokasjon" value="<?php echo set_value('Lokasjon',$Mote['Lokasjon']); ?>" />
      </div>
    </div>

  </div>

  <div class="panel-footer">
    <div class="form-group">
      <div class="btn-group col-sm-offset-2 col-sm-10">
        <input type="submit" class="btn btn-primary" value="Lagre" name="MoteLagre" />
        <input type="submit" class="btn btn-danger" value="Slett" name="MoteSlett" />
      </div>
    </div>
  </div>
</div>
<?php echo form_close(); ?>
