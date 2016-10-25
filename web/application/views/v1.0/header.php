<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/html4"
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="http://www.facebook.com/2008/fbml" lang="hu-HU">
<head>
    <title><?=$this->title?></title>
    <?=$this->addMeta('robots','index,folow')?>
    <?=$this->SEOSERVICE?>
    <? $this->render('meta'); ?>
    <? if($this->settings['google_analitics'] != ""): ?>
    <script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
	  ga('create', '<?=$this->settings['google_analitics']?>', 'auto');
	  ga('send', 'pageview');
	</script>
	<? endif; ?>
</head>
<body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/hu_HU/sdk.js#xfbml=1&version=v2.3";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div class="top">
	<div class="social-links">
		<? if($this->settings['social_facebook_link']): ?><a target="_blank" title="Facebook oldalunk" href="<?=$this->settings['social_facebook_link']?>"><i class="fa fa-facebook-square"></i></a><? endif; ?>
		<? if($this->settings['social_youtube_link']): ?><a target="_blank" title="Youtube csatorna" href="<?=$this->settings['social_youtube_link']?>"><i class="fa fa-youtube-square"></i></a><? endif; ?>
		<? if($this->settings['social_googleplus_link']): ?><a target="_blank" title="Google+" href="<?=$this->settings['social_googleplus_link']?>"><i class="fa fa-google-plus-square"></i></a><? endif; ?>
		<? if($this->settings['social_twitter_link']): ?><a target="_blank" title="Twitter" href="<?=$this->settings['social_twitter_link']?>"><i class="fa fa-twitter"></i></a><? endif; ?>
	</div>
	<div class="fl-right">
		<div><a href="/feliratkozas">Feliratkozás</a></div>
		<div class="cart-float" id="mb-cart">
			<div mb-event="true" data-mb='{ "event": "toggleOnClick", "target" : "#mb-cart" }'><i class="fa fa-shopping-cart"></i> <span id="cart-item-num-v">0</span></div>
			<div class="floating mobile-max-width">
				<div id="cartContent" class="overflowed">
					<div class="noItem"><div class="inf">A kosár üres</div></div>
				</div>
				<div class="totals">
					<table width="100%">
						<tbody>
							<tr>
								<td class="left"><strong>Összesen: </strong></td>
								<td class="right"><span id="cart-item-prices"></span> Ft</td>
							</tr>
							<tr>
								<td colspan="2" class="right">
									<a href="/kosar">Megrendelés</a>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div><a href="/p/kapcsolatfelvetel">Segítség</a></div>
    <div class="b2b-label"><a href="/b2b">B2B</a></div>
	</div>
	<div class="clr"></div>
</div>

<div class="mobile-smartphone-device">
	<div class="logo">
		<a href="<?=DOMAIN?>" title="<?=$this->settings['page_title']?> &mdash; <?=$this->settings['page_description']?>"><img src="<?=IMG?>logo_200x_white.png" alt=""></a>
	</div>
</div>
<div class="menu">
	<div class="row">
		<div class="mobile-device nav-view static-view">
			<div class="logo">
				<a href="<?=DOMAIN?>" title="<?=$this->settings['page_title']?> &mdash; <?=$this->settings['page_description']?>"><img src="<?=IMG?>logo_200x_white.png" alt=""></a>
			</div>
			<div class="nav-mobile">
				<div class="toggler" mb-event="true" data-mb='{ "event": "toggleOnClick", "target" : "#mb-nav-list" }'>
					<i class="fa fa-bars"></i>
				</div>
				<div class="nav overflowed of-blue mobile-max-width" id="mb-nav-list">
					<ul>
						<?
							foreach ( $this->menu_header->tree as $menu ):
							$nb_hash = hash('crc32', microtime());
						?>
						<li mb-event="true" data-mb='{"event":"toggleOnClick", "target":"#mb-navd1-<?=$nb_hash?>"}'>
							<? if( !$menu['child']): ?>
							<a href="<?=($menu['link']?:'')?>">
							<? endif; ?>
								<? if($menu['kep']): ?><img src="<?=\PortalManager\Formater::sourceImg($child['kep'])?>"><? endif; ?>
								<?=$menu['nev']?> <? if($menu['child']): ?><i class="fa fa-angle-down"></i><? endif; ?>
							<? if(!$menu['child']): ?>
							</a>
							<? endif; ?>
							<? if($menu['child']): ?>
							<div class="sub nav-sub-view" id="mb-navd1-<?=$nb_hash?>">
								<div class="page-width">
									<div class="inside">
										<? foreach($menu['child'] as $child): ?>
										<div class="sub-col <?=($child['lista'] ? 'kat-childlist' : '')?>">
											<div class="item item-header <?=$child['css_class']?>" >
											<? if($child['link']): ?><a href="<?=$child['link']?>"><? endif; ?>
											<? if($child['kep']): ?><img src="<?=\PortalManager\Formater::sourceImg($child['kep'])?>"><? endif; ?>
											<span style="<?=$child['css_styles']?>"><?=$child['nev']?></span>
											<? if($child['link']): ?></a><? endif; ?>
											</div>
											<? if($child['lista']): ?>
											<? foreach ($child['lista'] as $elem ) { ?>
												<div class="item <?=$elem['css_class']?>">
													<? if($elem['link']): ?><a href="<?=$elem['link']?>"><? endif; ?>
													<span style="<?=$elem['css_styles']?>"><?=$elem['neve']?></span>
													<? if($elem['link']): ?></a><? endif; ?>
												</div>
											<? }?>
											<? endif; ?>
											<? if($child['child']): ?>
											<? foreach ($child['child'] as $elem ) { ?>
												<div class="item <?=$elem['css_class']?>">
													<? if($elem['link']): ?><a href="<?=$elem['link']?>"><? endif; ?>


													<span style="<?=$elem['css_styles']?>"><?=$elem['nev']?></span>
													<? if($elem['link']): ?></a><? endif; ?>
												</div>
											<? }?>
											<? endif; ?>
										</div>
										<? endforeach; ?>
									</div>
								</div>
							</div>
							<? endif; ?>
						</li>
						<? endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
		<div class="nav-view view-desktop static-view">
			<div class="logo">
				<a href="<?=DOMAIN?>" title="<?=$this->settings['page_title']?> &mdash; <?=$this->settings['page_description']?>"><img src="<?=IMG?>logo_200x_white.png" alt=""></a>
			</div>
			<div class="nav">
				<ul>
					<? foreach ( $this->menu_header->tree as $menu ): ?>
					<li>
						<a href="<?=($menu['link']?:'')?>">
							<? if($menu['kep']): ?><img src="<?=\PortalManager\Formater::sourceImg($child['kep'])?>"><? endif; ?>
							<?=$menu['nev']?> <? if($menu['child']): ?><i class="fa fa-angle-down"></i><? endif; ?></a>
						<? if($menu['child']): ?>
						<div class="sub nav-sub-view">
							<div class="page-width">
								<div class="inside">
									<? foreach($menu['child'] as $child): ?>
									<?
										$has_stacklink = false;
										//print_r($child['child']);
										if( $child['child'] && count($child['child']) > 0) {
											foreach($child['child'] as $e):
												if ( strpos($e['css_class'], 'nav-link-stackview') !== false ) {
													$has_stacklink = true;
													break;
												}
											endforeach;
										}
									?>
									<div class="sub-col <?=($has_stacklink) ? 'has-stacklink' : ''?> <?=($child['lista'] ? 'kat-childlist' : '')?>">
										<div class="item item-header <?=$child['css_class']?>" >
										<? if($child['link']): ?><a href="<?=$child['link']?>"><? endif; ?>
										<? if($child['kep']): ?><img src="<?=\PortalManager\Formater::sourceImg($child['kep'])?>"><? endif; ?>
										<span style="<?=$child['css_styles']?>"><?=$child['nev']?></span>
										<? if($child['link']): ?></a><? endif; ?>
										</div>
										<? if($child['lista']): ?>
										<? foreach ($child['lista'] as $elem ) { ?>
											<div class="item <?=$elem['css_class']?>">
												<? if($elem['link']): ?><a href="<?=$elem['link']?>"><? endif; ?>
												<span style="<?=$elem['css_styles']?>"><?=$elem['neve']?></span>
												<? if($elem['link']): ?></a><? endif; ?>
											</div>
										<? }?>
										<? endif; ?>
										<? if($child['child']): ?>
										<? foreach ($child['child'] as $elem ) { ?>
											<div class="item <?=$elem['css_class']?>">
												<? if($elem['link']): ?><a href="<?=$elem['link']?>"><? endif; ?>
												<? if($elem['kep']): ?><img src="<?=\PortalManager\Formater::sourceImg($elem['kep'])?>"><? endif; ?>
												<span style="<?=$elem['css_styles']?>"><?=$elem['nev']?></span>
												<? if($elem['link']): ?></a><? endif; ?>
											</div>
										<? }?>
										<? endif; ?>
									</div>
									<? endforeach; ?>
								</div>
							</div>
						</div>
						<? endif; ?>
					</li>
					<? endforeach; ?>
				</ul>
			</div>
		</div>
		<div class="mobile-device right search-view">
			<div class="searching right">
				<div class="search-v">
					<div class="toggler" mb-event="true" data-mb='{ "event": "toggleOnClick", "target" : "#mb-search-input" }'>
						<i class="fa fa-search"></i>
					</div>
					<div id="mb-search-input" class="search-form mobile-max-width mb-tgl-close">
						<form action="" method="post" onSubmit="searchItem($(this)); return false;">
		                    <input type="text" class="form-control" placeholder="Keresés" value="<?=($this->gets[0] == 'kereses')?$this->gets[1]:''?>">
		                </form>
					</div>
				</div>
			</div>
		</div>
		<div class="right login-view view-deskop">
			<div class="login-float menu-link mb-tgl-close" id="mb-account" >
				<div mb-event="true" data-mb='{ "event": "toggleOnClick", "target" : "#mb-account" }'>
				<? if ( $this->user ) { ?>
					<?=$this->user['data']['nev']?> <i class="fa fa-angle-down"></i>
				<? } else { ?>
					Belépés  <i class="fa fa-angle-down"></i>
				<? }?>
				</div>
				<div class="floating mobile-max-width">
					<?
					if ( $this->user ) {
						echo $this->templates->get( 'user' );
					} else {
						echo $this->templates->get( 'user_login' );
					}
					?>
				</div>
			</div>
		</div>

		<div class="center search-view view-desktop">
			<div class="searching">
				<form action="" method="post" onSubmit="searchItem($(this)); return false;">
                    <input type="text" class="form-control" placeholder="Keresés" value="<?=($this->gets[0] == 'kereses')?$this->gets[1]:''?>">
                </form>
			</div>
		</div>
		<div class="clr"></div>
	</div>
</div>
<? if( count($this->highlight_text) > 0 ): ?>
<div class="highlight-view">
	<div class="items">
		<div class="hl-cont">
			<? if( count($this->highlight_text['data']) > 1 ): ?>
			<a href="javascript:void(0);" title="Előző" class="prev handler" key="prev"><i class="fa fa-angle-left"></i> |</a>
			<a href="javascript:void(0);" title="Következő" class="next handler" key="next">| <i class="fa fa-angle-right"></i></a>
			<? endif; ?>
			<ul>
				<? $step = 0; foreach( $this->highlight_text['data'] as $text ): $step++; ?>
				<li class="<?=($step == 1)?'active':''?>" index="<?=$step?>"><?=$text['tartalom']?></li>
				<? endforeach; ?>
			</ul>
		</div>
	</div>
</div>
<? endif; ?>
<!-- Content View -->
<div class="content-view  <?=($this->gets[0] == 'home')?'landing-page':''?>">
	<div class="view-content">
