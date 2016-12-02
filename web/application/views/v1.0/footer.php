<div class="clr"></div>
</div>
</div><!-- End of Content View -->
<div class="footer <?=(defined("B2BLOGGED"))?'b2b-view':''?>">
	<div class="page-width">
		<?php if (!defined("B2BLOGGED")): ?>
		<div class="nav nav-footer">
			<ul>
				<? foreach ( $this->menu_footer->tree as $menu ): ?>
				<li>
					<? if($menu['link']): ?><a href="<?=($menu['link']?:'')?>"><? endif; ?>
						<span class="item <?=$menu['css_class']?>" style="<?=$menu['css_styles']?>">
							<? if($menu['kep']): ?><img src="<?=\PortalManager\Formater::sourceImg($menu['kep'])?>"><? endif; ?>
							<?=$menu['nev']?></span>
					<? if($menu['link']): ?></a><? endif; ?>
					<? if($menu['child']): ?>
						<? foreach ( $menu['child'] as $child ) { ?>
							<div class="item <?=$child['css_class']?>">
								<?
								// Inclue
								if(strpos( $child['nev'], '=' ) === 0 ): ?>
									<? echo $this->templates->get( str_replace('=','',$child['nev']), array( 'view' => $this ) ); ?>
								<? else: ?>
								<? if($child['link']): ?><a href="<?=$child['link']?>"><? endif; ?>
								<? if($child['kep']): ?><img src="<?=\PortalManager\Formater::sourceImg($child['kep'])?>"><? endif; ?>
								<span style="<?=$child['css_styles']?>"><?=$child['nev']?></span>
								<? if($child['link']): ?></a><? endif; ?>
								<? endif; ?>
							</div>
						<? } ?>
					<? endif; ?>
				</li>
				<? endforeach; ?>
			</ul>
		</div>
		<?
		/**
		 * Sponzorok betöltése
		 */
		$this->render( 'logok/szponzor', true);
		?>
		<?php endif; ?>
		<div class="clr"></div>
		<div class="copyrights">
			<div class="text">&copy; 2009 - <?=date('Y')?> <?=$this->settings['page_title']?> &mdash; <?=$this->settings['page_description']?> <span class="aszf_footer" style="margin:0 8px;">
				<?php if (defined("B2BLOGGED")): ?>
					<a href="/p/b2b-aszf">B2B Általános Szerződési Feltételek</a>
				<?php else: ?>
					<a href="/p/b2b-aszf">Általános Szerződési Feltételek</a>
				<?php endif; ?>
			</span></div>
			<div class="payu-credit"><a href="http://simplepartner.hu/PaymentService/Fizetesi_tajekoztato.pdf" target="_blank">
<img style="height:25px !important;" src="<?=IMG?>simple_logo_long.png" title="Simple - Online bankkártyás fizetés" alt="Simple vásárlói tájékoztató"> </a></div>
		</div>
		<div class="clr"></div>
	</div>
</div>
<? if(defined('GA_REMARKETING') && GA_REMARKETING): ?>
	<? $this->render('templates/ga_remarketing'); ?>
<? endif; ?>
</body>
</html>
