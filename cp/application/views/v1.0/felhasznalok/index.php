<script type="text/javascript">
	$(function(){
		$('.termeklista i.vtgl').click(function(){
			visibleToggler($(this));
		});		
	})
	function visibleToggler(e){
		var id 		= e.attr('fid'); 
		var src 	= e.attr('class').indexOf('check');
		var mode 	= e.attr('mode');
		
	 	if(src >= 0){
			e.removeClass('fa-check').addClass('fa-spinner fa-spin');
			doChange(e, mode, id, false);
		}else{
			e.removeClass('fa-times').addClass('fa-spinner fa-spin');
			doChange(e, mode, id, true);
		}	
	}
	function doChange(e, mode, id, show){
		var v = (show) ? '1' : '0';
		$.post("<?=AJAX_POST?>",{
			type : 'userChangeActions',
			mode : mode,
			id 	: id,
			val : v
		},function(d){
			if(!show){
				e.removeClass('fa-spinner fa-spin').addClass('fa-times');
			}else{
				e.removeClass('fa-spinner fa-spin').addClass('fa-check');
			}
		},"html");
	}
</script>
<h1>Felhasználók <span><strong><?=Helper::cashFormat($this->users[info][total_num])?> db</strong> felhasználó <? if($_COOKIE[filtered] == '1'): ?><span class="filtered">Szűrt listázás <a href="/<?=$this->gets[0]?>/clearfilters/" class="btn btn-danger">eltávolítás</a></span><? endif; ?></span></h1>
<?
	$str = array(
		'nev' => 'Név',
		'uhsz' => 'Utca, házszám',
		'irsz' => 'Irányítószám',
		'city' => 'Város',
		'phone' => 'Telefonszám'
	);
?>
<form action="" method="post">
<table class="table termeklista table-bordered">
	<thead>
    	<tr>
			<th title="Felhasználó ID" width="40">#</th>
	        <th>Név</th>
            <th width="200">E-mail</th>
            <th width="120">Számlázási adat</th>
            <th width="120">Szállítási adat</th>
            <th width="100" title="Megrendeléseinek összesített értéke">Fizetett össz.</th>
            <th width="50">Kedvezménye</th>
            <th width="100">Engedélyezve</th>
            <th width="100">Aktiválva</th>
            <th width="120">Utoljára belépett</th>
            <th width="120">Regisztrált</th>
            <th width="20"></th>
        </tr>
	</thead>
    <tbody>
    	<tr class="search <? if($_COOKIE[filtered] == '1'): ?>filtered<? endif;?>">
    		<td><input type="text" name="ID" class="form-control" value="<?=$_COOKIE[filter_ID]?>" /></td>
    		<td><input type="text" name="nev" class="form-control" placeholder="felhasználó neve..." value="<?=$_COOKIE[filter_nev]?>" /></td>
            <td><input type="text" name="email" class="form-control" placeholder="e-mail cím..." value="<?=$_COOKIE[filter_email]?>" /></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><select class="form-control"  name="engedelyezve" style="max-width:100px;">
            	<option value="" <?=(!$_COOKIE[filter_engedelyezve])?'selected':''?>># Mind</option>
                	<option value="0" <?=($_COOKIE[filter_engedelyezve] == '0')?'selected':''?>>Nem</option>
                    <option value="1" <?=($_COOKIE[filter_engedelyezve] == '1')?'selected':''?>>Igen</option>
                </select></td>
    		<td><select class="form-control"  name="aktivalva" style="max-width:100px;">
            	<option value="" selected># Mind</option>
                	<option value="0" <?=($_COOKIE[filter_aktivalva] == '0')?'selected':''?>>Nem</option>
                    <option value="1" <?=($_COOKIE[filter_aktivalva] == '1')?'selected':''?>>Igen</option>
                </select></td>
            <td></td>
            <td></td>
    		<td align="center">
            	<button name="filterList" class="btn btn-default"><i class="fa fa-search"></i></button>
            </td>
    	</tr>
    	<? if(count($this->users[data]) > 0): foreach($this->users[data] as $d):  ?>
    	<tr>
	    	<td align="center"><?=$d[ID]?></td>
	        <td>
          		<strong><?=$d[nev]?></strong>
            </td>
            <td align="center"><?=$d[email]?></td>
            <td>
            	<? $szam = json_decode($d[szamlazasi_adatok],true); ?>
                <? if( !is_null($szam) ): foreach($szam as $szk => $szv): ?>
					<div title="<?=$str[$szk]?>"><?=$szv?></div>
                <? endforeach; else: ?> &mdash; hiányzó adat &mdash;  <? endif; ?>
            </td>
            <td>
            	<? $szall = json_decode($d[szallitasi_adatok],true); ?>
                <? if( !is_null($szall) ): foreach($szall as $szk => $szv): ?>
					<div title="<?=$str[$szk]?>"><?=$szv?></div>
                <? endforeach; else: ?> &mdash; hiányzó adat &mdash;  <? endif; ?>
            </td>
            <td align="center">
            	<?=Helper::cashFormat($d[totalOrderPrices])?> Ft
            </td>
            <td align="center">
            	<?=$d[total_data][kedvezmeny]?>%
            </td>
            <td align="center"><?=($d[engedelyezve] == 1)?'<i title="Engedélyezve" mode="engedelyezve" class="fa fa-check vtgl" fid="'.$d[ID].'"></i>':'<i mode="engedelyezve" class="fa fa-times vtgl" fid="'.$d[ID].'" title="Tiltva"></i>'?></td>
            <td align="center"><?=(!is_null($d[aktivalva]))?'<i title="Aktiválva" class="fa fa-check"></i>':'<i class="fa fa-times" title="Nincs aktiválva"></i>'?></td>
            <td align="center"><?=Helper::softDate($d[utoljara_belepett])?>	<br><em>(<?=Helper::distanceDate($d[utoljara_belepett])?>)</em></td>
            <td align="center"><?=Helper::softDate($d[regisztralt])?> <br><em>(<?=Helper::distanceDate($d[regisztralt])?>)</em></td>
            <td></td>
        </tr>
        <? endforeach; else: ?>
        <tr>
	    	<td colspan="15" align="center">
            	<div style="padding:25px;">Nincs találat!</div>
            </td>
        </tr>
        <? endif; ?>
    </tbody>
</table>
</form>
<ul class="pagination">
  <li><a href="/<?=$this->gets[0]?>/<?=($this->gets[1] != '')?$this->gets[1].'/':'-/'?>1">&laquo;</a></li>
  <? for($p = 1; $p <= $this->users[info][pages][max]; $p++): ?>
  <li class="<?=(Helper::currentPageNum() == $p)?'active':''?>"><a href="/<?=$this->gets[0]?>/<?=($this->gets[1] != '')?$this->gets[1].'/':'-/'?><?=$p?>"><?=$p?></a></li>
  <? endfor; ?>
  <li><a href="/<?=$this->gets[0]?>/<?=($this->gets[1] != '')?$this->gets[1].'/':'-/'?><?=$this->users[info][pages][max]?>">&raquo;</a></li>
</ul>