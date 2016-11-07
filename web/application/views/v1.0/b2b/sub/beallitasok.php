<?
  $szmnev = array(
      'nev' => 'Név',
      'phone' => 'Telefon',
      'irsz' => 'Irányítószám',
      'city' => 'Település',
      'kerulet' => 'Kerület',
      'uhsz' => 'Közterület neve', // közterület neve
      'kozterulet_jellege' => 'Közterület jellege',
      'hazszam' => 'Házszám',
      'epulet' => 'Épület',
      'lepcsohaz' => 'Lépcsőház',
      'szint' => 'Szint',
      'ajto' => 'Ajtó'
  );
?>
<h1>Beállítások</h1>
<?=$this->rmsg?>
<div class="row">
  <form action="" method="post">
    <div class="col-md-12">
      <h3>Törzsadatok</h3>
      <div class="row">
        <div class="col-md-3">
          <label for="nev">Cég neve</label>
          <input type="text" id="nev" name="nev" class="form-control" value="<?=$this->user['data']['nev']?>">
        </div>
        <div class="col-md-3">
          <label for="adoszam">Cég adószáma</label>
          <input type="text" id="adoszam" name="adoszam" class="form-control" value="<?=$this->user['data']['adoszam']?>">
        </div>
        <div class="col-md-6">
          <label for="telephely">Cég telephely pontos címe</label>
          <input type="text" id="telephely" name="telephely" class="form-control" value="<?=$this->user['data']['telephely']?>">
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-3">
          <label for="kapcsolat_nev">Kapcsolattartó neve</label>
          <input type="text" id="kapcsolat_nev" name="kapcsolat_nev" class="form-control" value="<?=$this->user['data']['kapcsolat_nev']?>">
        </div>
        <div class="col-md-3">
          <label for="kapcsolat_telefon">Kapcsolattartó telefon</label>
          <input type="text" id="kapcsolat_telefon" name="kapcsolat_telefon" class="form-control" value="<?=$this->user['data']['kapcsolat_telefon']?>">
        </div>
        <div class="col-md-4">
          <label for="email">Validáló Email / Login</label>
          <input type="text" id="email" name="email" class="form-control" readonly="readonly" value="<?=$this->user['data']['email']?>">
        </div>
        <div class="col-md-2 right">
          <label for="" style="display:block;">&nbsp;</label>
          <button name="saveTorzs" class="btn btn-info btn-sm"><i class="fa fa-save"></i> Törzsadatok mentése</button></div>
      </div>
    </div>
  </form>
</div>
<div class="divider"></div>
<div class="row">
  <form action="" method="post">
    <div class="col-md-6">
      <h3>Számlázási adatok</h3>
      <form action="#szamlazasi" method="post">
      <? foreach($szmnev as $dk => $dv):
        $val = ($this->user[szamlazasi_adat]) ? $this->user[szamlazasi_adat][$dk] : '';
        if($dk == 'phone') continue;
      ?>
      <div class="row np">
          <div class="col-md-12">
            <label for="szam_<?=$dk?>"><?=$szmnev[$dk]?></label>
             <? if($dk == 'state'): ?>
              <select name="<?=$dk?>" class="form-control" id="szam_state">
                  <? foreach( $this->states as $s ): ?>
                      <option value="<?=$s?>" <?=($val == $s) ? 'selected="selected"' : ''?>><?=$s?></option>
                  <? endforeach; ?>
              </select>
              <? elseif($dk == 'kozterulet_jellege'): ?>
              <select name="<?=$dk?>" class="form-control" id="szam_state">
                  <? foreach( $this->kozterulet_jellege as $kj ): ?>
                      <option value="<?=$kj?>" <?=($val == $kj) ? 'selected="selected"' : ''?>><?=$kj?></option>
                  <? endforeach; ?>
              </select>
              <? else: ?>
              <input name="<?=$dk?>" type="text" id="szam_<?=$dk?>" class="form-control" value="<?=$val?>" />
              <? if(!empty($this->buyer_inputs_hints[$dk])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints[$dk]?></div>  <? endif; ?>
              <? endif; ?>
          </div>
      </div>
      <br>
      <? endforeach; ?>
      <div class="row np">
          <div class="col-md-12" align="left"><button name="saveSzamlazasi" class="btn btn-info btn-sm"><i class="fa fa-save"></i> Számlázási adatok mentése</button></div>
      </div>
      </form>
    </div>
    <div class="col-md-6">
      <h3>Szállítási adatok</h3>
      <form action="#szallitasi" method="post">
      <? foreach($szmnev as $dk => $dv):
          $val = ($this->user[szallitasi_adat]) ? $this->user[szallitasi_adat][$dk] : '';
      ?>
      <div class="row np">
          <div class="col-md-12">
            <label for="szall_<?=$dk?>"><?=$szmnev[$dk]?></label>
             <? if($dk == 'state'): ?>
              <select name="<?=$dk?>" class="form-control" id="szall_state">
                  <? foreach( $this->states as $s ): ?>
                      <option value="<?=$s?>" <?=($val == $s) ? 'selected="selected"' : ''?>><?=$s?></option>
                  <? endforeach; ?>
              </select>
              <? elseif($dk == 'kozterulet_jellege'): ?>
              <select name="<?=$dk?>" class="form-control" id="szall_state">
                  <? foreach( $this->kozterulet_jellege as $kj ): ?>
                      <option value="<?=$kj?>" <?=($val == $kj) ? 'selected="selected"' : ''?>><?=$kj?></option>
                  <? endforeach; ?>
              </select>
              <? else: ?>
              <input name="<?=$dk?>" type="text" id="szall_<?=$dk?>" class="form-control" value="<?=$val?>" />
              <? if(!empty($this->buyer_inputs_hints[$dk])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints[$dk]?></div>  <? endif; ?>
              <? endif; ?>
          </div>
      </div>
      <br>
      <? endforeach; ?>
      <div class="row np">
          <div class="col-md-12" align="left"><button name="saveSzallitasi" class="btn btn-info btn-sm"><i class="fa fa-save"></i> Szállítási adatok mentése</button></div>

      </div>
      </form>
    </div>
  </form>
</div>
