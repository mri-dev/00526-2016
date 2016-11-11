<ul>
  <li class="<?=($this->gets[1] == '')?'on':''?>"><a href="/b2b/"><i class="fa fa-cubes"></i> Megrendelések <?=($this->orders->not_finished_count != 0) ? '<span class="onv">'.$this->orders->not_finished_count.'</span>':''?></a></li>
  <li class="<?=($this->gets[1] == 'beallitasok')?'on':''?>"><a href="/b2b/beallitasok"><i class="fa fa-gear"></i> Beállítások</a></li>
  <li class="logout"><a href="/b2b/logout"><i class="fa fa-sign-out"></i> Kijelentkzés</a></li>
</ul>
