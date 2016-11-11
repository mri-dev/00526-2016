<h1>Megrendelések</h1>

<div class="order-statuses">
  <div class="row">
    <div class="col-md-4">
      <div class="status"></div>
    </div>
    <div class="col-md-4">
      <div class="status"></div>
    </div>
    <div class="col-md-4">
      <div class="status"></div>
    </div>
  </div>
</div>

<div class="order-table">
  <div class="header">
    <div class="row">
      <div class="col-md-3"><div>Rendelés azonosító</div></div>
      <div class="col-md-3 center"><div>Állapot</div></div>
      <div class="col-md-3 center"><div>Végösszeg</div></div>
      <div class="col-md-3 center"><div>Rendelés leadva</div></div>
    </div>
  </div>
  <div class="body">
    <?php foreach ( $this->orders->get() as $o ): ?>
      <div class="row np main-ord-row" data-id="<?=$o['ID']?>" data-order="<?=$o['azonosito']?>"  data-status="<?=$o['allapot']?>">
        <div class="col-md-3 ord-id">
          <div class="id"><a title="Kattintson a megrendelés részleteiért." href="javascript:void(0);"><?=$o['azonosito']?> <i class="fa fa-table"></i></a></div>
        </div>
        <div class="col-md-3 ord-status center"><strong style="color: <?=$this->orders->status_text_colors[$o['allapot']]?>;"><?=$this->orders->status_texts[$o['allapot']]?></strong></div>
        <div class="col-md-3 ord-sumprice center"><?=\Helper::cashFormat($o['vegosszeg'])?> Ft</div>
        <div class="col-md-3 ord-date center"><?=$o['idopont']?></div>
      </div>
      <div class="row info-row order<?=$o['ID']?>">
        <h3>Tételek</h3>
        <div class="items">
        <?php foreach ($o['items']['list'] as $ti): ?>
          <div class="row item">
            <div class="col-md-5">
              <div class="">
                <?=$ti['me']?> x <strong class="name"><?=$ti['termek']?></strong>
              </div>
              <em><?=($ti['szin'])?'Szín: <strong>'.$ti['szin'].'</strong>':''?><?=($ti['meret'])?'&nbsp;&nbsp; Méret: <strong>'.$ti['meret'].'</strong>':''?></em>
            </div>
            <div class="col-md-3 center"><em><?=$ti['code']?></em></div>
            <div class="col-md-2 center"><?=\Helper::cashFormat($ti['egysegAr'])?> Ft</div>
            <div class="col-md-2 center"><strong><?=\Helper::cashFormat($ti['egysegAr']*$ti['me'])?> Ft</strong></div>
          </div>
        <?php endforeach; ?>
          <div class="row extra-row row-szallitas">
            <div class="col-md-8 right">
              Termékek összesen:
            </div>
            <div class="col-md-4 right">
              <strong><?=\Helper::cashFormat($o['items']['sum'])?> Ft</strong>
            </div>
          </div>
          <div class="row extra-row row-szallitas">
            <div class="col-md-8 right">
              Szállítási költség:
            </div>
            <div class="col-md-4 right">
              <strong><?=\Helper::cashFormat($o['szallitasi_koltseg'])?> Ft</strong>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
