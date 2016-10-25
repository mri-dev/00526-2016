<? $this->render('b2b/users/menu'); ?>
<div class="b2b-user-edit-view">
  <div class="row">
    <div class="col-md-12">
      <h2>Új partner létrehozása</h2>
    </div>
  </div>
  <br>
  <? if(isset($this->rmsg)): ?><?=$this->rmsg?><? endif; ?>
  <br>
  <div class="row np">
    <div class="col-sm-12">
      <form action="" method="post">
      <div class="row">
        <div class="col-md-12">
          <h3>Fiókdatok</h3>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-6<?=(isset($this->err) && in_array('nev', $this->err['missing_fields']))?' has-error':''?>">
          <label for="nev">Név (cég) *</label>
          <input type="text" id="nev" name="nev"  value="<?=$_POST['nev']?>" class="form-control">
        </div>
        <div class="col-md-6<?=(isset($this->err) && in_array('telephely', $this->err['missing_fields']))?' has-error':''?>">
          <label for="telephely">Telephely *</label>
          <input type="text" id="telephely" name="telephely" value="<?=$_POST['telephely']?>" class="form-control">
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-6<?=(isset($this->err) && in_array('adoszam', $this->err['missing_fields']))?' has-error':''?>">
          <label for="adoszam">Adószám *</label>
          <input type="text" id="adoszam" name="adoszam" value="<?=$_POST['adoszam']?>" class="form-control">
        </div>
        <div class="col-md-6<?=(isset($this->err) && in_array('email', $this->err['missing_fields']))?' has-error':''?>">
          <label for="email">E-mail / Login azonosító *</label>
          <input type="text" id="email" name="email" value="<?=$_POST['email']?>" class="form-control">
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-6<?=(isset($this->err) && in_array('jelszo', $this->err['missing_fields']))?' has-error':''?>">
          <label for="jelszo">Jelszó *</label>
          <input type="text" id="jelszo" name="jelszo" value="<?=\Helper::randomPassword()?>" class="form-control">
        </div>
        <div class="col-md-6">
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <br><br>
          <h3>Kapcsolattartó</h3>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-6<?=(isset($this->err) && in_array('kapcsolat_nev', $this->err['missing_fields']))?' has-error':''?>">
          <label for="kapcsolat_nev">Kapcsolatartó neve *</label>
          <input type="text" id="kapcsolat_nev" name="kapcsolat_nev" value="<?=$_POST['kapcsolat_nev']?>" class="form-control">
        </div>
        <div class="col-md-6<?=(isset($this->err) && in_array('kapcsolat_telefon', $this->err['missing_fields']))?' has-error':''?>">
          <label for="kapcsolat_telefon">Kapcsolattartó telefonszám *</label>
          <input type="text" id="kapcsolat_telefon" name="kapcsolat_telefon" value="<?=$_POST['kapcsolat_telefon']?>" class="form-control">
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-6">
          <br><br>
          <h3>Számlázási adatok</h3>
          <br>
          <?
          foreach (\B2B\B2BFactory::getSzamlazasFields() as $key) { ?>
          <div class="row np">
            <div class="col-md-12<?=(isset($this->err) && in_array('szamlazas_'.$key, $this->err['missing_fields']))?' has-error':''?>">
              <label for="szamlazas_<?=$key?>"><?=\B2B\B2BFactory::szmfieldName($key)?> <?=(in_array($key, $this->required_create_fields['szamlazas']))?'*':''?></label>
              <input type="text" id="szamlazas_<?=$key?>" name="szamlazas[<?=$key?>]" value="<?=$_POST['szamlazas'][$key]?>" class="form-control">
            </div>
          </div>
          <br>
          <? }?>
        </div>
        <div class="col-md-6">
          <br><br>
          <h3>Szállítási adatok</h3>
          <br>
          <?
          foreach (\B2B\B2BFactory::getSzallitasFields() as $key) { ?>
          <div class="row np">
            <div class="col-md-12<?=(isset($this->err) && in_array('szallitas_'.$key, $this->err['missing_fields']))?' has-error':''?>">
              <label for="szallitas_<?=$key?>"><?=\B2B\B2BFactory::szmfieldName($key)?> <?=(in_array($key, $this->required_create_fields['szallitas']))?'*':''?></label>
              <input type="text" id="szallitas_<?=$key?>" name="szallitas[<?=$key?>]" value="<?=$_POST['szallitas'][$key]?>" class="form-control">
            </div>
          </div>
          <br>
          <? }?>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-12">
          <button class="btn btn-primary" name="createUser" value="1">Létrehozás <i class="fa fa-plus-circle"></i></button>
        </div>
      </div>
      </form>
    </div>
  </div>
</div>
