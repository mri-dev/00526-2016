<div class="item item_<?=$itemhash?>">
	<div class="view">
		<div class="icon-set">
			<? if($ujdonsag == 1): ?>
				<img src="<?=IMG?>new_small_icon.png" title="Újdonság!" alt="Újdonság!">
			<? endif;?>
			<? if($akcios == 1): ?>
				<img src="<?=IMG?>discount_small_icon.png" title="Akciós termék!" alt="Akciós termék">
			<? endif;?>
		</div>
		<div class="img-bg">
			<div class="img img-thb"><span class="helper"></span><a title="<?=$product_nev?>" class="item_<?=$itemhash?>_link" href="<?=$link?>"><img class="item_<?=$itemhash?>_img" src="<?=$profil_kep?>" alt="<?=$product_nev?>"></a>
			</div>
		</div>
		<div class="datas">
			<div class="colors">
				<?
				if( !$sizefilter ):
				unset($hasonlo_termek_ids['colors'][$szin]); ?>
				<div class="colors-ca overflowed" style="">
					<ul class="colors-va" style="width: <?=(count($hasonlo_termek_ids['colors']) * 55 + 55)?>px;">
						<li hashkey="<?=$itemhash?>"><div class="img-thb"><span class="helper"></span><a href="<?=$link?>" title="<?=$szin?>"><img data-img="<?=$profil_kep?>" src="<?=Images::getThumbImg(75, $profil_kep)?>" alt="<?=$product_nev?>"></a></div></li>
						<? foreach ( $hasonlo_termek_ids['colors'] as $szin => $color ) { ?><li hashkey="<?=$itemhash?>">
							<div class="img-thb"><span class="helper"></span><a href="<?=$color['link']?>" title="<?=$szin?> <? if(rtrim($color['size_stack'],", ") != ''): ?>(<?=rtrim($color['size_stack'],", ")?>)<? endif; ?>"><img data-img="<?=$color['img']?>" src="<?=Images::getThumbImg(75, $color['img'])?>" alt=""></a></div></li><? } ?>
					</ul>
				</div>
				<div class="clr"></div>
				<?=count($hasonlo_termek_ids['colors'])+1?> szín
				<? else: ?>
					<span style="color:#FF8202;">Méret: <em><?=$meret?></em></span>
				<? endif; ?>
			</div>
			<div class="info">
				<div class="name"><a class="item_<?=$itemhash?>_link" href="<?=$link?>"><?=$product_nev?></a></div>
				<div class="kat"><?=ucfirst($csoport_kategoria)?></div>
				<div class="price">
					<? if( $akcios == '1' && $akcios_fogy_ar > 0): ?>
					<span class="old" title="Eredeti ár"><strike><?=\PortalManager\Formater::cashFormat($ar)?> Ft</strike></span> <span title="Akciós ár" class="new"><?=\PortalManager\Formater::cashFormat($akcios_fogy_ar)?> Ft</span>
					<? else: ?>
					<?=\PortalManager\Formater::cashFormat($ar)?> Ft <?=(defined("B2BLOGGED"))?'<span class="pafa">+ ÁFA</span>':''?>
					<? endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
