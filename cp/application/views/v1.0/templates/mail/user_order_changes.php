<? require "head.php"; 
$szamlazasi_keys 	= json_decode($szamlazasi_keys, true);
$szallitasi_keys 	= json_decode($szallitasi_keys, true);
$total 				= 0;
?>

<h2>Tisztelt <?=$nev?>!</h2>
<div><strong><u><?=$orderData[azonosito]?></u></strong> azonosítójú rendelése <strong><u><?=date('Y-m-d H:i:s')?></u></strong> időponttal megváltozott.</div>
<div><h3>Változások:</h3></div>
<? foreach($changedData as $chkey => $chv){
	
	$keyname = $strKey[$chkey];

	if($chkey == 'termekAllapot') {
		$after = ' ('.$chv.' db termék)';
	}
	if($chkey == 'uj_termek') {
		$after = ' ('.$chv.' db hozzáadott termék)';
	}
	echo '<div>- ' . $keyname . $after . '</div>';	
} 
?>
<div></div>
<div><h3>Megrendelés állapota:</h3></div>
<div><strong style="color:<?=$orderAllapotok[$allapot][szin]?>;"><?=$orderAllapotok[$allapot][nev]?></strong></div>

<div><h3>Termékek</h3></div> 
<table width="100%" border="1" style="border-collapse:collapse; border:2px solid #dddddd; background:#ffffff;" cellpadding="10" cellspacing="0">
<thead>
	<tr>
		<th align="center">Me.</th>
		<th align="center">Termék</th>
		<th align="center">Szín</th>
		<th align="center">Méret</th>
		<th align="center">Bruttó e. ár</th>
		<th align="center">Bruttó ár</th>
		<th align="center">Állapot</th>
	</tr>
</thead>
<tbody style="color:#888;">
<? foreach($cart as $d){
	$total += ($d[ar]*$d[me]);
?>
	<tr>
		<td align="center"><?=$d[me]?>x</td>
		<td><a href="<?=$d[url]?>"><?=$d[nev]?></a></td>
		<td align="center"><?=$d[szin]?></td>
		<td align="center"><?=$d[meret]?></td>
		<td align="center"><?=round($d[ar])?> Ft</td>
		<td align="center"><?=round($d[ar]*$d[me])?> Ft</td>
		<td align="center"><strong style="color:<?=$termekAllapotok[$d[allapotID]][szin]?>;"><?=$termekAllapotok[$d[allapotID]][nev]?></strong></td>
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
		<td align="center"><?=(($kedvezmeny > 0) ? $kedvezmeny : 0 )?>%</td>
	</tr>
	<? 
		if($szallitasi_koltseg > 0) $total += $szallitasi_koltseg;
	?>
	<tr>
		<td colspan="6" align="right"><strong>Végösszeg:</strong></td>
		<td align="center"><strong><?=round($total)?> Ft</strong></td>
	</tr>
</tbody>
</table>
<div><h3>Számlázási adatok</h3></div> 
<table width="100%" border="1" style="border-collapse:collapse; border:2px solid #dddddd; background:#ffffff;" cellpadding="10" cellspacing="0">
<tbody>
	<tr>
		<td width="150" align="left">Név</td>
		<td align="left"><strong><?=$szamlazasi_keys[nev]?></strong></td>
	</tr>
	<tr>
		<td align="left">Utca, házszám</td>
		<td align="left"><strong><?=$szamlazasi_keys[uhsz]?></strong></td>
	</tr>
	<tr>
		<td align="left">Város</td>
		<td align="left"><strong><?=$szamlazasi_keys[city]?></strong></td>
	</tr>
	<tr>
		<td align="left">Megye</td>
		<td align="left"><strong><?=$szamlazasi_keys[state]?></strong></td>
	</tr>
	<tr>
		<td align="left">Irányítószám</td>
		<td align="left"><strong><?=$szamlazasi_keys[irsz]?></strong></td>
	</tr>
</tbody>
</table>
<div><h3>Szállítási adatok</h3></div> 
<table width="100%" border="1" style="border-collapse:collapse; border:2px solid #dddddd; background:#ffffff;" cellpadding="10" cellspacing="0">
<tbody>
	<tr>
		<td width="150" align="left">Név</td>
		<td align="left"><strong><?=$szallitasi_keys[nev]?></strong></td>
	</tr>
	<tr>
		<td align="left">Utca, házszám</td>
		<td align="left"><strong><?=$szallitasi_keys[uhsz]?></strong></td>
	</tr>
	<tr>
		<td align="left">Város</td>
		<td align="left"><strong><?=$szallitasi_keys[city]?></strong></td>
	</tr>
	<tr>
		<td align="left">Megye</td>
		<td align="left"><strong><?=$szallitasi_keys[state]?></strong></td>
	</tr>
	<tr>
		<td align="left">Irányítószám</td>
		<td align="left"><strong><?=$szallitasi_keys[irsz]?></strong></td>
	</tr>
	<tr>
		<td align="left">Telefonszám</td>
		<td align="left"><strong><?=$szallitasi_keys[phone]?></strong></td>
	</tr>
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
			<td align="left">
			<strong><?=$atvetel?></strong>
			<? if( $is_pickpackpont ){ ?>
				(<?=$ppp_uzlet_str?>)
			<? } ?></td>
		</tr>
		<tr>
			<td align="left">Fizetés módja</td>
			<td align="left"><strong><?=$fizetes?></strong></td>
		</tr>
		<tr>
			<td align="left">Megrendelve</td>
			<td align="left"><strong><?=$orderData[idopont]?></strong></td>
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
			<td align="left"><strong><?=$orderData[azonosito]?></strong></td>
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