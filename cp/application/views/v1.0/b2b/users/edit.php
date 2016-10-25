<? $this->render('b2b/users/menu'); ?>
<?
  $u = $this->u;
?>
<div class="b2b-user-edit-view">
  <div class="row">
    <div class="col-md-12">
      <h2><strong><?=$u->Name()?></strong> szerkesztése</h2>
    </div>
  </div>
  <br>
  <? if(isset($this->rmsg)): ?><?=$this->rmsg?><? endif; ?>
  <br>
  <div class="row np">
    <div class="col-sm-8">
      <form action="" method="post">
      <div class="row">
        <div class="col-md-12">
          <h3>Fiókdatok</h3>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-6">
          <label for="">Név (cég)</label>
          <input type="text" name="nev" value="<?=$u->Name()?>" class="form-control">
        </div>
        <div class="col-md-6">
          <label for="">Telephely</label>
          <input type="text" name="telephely" value="<?=$u->Address()?>" class="form-control">
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-6">
          <label for="">Adószám</label>
          <input type="text" name="adoszam" value="<?=$u->Taxnumber()?>" class="form-control">
        </div>
        <div class="col-md-6">
          <label for="">E-mail / Login azonosító</label>
          <input type="text" name="email" value="<?=$u->Email()?>" class="form-control">
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-6">
          <label for="">Jelszó</label>
          <input type="text" name="jelszo" value="<?=$u->PasswordString()?>" class="form-control">
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
          <label for="">Kapcsolatartó neve</label>
          <input type="text" name="kapcsolat_nev" value="<?=$u->ContactName()?>" class="form-control">
        </div>
        <div class="col-md-6">
          <label for="">Kapcsolattartó telefonszám</label>
          <input type="text" name="kapcsolat_telefon" value="<?=$u->ContactPhone()?>" class="form-control">
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-6">
          <br><br>
          <h3>Számlázási adatok</h3>
          <br>
          <?
          $szamlazas = $u->SzamlazasList();
          foreach (\B2B\B2BFactory::getSzamlazasFields() as $key) { ?>
          <div class="row np">
            <div class="col-md-12">
              <label for=""><?=\B2B\B2BFactory::szmfieldName($key)?></label>
              <input type="text" name="szamlazas[<?=$key?>]" value="<?=$szamlazas[$key]?>" class="form-control">
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
          $szallitas = $u->SzallitasList();
          foreach (\B2B\B2BFactory::getSzallitasFields() as $key) { ?>
          <div class="row np">
            <div class="col-md-12">
              <label for=""><?=\B2B\B2BFactory::szmfieldName($key)?></label>
              <input type="text" name="szallitas[<?=$key?>]" value="<?=$szallitas[$key]?>" class="form-control">
            </div>
          </div>
          <br>
          <? }?>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-12">
          <button class="btn btn-success" name="saveUser" value="1">Változások mentése <i class="fa fa-save"></i></button>
        </div>
      </div>
      </form>
    </div>
    <div class="col-sm-4">
      <h3>Egyéb adatok</h3>
      <br>
      <div class="row np">
        <div class="col-md-12">
          <label for="">Létrehozva</label><br>
          <?=$u->CreatedAt()?>
        </div>
      </div>
      <br>
      <div class="row np">
        <div class="col-md-12">
          <label for="">Utoljára belépett</label><br>
          <?=($u->Lastlogin())?$u->Lastlogin():'n.a.'?>
        </div>
      </div>
      <br>
      <div class="row np">
        <div class="col-md-12">
          <label for="">Utoljára frissítve</label><br>
          <?=($u->Lastupdate())?$u->Lastupdate():'n.a.'?>
        </div>
      </div>
      <br>
      <div class="row np">
        <div class="col-md-12">
          <label for="">Státusz</label><br>
          <? if($u->Active()): ?>
          <div class="label label-success">Aktivált</div>
          <? else: ?>
            <div class="label label-warning">Inaktivált</div>
          <? endif; ?>
        </div>
      </div>
      <div class="divider spaced"></div>
      <h3>Műveletetek végrehajtása</h3>
      <? if($u->Active()): ?>
      <a href="/b2b/users/deactivate/<?=$u->ID()?>" class="btn btn-warning form-control"><i class="fa fa-times"></i> Fiók felfüggesztés</a>
      <? else: ?>
      <a href="/b2b/users/activate/<?=$u->ID()?>" class="btn btn-success form-control"><i class="fa fa-plus-circle"></i> Fiók aktiválás</a>
      <? endif; ?>
    </div>
  </div>
  <br><br><br><br>
  <div class="row">
    <div class="row">
      <div class="col-md-12">
        <div class="divider spaced"></div>
        <h3>Fiók eltávolítása (nem visszavonható)</h3>
      </div>
    </div>
    <div class="col-md-12">

      <a href="/b2b/users/delete/<?=$u->ID()?>" class="btn btn-danger"><i class="fa fa-trash"></i> Fiók végleges törlése</a>
    </div>
  </div>
</div>
