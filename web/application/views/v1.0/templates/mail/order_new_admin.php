<? require "head.php"; ?>
<div>Név: <strong><?=$nev?></strong></div>
<div>E-mail: <strong><?=$email?></strong> (<?=(($uid == '')? 'Nem regisztrált':'Regisztrált')?>)</div>
<div>Rendelés azonosító: <strong><?=$orderData['azonosito']?></strong></div>

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
<br>
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
	<tr>
		<td align="left">Megrendelés ID</td>
		<td align="left"><strong><?=$orderID?></strong></td>
	</tr>
</tbody>
</table>
<? require "footer.php"; ?>
