<div class="product-view page-width">
    <div class="main-view">
        <div class="images">
            <div class="all">
                <ul>
                    <?  foreach ( $this->product['images'] as $img ) { ?>
                    <li class="img-auto-cuberatio">
                        <div class="img-thb">
                            <span class="helper"></span>
                            <img i="<?=\PortalManager\Formater::productImage($img)?>" src="<?=\PortalManager\Formater::productImage($img, 75)?>" alt="<?=$this->product['nev']?>">
                        </div>
                    </li>
                    <? } ?>
                </ul>
                <div class="clr"></div>
            </div>
            <div class="main-img">
                <div class="img-thb">
                    <span class="helper"></span>
                    <a href="<?=$this->product['profil_kep']?>" class="zoom"><img di="<?=$this->product['profil_kep']?>" src="<?=$this->product['profil_kep']?>" alt="<?=$this->product['nev']?>"></a>
                </div>
                <div class="share">
                    <div class="fb-like" data-href="http://<?=$this->settings['page_url'].'/'.substr($_SERVER[REQUEST_URI],1)?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>
                </div>
            </div>
        </div>

        <div class="data-view">
            <div class="cimkek">
            <? if($this->product['ujdonsag'] == '1'): ?>
                <img src="<?=IMG?>new_icon.png" title="Újdonság!" alt="Újdonság">
            <? endif; ?>
            <? if($this->product['akcios'] == '1'): ?>
                <img src="<?=IMG?>discount_icon.png" title="Akciós termék!" alt="Akciós">
            <? endif; ?>
            </div>
            <h1><?=$this->product['nev']?></h1>
            <div class="cat"><?=$this->product['csoport_kategoria']?></div>
            <div class="price">
              <? if( $this->product['akcios'] == '1' && $this->product['akcios_fogy_ar'] > 0): ?>
                <span class="old" title="Eredeti ár"><strike><?=\PortalManager\Formater::cashFormat($this->product['ar'])?> Ft</strike></span> <span title="Akciós ár" class="new"><?=\PortalManager\Formater::cashFormat($this->product['akcios_fogy_ar'])?> Ft</span> <? else: ?>
                <?=\PortalManager\Formater::cashFormat($this->product['ar'])?> Ft <?=(defined("B2BLOGGED"))?'<span class="pafa">+ ÁFA</span>':''?>
                <?php if (defined("B2BLOGGED")): ?>
                  <span title="Termék bruttó ára" class="b2b-br-price">bruttó <?=\PortalManager\Formater::cashFormat($this->product['ar']*AFA)?> Ft</span>
                <?php endif; ?>
              <? endif; ?>
            </div>
            <?
                if( count($this->product['hasonlo_termek_ids']['colors']) > 0 ):
                $colorset = $this->product['hasonlo_termek_ids']['colors'];
                unset($colorset[$this->product['szin']]);
            ?>
            <div class="variant">
                <? foreach ($colorset as $szin => $adat ) { ?>
                <div class="item">
                    <div class="img-thb">
                        <span class="helper"></span>
                        <a title="<?=$szin?><? if(rtrim($adat['size_stack'],", ") != ''): ?>(<?=rtrim($adat['size_stack'],", ")?>)<? endif; ?>" href="<?=$adat['link']?>"><img src="<?=$adat['img']?>" alt="<?=$adat['img']?>"></a>
                    </div>
                </div>
                <? } ?>
                <div class="clr"></div>
            </div>
        <? endif; ?>
            <div class="short-description">
                <? if( !$this->product['rovid_leiras'] ): ?>
                A terméknek nincs leírása.
                <? endif; ?>
                <?=str_replace( array( trim($this->product['csoport_kategoria']) . '<br>', trim($this->product['csoport_kategoria']) ) , '', nl2br($this->product['rovid_leiras'], false))?>
            </div>
			<? if($this->product['link_lista']): ?>
            <div class="links">
               <ul>
               <? foreach ($this->product['link_lista'] as $link ) { ?>
                    <li><a target="_blank" href="<?=$link['link']?>"><?=$link['title']?></a></li>
               <? } ?>
                </ul>
                <div class="clr"></div>
            </div>
			<? endif; ?>
            <div class="cart">
                <? if( $this->product['szin'] != '' ) : ?>
                <div class="color">
                    Szín: <br>
                    <strong><?=$this->product['szin']?></strong>
                </div>
                <? endif; ?>
                <div class="status">
                    Elérhetőség: <br>
                    <strong><?=$this->product['keszlet_info']?></strong>
                </div>
                <div class="clr"></div>
                <?php if (defined("B2BLOGGED")): ?>
                <div class="status">
                  Készleten: <br>
                  <strong style="color: orange;"><?=$this->product['raktar_keszlet']?> db</strong>
                </div>
                <div class="clr"></div>
                <?php endif; ?>
                <div id="cart-msg"></div>
                <? if( $this->product['meret'] != '' ): ?>
                <div class="size-selector cart-btn dropdown-list-container">
                    <div class="dropdown-list-title"><span id="">Méret: <strong><?=$this->product['meret']?></strong></span> <? if( count( $this->product['hasonlo_termek_ids']['colors'][$this->product['szin']]['size_set'] ) > 0): ?> <i class="fa fa-angle-down"></i><? endif; ?></div>
                    <? if( count( $this->product['hasonlo_termek_ids']['colors'][$this->product['szin']]['size_set'] ) > 0): ?>
                    <div class="number-select dropdown-list-selecting overflowed">
                        <? foreach (  $this->product['hasonlo_termek_ids']['colors'][$this->product['szin']]['size_set'] as $size ) { ?>
                        <div link="<?=$size['link']?>"><?=$size['size']?></div>
                        <? } ?>
                    </div>
                    <? endif; ?>
                </div>
                <? endif; ?>

                <?php if (defined("B2BLOGGED")): ?>
                <div class="item-count cart-btn">
                  <div class="input-group">
                    <input type="number" id="add_cart_num" cart-count="<?=$this->product['ID']?>" class="form-control" min="1" max="<?=$this->product['raktar_keszlet']?>" step="1" value="1">
                    <span class="input-group-addon">DB</span>
                  </div>
                </div>
                <?php else: ?>
                <input type="text" id="add_cart_num" style="display:none;" value="0" cart-count="<?=$this->product['ID']?>" />
                <div class="item-count cart-btn dropdown-list-container">
                    <div class="dropdown-list-title"><span id="item-count-num">Mennyiség</span> <i class="fa fa-angle-down"></i></div>
                    <div class="number-select dropdown-list-selecting overflowed">
                        <?
                        $maxi = 10;
                        if( $this->product[raktar_keszlet] < $maxi ) $maxi = (int)$this->product[raktar_keszlet];

                        for ( $n = 1; $n <= $maxi; $n++ ) { if($n > 10) break; ?>
                        <div num="<?=$n?>"><?=$n?></div>
                        <? } ?>
                    </div>
                </div>
                <?php endif; ?>
                <? if( $this->product['keszletID'] != $this->settings['flagkey_itemstatus_outofstock'] ): ?>
                <button cart-data="<?=$this->product['ID']?>" cart-remsg="cart-msg" title="Kosárba" class="tocart cart-btn">Kosárba</i></button>
                <? endif; ?>
                <div class="clr"></div>
            </div>
        </div>


        <div class="clr"></div>
    </div>
    <div class="description">
         <?=$this->product['leiras']?>
    </div>

    <? if( $this->related && $this->related->hasItems() ): ?>
    <div class="related-products">
        <h3>További termékeink</h3>
        <div class="list">
            <div class="products">
                <div class="grid-container">
                <? foreach ( $this->related_list as $p ) {
                    $p['itemhash'] = hash( 'crc32', microtime() );
                    $p['sizefilter'] = ( count($this->related->getSelectedSizes()) > 0 ) ? true : false;
                    echo $this->template->get( 'product_list_item', $p );
                } ?>
                </div>
                <div class="clr"></div>
            </div>
        </div>
    </div>
    <? endif; ?>
</div>

<script type="text/javascript">
    $(function(){
        $('.number-select > div[num]').click( function (){
            $('#add_cart_num').val($(this).attr('num'));
            $('#item-count-num').text($(this).attr('num')+' db');
        });
        $('.size-selector > .number-select > div[link]').click( function (){
            document.location.href = $(this).attr('link');
        });

        $('.product-view .images .all img').hover(function(){
            changeProfilImg( $(this).attr('i') );
        });

        $('.product-view .images .all img').bind("mouseleave",function(){
            //changeProfilImg($('.product-view .main-view a.zoom img').attr('di'));
        });

        $('.products > .grid-container > .item .colors-va li')
        .bind( 'mouseover', function(){
            var hash    = $(this).attr('hashkey');
            var mlink   = $('.products > .grid-container > .item').find('.item_'+hash+'_link');
            var mimg    = $('.products > .grid-container > .item').find('.item_'+hash+'_img');

            var url = $(this).find('a').attr('href');
            var img = $(this).find('img').attr('data-img');

            mimg.attr( 'src', img );
            mlink.attr( 'href', url );
        });
    })

    function changeProfilImg(i){
        $('.product-view .main-img a.zoom img').attr('src',i);
        $('.product-view .main-img a.zoom').attr('href',i);
    }
</script>
