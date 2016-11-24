<? if( $this->category->getName() ): ?>
    <div class="category-listing page-width"> 

        <div class="side-menu side-left">
            <form action="/<?=$this->gets[0]?>/<?=$this->gets[1]?>/-/1<?=( $this->cget != '' ) ? '?'.$this->cget : ''?>" method="get">
            <ul>
                <li class="head"><?=$this->parent_menu->getParentData('neve')?> <span class="icon"><i class="fa fa-angle-down"></i></span></li>
                <? while( $this->parent_menu->walk() ): 
                    $sm = $this->parent_menu->the_cat(); 
                    if(($this->parent_menu->getParentData('deep')+1) != $sm['deep']) { continue; } 
                ?>
                <li class="<?=($sm['ID'] == $this->category->getId()) ? 'active':''?>"><a href="<?=$sm['link']?>"><?=$sm['neve']?></a></li>
                <? endwhile; ?>
                <? if( count($this->products->getAvaiableSizes()) > 0 ): ?>
                 <li class="head">Méret <span class="icon"><i class="fa fa-angle-down"></i></span></li>
                 <li style="padding:8px 0;">
                    <div class="sizes">
                     <? foreach ( $this->products->getAvaiableSizes() as $size ) { ?>
                       <div class="size"><input <?=($_GET['meret'] && in_array($size,$_GET['meret']))?'checked="checked"':''?> type="checkbox" id="size_<?=$size?>" name="meret[]" value="<?=$size?>"><label for="size_<?=$size?>"><?=$size?></label></div>
                     <? }?>
                    </div>
                    <div class="clr"></div>
                 </li>
                <? endif;?>
                <li class="head">Rendezés <span class="icon"><i class="fa fa-angle-down"></i></span></li>
                <li class="full-width">
                    <select name="order" class="form-control">
                        <option value="nev_asc" <?=($_GET['order'] == 'nev_asc')?'selected="selected"':''?>>Név: A-Z</option>
                        <option value="nev_desc" <?=($_GET['order'] == 'nev_desc')?'selected="selected"':''?>>Név: Z-A</option>
                        <option value="ar_asc" <?=($_GET['order'] == 'ar_asc')?'selected="selected"':''?>>Ár: növekvő</option>
                        <option value="ar_desc" <?=($_GET['order'] == 'ar_desc')?'selected="selected"':''?>>Ár: csökkenő</option>
                    </select>
                </li>
                <li class="right full-width"><button class="btn btn-default btn-sm">szűrés <i class="fa fa-refresh"></i></button></li>
            </ul>
            </form>
        </div>

        <div class="list-view">
            <div class="category-title">
                <h1><?=$this->category->getName()?></h1>            
            </div>
            <div class="products">
                <? if( !$this->products->hasItems()): ?>
                <div class="no-product-items">
                    Nincsenek termékek ebben a kategóriában!
                </div>
                <? else: ?>
                    <div class="grid-container">
                    <? foreach ( $this->product_list as $p ) { 
                        $p['itemhash'] = hash( 'crc32', microtime() );
                        $p['sizefilter'] = ( count($this->products->getSelectedSizes()) > 0 ) ? true : false;
                        echo $this->template->get( 'product_list_item', $p );                   
                    } ?>
                    </div>
                    <div class="clr"></div>
                    <?=$this->navigator?>
                <br>
                <? endif; ?>
            </div>
        </div>

    </div>

    <script>
        $(function(){
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
    </script>
    
<? else: ?>
    <?=$this->render('home')?>
<? endif; ?>