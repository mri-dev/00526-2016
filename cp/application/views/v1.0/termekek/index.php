<div style="float:right;">
    <a href="/termekek/import" class="btn btn-default"><i class="fa fa-newspaper-o"></i> termékek importálása</a>
    <a href="/termekek/kepek" class="btn btn-default"><i class="fa fa-picture-o"></i> képek frissítése</a>
    <a href="/termekek/fizetesi_mod" class="btn btn-default"><i class="fa fa-money"></i> fizetési módok</a>
	<a href="/termekek/szallitasi_mod" class="btn btn-default"><i class="fa fa-truck"></i> szállítási módok</a>
	<a href="/termekek/szallitasi_ido" class="btn btn-default"><i class="fa fa-clock-o"></i> szállítási idők</a>
	<a href="/termekek/uj" class="btn btn-info"><i class="fa fa-plus-circle"></i> új termék</a>
</div>
<h1>Termékek <span><strong><?=Helper::cashFormat($this->products->getItemNumbers())?> db</strong> termék <? if($_COOKIE[filtered] == '1'): ?><span class="filtered">Szűrt termék listázás <a href="/termekek/clearfilters/" title="szűrés eltávolítása" class="actions"><i class="fa fa-times-circle"></i></a></span><? endif; ?></span></h1>
<?=$this->rmsg?>
<div style="float:right;">
	<?=$this->fb_login_status?>
</div>
<?=$this->navigator?>
<div>
	<span class="label label-default"><input type="checkbox" id="showKats" /> részletek mutatása</span>
</div>
<div class="clr"></div>
<? if($this->gets[2] != 'del'): ?>
<form action="/termekek/1/" method="post">
<div class="tbl-container overflowed">
<table class="table termeklista table-bordered">
	<thead>
    	<tr>
			<th title="Termék ID" width="50">#</th>
			<? if( true ): ?>
			<th width="80">#Cikkszám</th>
			<? endif; ?>
	        <th>Elnevezés</th>
            <th width="60">Méret</th>
            <th width="110">Szín</th>
            <th width="120">Márka</th>
            <th width="80">Nettó ár</th>
            <th width="80">Bruttó ár</th>
            <th width="80">Ár</th>
            <th width="90">Egyedi ár</th>
            <th width="100">Száll. idő</th>
            <th width="120">Állapot</th>
            <th width="65">Készlet</th>
            <th width="75">Aktív</th>
            <!-- <th width="20" title="Főtermék">Fő</th>-->
            <th width="20"></th>
        </tr>
	</thead>
    <tbody>
    	<tr class="search <? if($_COOKIE['filtered'] == '1'): ?>filtered<? endif;?>">
    		<td><input type="text" name="ID" class="form-control" value="<?=$_COOKIE['filter_ID']?>" /></td>
    		<? if( true ): ?>
			<td>
				<input type="text" name="cikkszam" placeholder="azonosító..." class="form-control" value="<?=$_COOKIE['filter_cikkszam']?>" />
			</td>
			<? endif; ?>
    		<td><input type="text" name="nev" class="form-control" placeholder="termék elnevezése..." value="<?=$_COOKIE['filter_nev']?>" /></td>
            <td><input type="text" name="meret" class="form-control" placeholder="méret..." value="<?=$_COOKIE['filter_meret']?>" /></td>
            <td><input type="text" name="szin" class="form-control" placeholder="szín..." value="<?=$_COOKIE['filter_szin']?>" /></td>
    		<td><select class="form-control"  name="marka" style="max-width:150px;">
            	<option value="" selected="selected"># Mind</option>
                	<? foreach($this->markak as $m): ?>
                    <option value="<?=$m['ID']?>" <?=($m['ID'] == $_COOKIE['filter_marka'])?'selected':''?>><?=$m['neve']?></option>
                    <? endforeach; ?>
                </select></td>
    		<td></td>
    		<td></td>
    		<td></td>
            <td></td>
    		<td><select class="form-control"  name="szallitasID" style="max-width:150px;">
            <option value="" selected="selected"># Mind</option>
				<? foreach($this->szallitas as $sz): ?>
                <option value="<?=$sz['ID']?>" <?=($sz['ID'] == $_COOKIE['filter_szallitasID'])?'selected':''?>><?=$sz['elnevezes']?></option>
                <? endforeach; ?>
            </select></td>
    		<td><select class="form-control"  name="keszletID" style="max-width:150px;">
            <option value="" selected="selected"># Mind</option>
				<? foreach($this->keszlet as $k): ?>
                <option value="<?=$k['ID']?>" <?=($k['ID'] == $_COOKIE['filter_keszletID'])?'selected':''?>><?=$k['elnevezes']?></option>
                <? endforeach; ?>
            </select></td>
            <td></td>
            <td align="center"><select class="form-control"  name="lathato" style="max-width:150px;">
                <option value="" selected="selected">X / ✓</option>
               <option value="0" <?=('0' == $_COOKIE['filter_lathato'])?'selected':''?>>X</option>
               <option value="1" <?=('1' == $_COOKIE['filter_lathato'])?'selected':''?>>✓</option>
            </select></td>
            <!--<td align="center">
                <input type="checkbox" name="fotermek" <?=($_COOKIE['filter_fotermek'] == '1')?'checked="checked"':''?> >
            </td>-->
    		<td align="center">
            	<button name="filterList" class="btn btn-default"><i class="fa fa-search"></i></button>
            </td>
    	</tr>
    	<? if( $this->products->hasItems() ): foreach($this->termekek as $d):  ?>
    	<tr>
	    	<td align="center">
				<?=$d['product_id']?><br />
				<input type="checkbox" name="selectedItem[]" value="<?=$d['product_id']?>" />
			</td>
			<? if( true ): ?>
			<td>
				<input type="text" class="form-control action" mode="cikkszam" tid="<?=$d['product_id']?>" min="0" value="<?=$d['cikkszam']?>" />
			</td>
			<? endif; ?>
	        <td>
            <div class="ind">
              	<? if($d['pickpackszallitas'] == '0'): ?> <img src="<?=IMG?>icons/no_ppp.png" alt="Pick Pack Pont" title="PickPackPont-ra NEM szállítható" /><? endif; ?>
                <? if($d['akcios'] == '1'): ?><span class="akcios itemInf" itemId="<?=$d['product_id']?>" title="Akciós termék">A</span><? endif; ?>
                <? if($d['ujdonsag'] == '1'): ?><span class="ujdonsag itemInf" title="Újdonság termék">U</span><? endif; ?>
            </div>
            <div class="img"><a class="zoom" href="<?=$d['profil_kep']?>" target="_blank"><img src="<?=$d['profil_kep_small']?>" alt="" /></a></div>
			<div class="nev">
				<a title="Szerkesztés" href="/termekek/t/edit/<?=$d['product_id']?>" style="color:black;" ><?=$d['product_nev']?></a>
            </div>
            <div class="inkat">
                <em title="Kulcsszavak"><?=$d['kulcsszavak']?></em>
            </div>
            <? if( true ): ?>
            	<span class="modkat">
                    <strong><a href="javascript:void(0);" title="Kategóriába listázva" class="itemInf" itemId="<?=$d['product_id']?>"><?=count($d[inKatList])?> <i class="fa fa-th-list"></i></a></strong><? if(count($d['hasonlo_termek_ids']['ids']) > 0): ?>&nbsp;&nbsp;&nbsp;<span title="Termék variáció kapcsolatok száma"><?=count($d['hasonlo_termek_ids']['ids'])?> <i class="fa fa-th"></i></span><? endif;?>
                </span>
            <? endif; ?>
            </td>
            <td>
                <input type="text" class="form-control action" mode="meret" tid="<?=$d['product_id']?>" value="<?=$d['meret']?>" />
            </td>
            <td>
                <input type="text" class="form-control action" mode="szin" tid="<?=$d['product_id']?>" value="<?=$d['szin']?>" />
            </td>
            <td>
            	<select class="form-control  action"  mode="marka" tid="<?=$d['product_id']?>" style="max-width:120px;">
                	<? foreach($this->markak as $m): ?>
                    <option value="<?=$m[ID]?>" <?=($m['ID'] == $d['marka_id'])?'selected':''?>><?=$m['neve']?></option>
                    <? endforeach; ?>
                </select>
            </td>
            <td>
            	<input type="number" step="any" class="form-control action" mode="netto_ar" tid="<?=$d['product_id']?>" min="0" value="<?=$d[netto_ar]?>" />
                <? if($d[akcios] == '1'): ?>
				<input type="number" step="any" class="form-control action" mode="akcios_netto_ar" tid="<?=$d['product_id']?>" min="0" value="<?=$d[akcios_netto_ar]?>" />
				<? endif;?>
                </td>
            <td>
            <input type="number" step="any" class="form-control action" mode="brutto_ar" tid="<?=$d['product_id']?>" min="0" value="<?=$d[brutto_ar]?>" />
            <? if($d[akcios] == '1'): ?>
				<input type="number" step="any" class="form-control action" mode="akcios_brutto_ar" tid="<?=$d['product_id']?>" min="0" value="<?=$d[akcios_brutto_ar]?>" />
				<? endif;?>
            </td>
            <td align="center">
				<? if($d[akcios] == '0' || ($d[akcios] == '1' && (int)$d['akcios_brutto_ar'] == 0)): ?>
					<?=Helper::cashFormat($d[ar])?> Ft
                <? else: ?>
                	<div><strike style="color:red;"><?=Helper::cashFormat($d[ar])?> Ft</strike></div>
                    <div style="color:green;"><?=Helper::cashFormat($d[akcios_fogy_ar])?> Ft</div>
                <? endif; ?>
                <div class="arres">(<?=number_format(($d[ar] - $d[brutto_ar]) / ($d[ar] / 100),2,"."," ")?>%)</div>
            </td>
            <td align="center">
				<input type="number" step="any" class="form-control action" mode="egyedi_ar" tid="<?=$d['product_id']?>" min="0" value="<?=$d[egyedi_ar]?>" />
            	<? if(!is_null($d[egyedi_ar])): ?>
                <div class="arres">(<?=number_format(($d[egyedi_ar] - $d[brutto_ar]) / ($d[egyedi_ar] / 100),2,"."," ")?>%)</div>
                <? endif; ?>
            </td>
	        <td>
			<select class="form-control  action" mode="szallitasi_ido" tid="<?=$d['product_id']?>" style="max-width:150px;">
				<? foreach($this->szallitas as $sz): ?>
                <option value="<?=$sz[ID]?>" <?=($sz[ID] == $d[szallitasID])?'selected':''?>><?=$sz[elnevezes]?></option>
                <? endforeach; ?>
            </select></td>
            <td>
            <select class="form-control  action" mode="allapot" tid="<?=$d['product_id']?>" style="max-width:150px;">
				<? foreach($this->keszlet as $k): ?>
                <option value="<?=$k[ID]?>" <?=($k[ID] == $d[keszletID])?'selected':''?>><?=$k[elnevezes]?></option>
                <? endforeach; ?>
            </select>
            </td>
            <td align="center">
				<input type="number" step="any" class="form-control action" mode="raktar_keszlet" tid="<?=$d['product_id']?>" min="-1" value="<?=$d['raktar_keszlet']?>" />
            </td>
            <td align="center"><? if($d['lathato'] == '1'): ?><i class="fa fa-check vtgl" title="Aktív / Kattintson az inaktiváláshoz" tid="<?=$d['product_id']?>"></i><? else: ?><i class="fa fa-times vtgl" title="Inaktív / Kattintson az aktiváláshoz" tid="<?=$d['product_id']?>"></i><? endif; ?></td>
            <!--<td align="center"><? if($d['fotermek'] == '1'): ?><i class="fa fa-check ftgl" title="Főtermék / Kattintson az inaktiváláshoz" tid="<?=$d['product_id']?>"></i><? else: ?><i class="fa fa-times ftgl" title="Nem főtermék / Kattintson az aktiváláshoz" tid="<?=$d['product_id']?>"></i><? endif; ?></td>-->
            <td align="center">
            <div class="dropdown">
            	<i class="fa fa-gears dropdown-toggle" title="Beállítások" id="dm<?=$d['product_id']?>" data-toggle="dropdown"></i>
                  <ul class="dropdown-menu" role="menu" aria-labelledby="dm<?=$d['product_id']?>">
                  	<li role="presentation"><a role="menuitem" tabindex="-1" href="/termekek/t/edit/<?=$d['product_id']?>">szerkesztés <i class="fa fa-pencil"></i></a></li>
				    <li role="presentation"><a role="menuitem" tabindex="-1" href="/termekek/t/del/<?=$d['product_id']?>">törlés <i class="fa fa-times"></i></a></li>
				  </ul>
            </div>
            </td>
        </tr>
        <tr>
          <td colspan="99" class="in i<?=$d['product_id']?>" style="display:none;">
          	<div class="row">

                <div align="left" class="col-md-6">
                    <h4>Kategóriákban megjelen</h4>
                    <div class="list">
                        <? if(count($d['inKatList']) > 0): foreach($d['inKatList'] as $kt): ?>
                            <div><strong><?=$kt['neve']?></strong></div>
                        <? endforeach; else:?>
                        <span class="subt"><i class="fa fa-info-circle"></i> nincs kategóriákba sorolva</span>
                        <? endif; ?>
                    </div>
                </div>

                <div align="left" class="col-md-3">
                	<? if($d[akcios] == '1' && $d['akcios_brutto_ar']): ?>
                	<h4>Akciós ár</h4>
                    <div class="list">
                    	<div><strong>Nettó:</strong> <?=Helper::cashFormat($d[akcios_netto_ar])?> Ft</div>
                        <div><strong>Bruttó:</strong> <?=Helper::cashFormat($d[akcios_brutto_ar])?> Ft <em class="arres">(-<?=Helper::cashFormat((int)$d[brutto_ar]/((int)$d[akcios_brutto_ar]/100)-100)?>%)</em></div>
                        <div><strong>Akciós ár:</strong> <?=Helper::cashFormat($d[akcios_fogy_ar])?> Ft</div>
                    </div>
                    <? endif; ?>
                </div>
                <div align="right" class="col-md-3">
                    <h4>Egyéb</h4>
                    <div class="list">
                        <div><strong>PickPackPont-ra szállítás:</strong> <? if($d[pickpackszallitas] == '1'): ?><i class="fa fa-check"></i><? else: ?><i class="fa fa-times"></i><? endif; ?></div>
                    </div>
                </div>
            </div>
          </td>
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
</div>
<? if(true): ?>
	<div class="con padding-vertical-5">
		<div class="tbl">
			<div class="tbl-col" style="border-right:1px solid #ddd; width:40px;" align="center">
				<input type="checkbox" id="selectAll" />
			</div>
			<div class="tbl-col">
				<div class="padding-horizontal-5">
				<!-- Actions -->
					<select name="selectAction" id="selectAction" class="form-control">
						<option value=""># Kiválasztott termékek módosítása! Művelet kiválasztása...</option>
						<option value="" disabled></option>
                        <!--<option value="action_variacio">Termékek összekapcsolása (variáció kapcsolatok)</option>-->
                        <option value="action_fotermek">Főtermék állapot cseréje</option>
                        <option value="action_value_szin">Szín cseréje</option>
                        <option value="action_value_meret">Méret cseréje</option>
						<option value="action_marka">Márka cseréje</option>
						<option value="action_szallitasID">Szállítási idő cseréje</option>
						<option value="action_keszletID">Állapot cseréje</option>
						<option value="action_lathato">Aktíválás/Deaktiválás</option>
						<option value="action_ujdonsag">Újdonság állapot</option>
						<option value="action_akcios">Akciós állapot</option>
						<option value="action_akcio_szaz">Akciós termék, ár csökkentés (% alapján)</option>
                        <option value="action_uploadimage">Képek frissítése, új kép feltöltése a termékekhez</option>
                        <option value="action_addtocategory">Termékek kategóriákba csatolása</option>
                        <option value="action_defaultcategory">Alapértelmezett kategória cseréje</option>
						<option value="" disabled></option>
						<option value="action_value_netto_ar">Nettó ár cseréje</option>
						<option value="action_value_akcios_netto_ar">Nettó, akciós ár cseréje</option>
						<option value="action_value_brutto_ar">Bruttó ár cseréje</option>
						<option value="action_value_akcios_brutto_ar">Bruttó, akciós ár cseréje</option>
						<option value="action_value_egyedi_ar">Egyedi ár cseréje</option>
						<option value="action_value_raktar_keszlet">Raktárkészlet mennyiség cseréje</option>
                        <option value="action_value_kulcsszavak">Kulcsszavak cseréje</option>
                        <option value="action_value_linkek">Csatolt linkek cseréje</option>
					</select>
				<!--//Actions -->
				</div>
			</div>
			<div class="tbl-col" style="width:25%;">
                <!-- Action - Kategóriába csatolás -->
                <div id="action_addtocategory" class="hided actionContainer connectintocat">
                   <strong>Jelölje ki a kategóriákat:</strong>
                    <div class="tree overflowed">
                        <? while( $this->categories->walk() ): $item = $this->categories->the_cat(); ?>
                        <div class="item deep<?=$item['deep']?>">
                            <label><input name="action_addtocategory[]" value="<?=$item['ID']?>" type="checkbox"><?=$item['neve']?></label>
                        </div>
                        <? endwhile; ?>
                    </div>
                </div>
                <!--//Action - Kategóriába csatolás -->
                 <!-- Action - Alapértelmezett kategória csere -->
                <div id="action_defaultcategory" class="hided actionContainer">
                    <select name="action_defaultcategory" id="action_defaultcategory" class="form-control">
                        <option value="">-- alapé. kategória kiválasztása --</option>
                        <option value="" disabled></option>
                        <? while( $this->categories->walk() ): $item = $this->categories->the_cat(); ?>
                        <option value="<?=$item['ID']?>"><? for( $is = 1; $is <= $item['deep']; $is++ ){ echo '&mdash;'; }?><?=$item['neve']?></option>
                        <? endwhile; ?>
                    </select>
                </div>
                <!--//Action - Alapértelmezett kategória csere -->
                <!-- Action - Főtermék -->
                <div id="action_fotermek" class="hided actionContainer">
                    <select name="action_fotermek" id="action_fotermek" class="form-control">
                        <option value="">-- új főtermék állapot kiválasztása --</option>
                        <option value="" disabled></option>
                        <option value="1">Legyen főtermék</option>
                        <option value="0">NE legyen főtermék</option>
                    </select>
                </div>
                <!--//Action - Főtermék -->
				<!-- Action - Márka -->
				<div id="action_marka" class="hided actionContainer">
					<select name="action_marka" id="action_marka" class="form-control">
						<option value="">-- új márka kiválasztása --</option>
						<option value="" disabled></option>
						<? foreach($this->markak as $d): ?>
						<option value="<?=$d['product_id']?>"><?=$d[neve]?></option>
						<? endforeach; ?>
					</select>
				</div>
				<!--//Action - Márka -->
				<!-- Action - Szállítási idő -->
				<div id="action_szallitasID" class="hided actionContainer">
					<select name="action_szallitasID" id="action_szallitasID" class="form-control">
						<option value="">-- új szállítási idő kiválasztása --</option>
						<option value="" disabled></option>
						<? foreach($this->szallitas as $d): ?>
						<option value="<?=$d['product_id']?>"><?=$d[elnevezes]?></option>
						<? endforeach; ?>
					</select>
				</div>
				<!--//Action - Szállítási idő -->
				<!-- Action - Állapot -->
				<div id="action_keszletID" class="hided actionContainer">
					<select name="action_keszletID" id="action_keszletID" class="form-control">
						<option value="">-- új állapot kiválasztása --</option>
						<option value="" disabled></option>
						<? foreach($this->keszlet as $d): ?>
						<option value="<?=$d['product_id']?>"><?=$d[elnevezes]?></option>
						<? endforeach; ?>
					</select>
				</div>
				<!--//Action - Állapot -->
				<!-- Action - Akciós -->
				<div id="action_akcios" class="hided actionContainer">
					<select name="action_akcios" id="action_akcios" class="form-control">
						<option value="">-- új állapot kiválasztása --</option>
						<option value="" disabled></option>
						<option value="1">Legyen akciós</option>
						<option value="0">NE legyen akciós</option>
					</select>
				</div>
				<!--//Action - Akciós -->
				<!-- Action - Újdonság -->
				<div id="action_ujdonsag" class="hided actionContainer">
					<select name="action_ujdonsag" id="action_ujdonsag" class="form-control">
						<option value="">-- új állapot kiválasztása --</option>
						<option value="" disabled></option>
						<option value="1">Legyen újdonság</option>
						<option value="0">NE legyen újdonság</option>
					</select>
				</div>
				<!--//Action - Újdonság -->
				<!-- Action - Aktív -->
				<div id="action_lathato" class="hided actionContainer">
					<select name="action_lathato" id="action_lathato" class="form-control">
						<option value="">-- státusz kiválasztása --</option>
						<option value="" disabled></option>
						<option value="0">Deaktiválás</option>
						<option value="1">Aktiválás</option>
					</select>
				</div>
				<!--//Action - Aktív -->
				<!-- Action - Akció százalék -->
				<div id="action_akcio_szaz" class="hided actionContainer">
					<div class="input-group">
						<div class="input-group-addon">-</div>
						<input type="number" step="any"  class="form-control" name="action_akcio_szaz[percent]" >
						<div class="input-group-addon">%</div>
					</div>
					<br>
					<div>
						<select name="action_akcio_szaz[type]" class="form-control">
                            <option value="0" selected="selected">Akciós</option>
                            <option value="" disabled></option>
							<option value="-1">Akciós jelzők eltávolítása</option>

						</select>
					</div>
				</div>
				<!--//Action - Akció százalék -->
			</div>
			<div id="action_value" class="tbl-col actionContainer hided">
				<input type="text" name="action_value" class="form-control" placeholder="érték megadása..." />
			</div>
			<div class="tbl-col" style="width:105px;" align="center">
				<button class="btn btn-success" name="actionSaving" value="true" type="submit">Végrehajtás</button>
			</div>
		</div>
	</div>
<? endif; ?>
</form>
<?=$this->navigator?>
<? endif; ?>
<script type="text/javascript">
	var showMoreInfo = false;
	$(function(){
		autoShowInfos();

		$('#selectAll').change(function(){
			var sa 	= $(this).is(':checked');
			var chs = $('.termeklista').find('input[type=checkbox]');
			if(sa){
				chs.prop("checked", !chs.prop("checked"));
			}else{
				chs.prop("checked", !chs.prop("checked"));
			}
		});

		$('#selectAction').change(function(){
			var v 		= $(this).val();
			var isval 	= v.indexOf('action_value');

			$('.actionContainer').hide();

			if(isval === -1){
				$('#'+v).show();
			}else if(isval === 0){
				$('#action_value').show();
			}
		});

		$('.action').change(function(){
			doAction($(this));
		});
		$('.termeklista i.vtgl').click(function(){
			visibleToggler($(this));
		});
        $('.termeklista i.ftgl').click(function(){
            mainProductToggler($(this));
        });
		$('.itemInf').click(function(){
			var id = $(this).attr('itemId');
			$('.termeklista td.in.i'+id).slideToggle(400);
		});
		$('#showKats').click(function(){
			var ch = $(this).is(':checked');
			console.log('FlagChange: '+ch);
			if(ch){
				localStorage.setItem('showMoreInfoOnTermekList','1');
			}else{
				localStorage.removeItem('showMoreInfoOnTermekList');
			}
			autoShowInfos();
		});
		$('.modkat i').click(function(){
			$('.modkat .shinkat').hide(0);
			$('.modkat i').removeClass('showed');
			var key = $(this).attr('key');
			var sh 	= $(this).attr('sh');


			if(sh == 0){
				$('.modkat #inkatid'+key).show(0).html('<div style="padding:10px; text-align:center;"><i class="fa fa-spinner fa-spin"></i> betöltés...</div>');
				$(this).attr('sh',1);
				$(this).addClass('showed');
				loadKatValaszto(key);
			}else{
				$('.modkat #inkatid'+key).hide(0);
				$(this).attr('sh',0);
			}
		});
	})
	function loadKatValaszto(tid){
		$.post("<?=AJAX_GET?>",{
			type : 'loadCheckKat',
			id 	: tid,
		},function(d){
			$('.modkat #inkatid'+tid).html(d);
		},"html");
	}
	function autoShowInfos(){
		var stored = localStorage.getItem('showMoreInfoOnTermekList');
		console.log('StoredFlag: '+stored);

		if(stored == null){
			showMoreInfo = false;
		}else{
			showMoreInfo = true;
		}

		if(showMoreInfo){
			$('.termeklista .in').show(0);
			$('#showKats').attr('checked', 'true');
		}else{
			$('.termeklista .in').hide(0);
		}

		console.log(showMoreInfo);
	}

    function mainProductToggler(e){
        var tid = e.attr('tid');
        var src =  e.attr('class').indexOf('check');

        if(src >= 0){
            e.removeClass('fa-check').addClass('fa-spinner fa-spin');
            doMainProductChange(e, tid, false);
        }else{
            e.removeClass('fa-times').addClass('fa-spinner fa-spin');
            doMainProductChange(e, tid, true);
        }
    }

	function visibleToggler(e){
		var tid = e.attr('tid');
		var src =  e.attr('class').indexOf('check');

	 	if(src >= 0){
			e.removeClass('fa-check').addClass('fa-spinner fa-spin');
			doVisibleChange(e, tid, false);
		}else{
			e.removeClass('fa-times').addClass('fa-spinner fa-spin');
			doVisibleChange(e, tid, true);
		}
	}

    function doMainProductChange(e, tid, show){
        var v = (show) ? '1' : '0';
        $.post("<?=AJAX_POST?>",{
            type : 'termekChangeActions',
            mode : 'changePrimaryProduct',
            id  : tid,
            val : v
        },function(d){
            if(!show){
                e.removeClass('fa-spinner fa-spin').addClass('fa-times');
            }else{
                e.removeClass('fa-spinner fa-spin').addClass('fa-check');
            }
        },"html");
    }

	function doVisibleChange(e, tid, show){
		var v = (show) ? '1' : '0';
		$.post("<?=AJAX_POST?>",{
			type : 'termekChangeActions',
			mode : 'showHideTermek',
			id 	: tid,
			val : v
		},function(d){
			if(!show){
				e.removeClass('fa-spinner fa-spin').addClass('fa-times');
			}else{
				e.removeClass('fa-spinner fa-spin').addClass('fa-check');
			}
		},"html");
	}
	function doAction(e){
		var mode 	= e.attr('mode');
		var tid 	= e.attr('tid');
		var val 	= e.val();
		console.log(mode+' : '+tid+' = '+val);
		$.post("<?=AJAX_POST?>",{
			type : 'termekChangeActions',
			mode : mode,
			id: tid,
			val : val
		},function(d){

		},"html");
	}
</script>
