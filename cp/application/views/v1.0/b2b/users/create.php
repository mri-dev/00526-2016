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
          <label for="">Név (cég) *</label>
          <input type="text" name="nev"  value="<?=$_POST['nev']?>" class="form-control">
        </div>
        <div class="col-md-6">
          <label for="">Telephely *</label>
          <input type="text" name="telephely" value="<?=$_POST['telephely']?>" class="form-control">
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-6">
          <label for="">Adószám *</label>
          <input type="text" name="adoszam" value="<?=$_POST['adoszam']?>" class="form-control">
        </div>
        <div class="col-md-6">
          <label for="">E-mail / Login azonosító *</label>
          <input type="text" name="email" value="<?=$_POST['email']?>" class="form-control">
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-6">
          <label for="">Jelszó *</label>
          <input type="text" name="jelszo" value="<?=\Helper::randomPassword()?>" class="form-control">
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
        <div class="col-md-6">
          <label for="">Kapcsolatartó neve *</label>
          <input type="text" name="kapcsolat_nev" value="<?=$_POST['kapcsolat_nev']?>" class="form-control">
        </div>
        <div class="col-md-6">
          <label for="">Kapcsolattartó telefonszám *</label>
          <input type="text" name="kapcsolat_telefon" value="<?=$_POST['kapcsolat_telefon']?>" class="form-control">
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
            <div class="col-md-12">
              <label for=""><?=\B2B\B2BFactory::szmfieldName($key)?></label>
              <input type="text" name="szamlazas[<?=$key?>]" value="<?=$_POST['szamlazas'][$key]?>" class="form-control">
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
            <div class="col-md-12">
              <label for=""><?=\B2B\B2BFactory::szmfieldName($key)?></label>
              <input type="text" name="szallitas[<?=$key?>]" value="<?=$_POST['szallitas'][$key]?>" class="form-control">
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
