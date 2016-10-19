<? $this->render('b2b/users/menu'); ?>
<?
  $u = $this->u;
?>
<div class="b2b-user-edit-view">
  <h2><strong><?=$u->Name()?></strong> szerkesztése</h2>

  <div class="row np">
    <div class="col-sm-8">
      <h3>Fiókdatok</h3>
      <h3>Kapcsolattartó</h3>
    </div>
    <div class="col-sm-4">
      <h3>Végrehajtás</h3>
    </div>
  </div>
</div>
