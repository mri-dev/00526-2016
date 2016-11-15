<? $this->render('b2b/menu'); ?>
<div class="b2b-dashboard">
  <div class="row">
    <div class="col-md-4">
      <?php
        $users = $this->stats->users();
      ?>
      <div class="stat-box">
        <div class="line yellow">
          <div class="n"><?=\Helper::cashFormat($users['total'])?> <span class="me">db</span></div>
          <div class="t">B2B felhasználó</div>
        </div>
        <div class="line yellow">
          <div class="n"><?=\Helper::cashFormat($users['last_month'])?> <span class="me">db</span></div>
          <div class="t">felhasználó bejelentkezett az elmúlt 30 napban</div>
        </div>
        <?php
          $forgalom = $this->stats->traffics();
        ?>
        <div class="line red">
          <div class="n"><?=\Helper::cashFormat($forgalom['day30'])?> <span class="me">FT</span></div>
          <div class="t">forgalom az elmúlt 30 napban</div>
        </div>
        <div class="line red">
          <div class="n"><?=\Helper::cashFormat($forgalom['day90'])?> <span class="me">FT</span></div>
          <div class="t">forgalom az elmúlt 90 napban</div>
        </div>
        <div class="line red">
          <div class="n"><?=\Helper::cashFormat($forgalom['day365'])?> <span class="me">FT</span></div>
          <div class="t">forgalom az elmúlt évben</div>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div>
        <div class="box">
          <?php
            $ordertypes = $this->stats->ordertypes();
          ?>
          <h4 class="center">Folyamatban lévő megrendelések</h4>
          <div id="ordertypes-chart" style="height: 400px; width: 100%;"></div>
          <?php foreach ($ordertypes as $k => $v): ?>
          <div class="row np" style="margin: 10px 0; font-size: 1.3em;">
            <div class="col-md-6"><strong style="color:<?=$v['color']?>;"><?=$v['name']?></strong></div>
            <div class="col-md-6 right"><?=$v['count']?> db</div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
  <br>
  <div class="row">
    <div class="col-md-12">
      <?php
        $orders = $this->stats->orders( 12 );
      ?>
      <div class="box">
        <h3 class="center">Teljesített megrendelések száma az elmúlt 12 hónapban</h3>
        <div id="orders-chart" style="height: 400px; width: 100%;"></div>
      </div>
    </div>
  </div>
</div>
<script>
var chart;
var orderchart;
var status_row_ids = {
  <?php $cin = -1; foreach ($ordertypes as $key => $o): $cin++; ?>
  <?=$cin?>: <?=$key?>,
  <?php endforeach; ?>
};
google.charts.load("current", {packages:["corechart"]});
google.charts.setOnLoadCallback(drawChart);
google.charts.setOnLoadCallback(drawOrderChart);
function drawChart() {
  var colors = {
  <?php $cin = -1; foreach ($ordertypes as $key => $o): $cin++; ?>
  <?=$cin?>: {color: '<?=$o['color']?>'},
  <?php endforeach; ?>
  };
  var data = google.visualization.arrayToDataTable([
    ['Státuszok', 'db'],
    <?php foreach ($ordertypes as $key => $o): ?>
    ['<?=$o['name']?>', <?=$o[count]?>],
    <?php endforeach; ?>
  ]);

  var options = {
    slices: colors,
    pieSliceText: 'value',
    legend: 'none',
    chartArea: {'width': '100%', 'height': '80%'},
    sliceVisibilityThreshold: .1
  };

  chart = new google.visualization.PieChart(document.getElementById('ordertypes-chart'));
  chart.draw(data, options);
}

function drawOrderChart() {
  var colors = {
    0: {color: 'blue'},
    1: {color: 'red'},
  };
  var data = new google.visualization.DataTable();

  data.addColumn('string', 'Megrendelések száma');
  data.addColumn('number', 'Sima megrendelés');
  data.addColumn({type: 'number', role: 'annotation'});
  data.addColumn('number', 'B2B megrendelés');
  data.addColumn({type: 'number', role: 'annotation'});


  data.addRows([
    <?php foreach ($orders as $key => $o): ?>
    ['<?=$key?>', <?=$o[normal]?>, <?=$o[normal]?>, <?=$o[b2b]?>, <?=$o[b2b]?>],
    <?php endforeach; ?>
  ]);

  var options = {
    slices: colors,
    chartArea: {'width': '100%', 'height': '80%'},
    sliceVisibilityThreshold: .3,
    pointSize: 10,
    legend: { position: 'bottom' }
  };

  orderchart = new google.visualization.LineChart(document.getElementById('orders-chart'));
  orderchart.draw(data, options);
}
</script>
