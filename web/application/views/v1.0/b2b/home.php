<div class="b2b-view dashboard-page">
  <div class="b2busertop">
    <h1>Arena&reg; B2B ügyfélközpont <strong>Üdvözöljük, <?=$this->user['data']['nev']?>!</strong></h1>
  </div>
  <div class="fullwidth-container">
    <?=$this->rmsg?>
    <div class="sidebarmenu">
      <? $this->render('b2b/sidebar'); ?>
    </div>
    <div class="b2b-content-holder">
      <div class="b2b-content-wrapper">
        <? if(isset($this->gets['1'])): $this->render('b2b/sub/'.$this->gets['1']); else: $this->render('b2b/sub/dashboard'); endif;  ?>
      </div>
    </div>
  </div>
</div>
