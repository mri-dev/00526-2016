<? require "head.php"; ?>
<h2>Tisztelt <?=$nev?>!</h2>
<h3>Köszönjük, hogy a(z) <?=$settings['page_title']?> webáruházat választotta!</h3>
<div>A rendelés azonosítója: <strong><?=$orderData[azonosito]?></strong></div>
<?php if ($b2b == 1): ?>
	<div style="color: red; margin: 10px 0; font-size: 13px;">
		Figyelem! Kérjük, hogy kártyás és banki átutalás fizetés esetén a vételár összegét azután teljesítse pénzforgalmi számlaszámunkra, miután a szállítási költség ismert. A fizetést a "Fizetésre vár" állapotváltozásra állítás után tudja rendezni. Ekkor már a tényleges fizetendő végösszeg szerepel a megrendelésénél.
	</div>
<?php endif; ?>
<div><h3>Megrendelt termékek</h3></div>
<table width="100%" border="1" style="border-collapse:collapse; border:2px solid #dddddd; background:#ffffff;" cellpadding="10" cellspacing="0">
<thead>
	<tr>
		<th align="center">Me.</th>
		<th align="center">Termék</th>
		<th align="center">Méret</th>
		<th align="center">Szín</th>
		<th align="center"><?=($b2b == 1)?'Nettó':'Bruttó'?> e. ár</th>
		<th align="center"><?=($b2b == 1)?'Nettó':'Bruttó'?> ár</th>
		<th align="center">Állapot</th>
	</tr>
</thead>
<tbody style="color:#888;">
	<?
	$szmnev = array(
        'nev' => 'Név',
        'phone' => 'Telefon',
        'irsz' => 'Irányítószám',
        'city' => 'Település',
        'kerulet' => 'Kerület',
        'uhsz' => 'Közterület neve', // közterület neve
        'kozterulet_jellege' => 'Közterület jellege',
        'hazszam' => 'Házszám',
        'epulet' => 'Épület',
        'lepcsohaz' => 'Lépcsőház',
        'szint' => 'Szint',
        'ajto' => 'Ajtó'
    );

	foreach($cart as $d){
	$total += ($d[ar]*$d[me]);
	?>
	<tr>
		<td align="center"><?=$d[me]?>x</td>
		<td><a href="<?=$d[url]?>"><?=$d[nev]?></a></td>
		<td align="center"><?=$d[meret]?></td>
		<td align="center"><?=$d[szin]?></td>
		<td align="center"><?=round($d[ar])?> Ft <?=($b2b == 1)?' + ÁFA':''?></td>
		<td align="center"><?=round($d[ar]*$d[me])?> Ft <?=($b2b == 1)?' + ÁFA':''?></td>
		<td align="center"><strong style="color:#CC0000;">Feldolgozás alatt</strong></td>
	</tr>
	<? } ?>
	<tr>
		<td colspan="6" align="right">Összesen:</td>
		<td align="center"><?=$total?> Ft</td>
	</tr>
	<tr>
		<td colspan="6" align="right">Szállítási költség:</td>
		<td align="center"><?=$szallitasi_koltseg?> Ft</td>
	</tr>
	<tr>
		<td colspan="6" align="right">Kedvezmény:</td>
		<td align="center"><?=( ( !$kedvezmeny && $kedvezmeny == '') ? '0' : round($kedvezmeny) )?> %</td>
	</tr>
	<?
	$etotal = $total;
	if($szallitasi_koltseg > 0) $total += $szallitasi_koltseg;
	?>
	<tr>
		<td colspan="6" align="right"><strong>Végösszeg:</strong></td>
		<td align="center">
			<strong><?=round($total)?> Ft</strong> <?=($b2b == 1)?' + ÁFA':''?>
			<?php if ($b2b == 1): $etotal = $etotal*AFA; ?>
				<div>bruttó <? echo \Helper::cashFormat($etotal+$szallitasi_koltseg); ?> Ft</div>
			<?php endif; ?>
		</td>
	</tr>
</tbody>
</table>

<div><h3>Számlázási adatok</h3></div>
<table width="100%" border="1" style="border-collapse:collapse; border:2px solid #dddddd; background:#ffffff;" cellpadding="10" cellspacing="0">
<tbody>
	<? foreach($szmnev as $szkey => $szval): if($szkey == 'phone') continue; ?>
	<tr>
		<td width="150" align="left"><?=$szval?></td>
		<td align="left"><strong><?=($szamlazasi_keys[$szkey])? $szamlazasi_keys[$szkey]:'&mdash;'?></strong></td>
	</tr>
	<? endforeach; ?>
</tbody>
</table>
<div><h3>Szállítási adatok</h3></div>
<table width="100%" border="1" style="border-collapse:collapse; border:2px solid #dddddd; background:#ffffff;" cellpadding="10" cellspacing="0">
<tbody>
	<? foreach($szmnev as $szkey => $szval): ?>
	<tr>
		<td width="150" align="left"><?=$szval?></td>
		<td align="left"><strong><?=($szallitasi_keys[$szkey])? $szallitasi_keys[$szkey]:'&mdash;'?></strong></td>
	</tr>
	<? endforeach; ?>
</tbody>
</table>
<div><h3>Egyéb adatok</h3></div>
<table width="100%" border="1" style="border-collapse:collapse; border:2px solid #dddddd; background:#ffffff;" cellpadding="10" cellspacing="0">
<tbody>
	<tr>
		<td width="150" align="left">Megjegyzés</td>
		<td align="left"><strong><?=$megjegyzes?></strong></td>
	</tr>
	<tr>
		<td align="left">Átvétel módja</td>
		<td align="left"><strong><?=$atvetel?></strong></td>
	</tr>
	<tr>
		<td align="left">Fizetés módja</td>
		<td align="left"><strong><?=$fizetes?></strong>
		<? if( $is_pickpackpont ){ ?>
			(<?=$ppp_uzlet_str?>)
		<? } ?>
		</td>
	</tr>
	<tr>
		<td align="left">Megrendelve</td>
		<td align="left"><strong><?=date('Y-m-d H:i:s')?></strong></td>
	</tr>
</tbody>
</table>
<? if( $is_eloreutalas ){ ?>
	<div><h3>Átutaláshoz szükséges adatok</h3></div>
	<table width="100%" border="1" style="border-collapse:collapse; border:2px solid #dddddd; background:#ffffff;" cellpadding="10" cellspacing="0">
	<tbody>
		<tr>
			<td width="150" align="left">Név</td>
			<td align="left"><strong><?=$settings['banktransfer_author']?></strong></td>
		</tr>
		<tr>
			<td align="left">Számlaszám:</td>
			<td align="left"><strong><?=$settings['banktransfer_number']?></strong></td>
		</tr>
		<tr>
			<td align="left">Bank:</td>
			<td align="left"><strong><?=$settings['banktransfer_bank']?></strong></td>
		</tr>
		<tr>
			<td align="left">Közleménybe:<br><em style="font-size:12px;">(megrendelés azonosító)</em></td>
			<td align="left"><strong><strong><?=$orderData[azonosito]?></strong></td>
		</tr>
	</tbody>
	</table>
<? } ?>
<br>
<div>Megrendelését nyomon követheti weboldalunkon. Regisztrált tagként, bejelentkezés után a megrendelések menüpont alatt keresse. <br /><br />
<strong>Ha Ön nem regisztrált felhasználó a(z) <?=$settings['page_title']?> oldalon, ezen a linken megtekintheti aktuális megrendelését:</strong><br />
<a href="<?=$settings['domain']?>/order/<?=$accessKey?>"><?=$settings['domain']?>/order/<?=$accessKey?></a>
</div>
<? require "footer.php"; ?>
