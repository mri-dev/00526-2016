<h1>Megrendelések</h1>
<?php if ($this->orders->count == 0): ?>
  <div class="center">
    <h3>Önnek nincsen korábban leadott megrendelése.</h3>
    Böngésszen ajánlataink között és rendelje meg gyorsan, egyszerűen termékeinket! <br><br>
    <img src="<?=IMG?>icons/favicon.ico" alt="<?=$this->settings['page_author']?>"><br>
    <small>&mdash; <?=$this->settings['page_author']?> &mdash;</small> <br>
  </div>
<?php else: ?>
<div class="order-statuses">
  <div class="row">
    <div class="col-md-16">
      <div class="status">
        <div id="orderstatuses" style="width: 100%; height: 250px;"></div>
      </div>
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
          <div class="id"><a title="Kattintson a megrendelés részleteiért." href="javascript:void(0);" onclick="showOrder(<?=$o['ID']?>);" ><?=$o['azonosito']?> <i class="fa fa-table"></i></a></div>
        </div>
        <div class="col-md-3 ord-status center"><strong style="color: <?=$this->orders->status_text_colors[$o['allapot']]?>;"><?=$this->orders->status_texts[$o['allapot']]?></strong></div>
        <div class="col-md-3 ord-sumprice center"><?=\Helper::cashFormat($o['items']['sum']*AFA+$o['szallitasi_koltseg'])?> Ft</div>
        <div class="col-md-3 ord-date center"><?=$o['idopont']?></div>
      </div>
      <div class="row info-row order<?=$o['ID']?>">
        <div class="col-md-12">
          <div class="itemslist">
            <div class="row heading">
              <div class="col-md-5 left">
                <strong>Termék</strong>
              </div>
              <div class="col-md-3 center">
                <strong>Cikkszám</strong>
              </div>
              <div class="col-md-2 center">
                <strong>Egységár</strong>
              </div>
              <div class="col-md-2 center">
                <strong>Ár</strong>
              </div>
            </div>
            <div class="items">
            <?php foreach ($o['items']['list'] as $ti): ?>
              <div class="row item">
                <div class="col-md-5">
                  <div class="name">
                    <?=$ti['me']?>x &nbsp; <a href="<?=$ti[url]?>" title="Termék adatlap" target="_blank"><strong class="name"><?=$ti['termek']?></strong></a>
                  </div>
                  <em><?=($ti['szin'])?'Szín: <strong>'.$ti['szin'].'</strong>':''?><?=($ti['meret'])?'&nbsp;&nbsp; Méret: <strong>'.$ti['meret'].'</strong>':''?></em>
                </div>
                <div class="col-md-3 center"><em><?=$ti['code']?></em></div>
                <div class="col-md-2 center"><?=\Helper::cashFormat($ti['egysegAr'])?> Ft <span class="pafa">+ ÁFA</span></div>
                <div class="col-md-2 center"><strong><?=\Helper::cashFormat($ti['egysegAr']*$ti['me'])?> Ft</strong> <span class="pafa">+ ÁFA</span></div>
              </div>
            <?php endforeach; ?>
              <div class="row extra-row">
                <div class="col-md-10 right">
                  Termékek összesen:
                </div>
                <div class="col-md-2 center">
                  <strong><?=\Helper::cashFormat($o['items']['sum'])?> Ft</strong> <span class="pafa">+ ÁFA</span>
                </div>
              </div>
              <div class="row extra-row">
                <div class="col-md-10 right">
                  Termékek vételára:
                </div>
                <div class="col-md-2 center">
                  <strong><?=\Helper::cashFormat($o['items']['sum']*AFA)?> Ft</strong>
                </div>
              </div>
              <div class="row extra-row row-szallitas">
                <div class="col-md-10 right">
                  Szállítási költség:
                </div>
                <div class="col-md-2 center">
                  <strong><?=\Helper::cashFormat($o['szallitasi_koltseg'])?> Ft</strong>
                </div>
              </div>
              <div class="row extra-row">
                <div class="col-md-10 right">
                  Végösszeg:
                </div>
                <div class="col-md-2 center">
                  <strong><?=\Helper::cashFormat($o['items']['sum']*AFA+$o['szallitasi_koltseg'])?> Ft</strong></span>
                </div>
              </div>
              <div class="row extra-row row-szallitas">
                <div class="col-md-12 right">
                  <a href="/order/<?=$o['accessKey']?>" target="_blank" class="btn btn-primary btn-sm">Részletes adatlap >></a>
                  <?php if ($o['allapot'] == 7 && $o['payu_fizetve'] == 0): ?>
                    <a href="/order/<?=$o['accessKey']?>" target="_blank" class="btn btn-danger btn-sm"> Online kártyás fizetés >></a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>
<script>
  var chart;
  var status_row_ids = {
    <?php $cin = -1; foreach ($this->orders->status_code_counts as $key => $count): $cin++; ?>
    <?=$cin?>: <?=$key?>,
    <?php endforeach; ?>
  };
  google.charts.load("current", {packages:["corechart"]});
  google.charts.setOnLoadCallback(drawChart);
  function drawChart() {
    var colors = {
    <?php $cin = -1; foreach ($this->orders->status_code_counts as $key => $count): $cin++; ?>
    <?=$cin?>: {color: '<?=$this->orders->status_text_colors[$key]?>'},
    <?php endforeach; ?>
    };
    var data = google.visualization.arrayToDataTable([
      ['Státuszok', 'db'],
      <?php foreach ($this->orders->status_code_counts as $key => $count): ?>
      ['<?=$this->orders->status_texts[$key]?>', <?=$count?>],
      <?php endforeach; ?>
    ]);

    var options = {
      pieHole: 0.35,
      slices: colors,
      pieSliceText: 'value',
      chartArea: {'width': '100%', 'height': '80%'}
    };

    chart = new google.visualization.PieChart(document.getElementById('orderstatuses'));
    chart.draw(data, options);
    google.visualization.events.addListener(chart, 'select', filterOrderByStatus);
  }

  function filterOrderByStatus(e) {
    var sel = chart.getSelection();

    if (sel && sel[0] != null) {
      var sid = status_row_ids[sel[0].row];

      $('.order-table *[data-status]').each(function(i,e){
        if ($(this).data('status') != sid) {
          $(this).addClass('hide');
        } else {
          $(this).removeClass('hide');
        }
      });
    } else {
      $('.order-table *[data-status].hide').removeClass('hide');
    }
  }

  function showOrder( id ) {
    var tr = $('.order'+id);
    if (tr.hasClass('opened')) {
      tr.removeClass('opened');
    } else {
      tr.addClass('opened');
    }

  }
</script>
