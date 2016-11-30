<div class="order page-width">
    <div class="row np">
    <div class="col-md-12">
        <div class="responsive-view full-width">
            <?
                $o = $this->order;
                 $nevek = array(
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
                $vegosszeg = 0;
                $termek_ar_total = 0;
                if(!empty($o[items])):

                foreach($o[items] as $d):
                    $vegosszeg += $d[subAr];
                    $termek_ar_total += $d[subAr];
                endforeach;

                if ($o['b2b'] == 1) {
                  $vegosszeg = round($vegosszeg * AFA);
                }

                if($o[szallitasi_koltseg] > 0) $vegosszeg += $o[szallitasi_koltseg];
              //  if($o[kedvezmeny] > 0) $vegosszeg -= $o[kedvezmeny];

                $discount = $o[kedvezmeny_szazalek];
            ?>
            <div class="box orderpage">
                <div class="p10 head">
                    <div class="serial"><?=$o[azonosito]?></div>
                    <h1><?=$o[nev]?> rendelése</h1>
                    <div class="sub">
                        <span><em>Megrendelés leadva:</em> <?=\PortalManager\Formater::dateFormat($o[idopont], $this->settings['date_format'])?></span>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="p10 divBtm">
                    <?=$this->rmsg?>
                    <h5>Megrendelés állapota:</h5>
                    <div class="orderStatus">
                        <span style="color:<?=$this->orderAllapot[$o[allapot]][szin]?>;"><strong><?=$this->orderAllapot[$o[allapot]][nev]?></strong></span>
                        <? // PayPal fizetés
                            if($this->fizetes[Helper::getFromArrByAssocVal($this->fizetes,'ID',$o[fizetesiModID])][nev] == 'PayPal' && $o[paypal_fizetve] == 0): ?>
                                <div style="padding:10px 0;">
                                    <form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                                        <input type="hidden" name="cmd" value="_xclick">
                                        <INPUT TYPE="hidden" name="charset" value="utf-8">
                                        <input type="hidden" name="business" value="<?=$this->settings['paypal_email']?>">
                                        <input type="hidden" name="currency_code" value="HUF">
                                        <input type="hidden" name="item_name" value="<?=$this->settings['page_title']?> megrendelés: <?=$o[azonosito]?>">
                                        <input type="hidden" name="amount" value="<?=$vegosszeg?>">
                                        <INPUT TYPE="hidden" NAME="return" value="<?=DOMAIN?>order/<?=$o[accessKey]?>/paid_via_paypal#pay">
                                        <input type="image" src="<?=IMG?>i/paypal_payout.svg" border="0" style="height:35px;" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
                                    </form>
                                </div>
                            <? endif; ?>
                            <?php if ($o['b2b'] == 1): ?>
                              <?php if ($o['allapot'] == 7 && $o['payu_fizetve'] == 0): ?>
                                <br>
                                <?=$this->payu_btn?>
                              <?php else: ?>
                                <br>
                                <strong style="color: #0085ff; font-size: 13px;"> <i class="fa fa-info-circle"></i> A fizetést akkor kezdheti meg, ha a megrendelés állapota "Fizetésre vár" állapotba kerül.</strong>
                              <?php endif; ?>
                            <?php endif; ?>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="p10 divBtm items">
                     <h4>Megrendelt termékek</h4>
                     <div>
                        <div class="mobile-table-container overflowed">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <td>Termék</td>
                                    <td>Állapot</td>
                                    <td>Me.</td>
                                    <td>Egységár</td>
                                    <td>Ár</td>
                                </tr>
                            </thead>
                            <tbody>
                                <? foreach($o[items] as $d): ?>
                                <tr>
                                    <td>
                                        <div class="cont">
                                            <div class="img img-thb" onClick="document.location.href='<?=$d[url]?>'">
                                                <span class="helper"></span>
                                                <a href="<?=$d[url]?>" target="_blank">
                                                    <img src="<?=\PortalManager\Formater::productImage($d[profil_kep], 75, \ProductManager\Products::TAG_IMG_NOPRODUCT)?>" alt="<?=$d[nev]?>">
                                                </a>
                                            </div>
                                            <div class="name">
                                                <a href="<?=$d[url]?>" target="_blank"><?=$d[nev]?></a>
                                                <div class="sel-types">
                                                    <? if($d['meret']): ?><em>Méret:</em> <strong><?=$d['meret']?></strong><? endif;?>
                                                    <? if($d['szin']): ?><em>Szín:</em> <strong><?=$d['szin']?></strong><? endif;?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="center"><span style="color:<?=$d[allapotSzin]?>;"><strong><?=$d[allapotNev]?></strong></span></td>
                                    <td class="center"><span><?=$d[me]?></span></td>
                                    <td class="center"><span><?=Helper::cashFormat($d[egysegAr])?> Ft <?=($o['b2b'] == 1)?'<span class="pafa">+ ÁFA</span>':''?></span></td>
                                    <td class="center"><span><?=Helper::cashFormat($d[subAr])?> Ft <?=($o['b2b'] == 1)?'<span class="pafa">+ ÁFA</span>':''?></span></td>
                                </tr>
                                <? endforeach; ?>
                                <?php if ($o['b2b'] == 1): ?>
                                <tr style="font-size:15px;">
                                    <td class="right" colspan="4"><strong>Nettó termékár</strong></td>
                                    <td class="center"><span><strong><?=Helper::cashFormat($termek_ar_total)?> Ft</strong></span></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td class="right" colspan="4"><div><strong>Szállítási költség</strong></div></td>
                                    <td class="center"><span><?=Helper::cashFormat($o[szallitasi_koltseg])?> Ft</span></td>
                                </tr>
                                <tr>
                                    <td class="right" colspan="4"><div><strong>Kedvezmény</strong></div></td>
                                    <td class="center"><span><?=($o[kedvezmeny_szazalek] > 0)?'-'.Helper::cashFormat( ($termek_ar_total * ( $o[kedvezmeny_szazalek] / 100 + 1 )) - $termek_ar_total ) . ' Ft <em style="color:#999; font-size:0.85em;">(-'.$discount.'%)</em>' : '-'?> </span></td>
                                </tr>
                                <tr style="font-size:18px;">
                                    <td class="right" colspan="4"><strong>Végösszeg</strong></td>
                                    <td class="center"><span><strong><?=Helper::cashFormat($vegosszeg)?> Ft</strong></span></td>
                                </tr>
                            </tbody>
                        </table>
                        </div>
                     </div>
                </div>
                <a name="pay"></a>
                <div class="datas">
                     <h4>Adatok</h4>
                     <div class="row np">
                        <div class="col-md-12">
                            <div class="head"><strong>Kiválasztott szállítási mód:</strong></div>
                            <?=$this->szallitas[Helper::getFromArrByAssocVal($this->szallitas,'ID',$o[szallitasiModID])][nev]?> <em><?=Product::transTime($o[szallitasiModID])?></em>
                            <?
                            // PickPackPont
                            if( $o[szallitasiModID] == $this->settings['flagkey_pickpacktransfer_id'] ): ?>
                            <div class="showSelectedPickPackPont">
                                <div class="head">Kiválasztott <strong>Pick Pack</strong> átvételi pont:</div>
                                <div class="p5">
                                   <?=$o['pickpackpont_uzlet_kod']?>
                                </div>
                            </div>
                            <? endif; ?>
                            <?
                            // PostaPont
                            if($o[szallitasiModID] == $this->settings['flagkey_postaponttransfer_id']): ?>
                            <div class="showSelectedPostaPont">
                                <div class="head">Kiválasztott <strong>PostaPont</strong>:</div>
                                <div class="p5">
                                    <div class="row np">
                                        <div class="col-md-12 center">
                                           <?=$o['postapont']?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <? endif; ?>
                        </div>
                     </div>
                     <br>
                     <div class="row np">
                        <div class="col-md-12">
                            <div class="head"><strong>Kiválasztott fizetési mód:</strong></div>
                            <?=$this->fizetes[Helper::getFromArrByAssocVal($this->fizetes,'ID',$o[fizetesiModID])][nev]; ?>
                            <?
                            // PayU kártyás fizetés
                            if( $o['fizetesiModID'] == $this->settings['flagkey_pay_payu'] && $o['payu_fizetve'] == 0 ): ?>
                                <?php if ($o['b2b'] == 1 && $o['allapot'] == 7): ?>
                                  <br>
                                  <?=$this->payu_btn?>
                                <?php elseif($o['b2b'] != 1): ?>
                                  <br>
                                  <?=$this->payu_btn?>
                                <?php else: ?>
                                  <br>
                                  <strong style="color: #0085ff;"> <i class="fa fa-info-circle"></i> A fizetést akkor kezdheti meg, ha a megrendelés állapota "Fizetésre vár" állapotba kerül.</strong>
                                <?php endif; ?>
                            <? elseif( $o['fizetesiModID'] == $this->settings['flagkey_pay_payu'] && $o['payu_fizetve'] == 1 ): ?>
                                <? if( $o['payu_teljesitve'] == 0 ): ?>
                                <span class="payu-paidonly">Fizetve. Visszaigazolásra vár.</span>
                                <? else: ?>
                                <span class="payu-paid-done">Fizetve. Elfogadva.</span>
                                <? endif; ?>
                            <? endif; ?>

                            <? // PayPal fizetés
                            if($this->fizetes[Helper::getFromArrByAssocVal($this->fizetes,'ID',$o[fizetesiModID])][nev] == 'PayPal' && $o[paypal_fizetve] == 0): ?>
                                <div style="padding:10px 0;">
                                    <form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                                        <input type="hidden" name="cmd" value="_xclick">
                                        <INPUT TYPE="hidden" name="charset" value="utf-8">
                                        <input type="hidden" name="business" value="">
                                        <input type="hidden" name="currency_code" value="HUF">
                                        <input type="hidden" name="item_name" value="GoldFishing.hu megrendelés: <?=$o[azonosito]?>">
                                        <input type="hidden" name="amount" value="<?=$vegosszeg?>">
                                        <INPUT TYPE="hidden" NAME="return" value="<?=DOMAIN?>order/<?=$o[accessKey]?>/paid_via_paypal#pay">
                                        <input type="image" src="<?=IMG?>i/paypal_payout.svg" border="0" style="height:35px;" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
                                    </form>
                                </div>
                            <? elseif($o[paypal_fizetve] == 1): ?>
                                <br /><br />
                                <span style="font-size:13px;" class="label label-success">PayPal: Vételár fizetve!</span>
                            <? endif; ?>
                        </div>
                     </div>
                     <br>
                     <div class="row np">
                        <div class="col-sm-12">
                            <div class="head"><strong>Vásárlói megjegyzés a megrendeléshez:</strong></div>
                            <em><?=($o[comment] == '') ? '&mdash; nincs megjegyzés &mdash; ' : $o[comment]?></em>
                        </div>
                     </div>
                     <br>
                     <div class="row np">
                         <div class="col-sm-6 order-info">
                            <div class="head"><strong>Számlázási adatok</strong></div>
                            <div>
                                <? $szam = json_decode($o[szamlazasi_keys],true); ?>
                                <? foreach($szam as $h => $d): ?>
                                    <div class="col-md-4"><em><?=$nevek[$h]?></em></div>
                                    <div class="col-md-8"><?=(!empty($d)) ? $d : '-'?></div>
                                <? endforeach; ?>
                            </div>
                         </div>
                         <div class="col-sm-6 order-info">
                            <div class="head"><strong>Szállítási adatok</strong></div>
                             <div>
                                <? $szall = json_decode($o[szallitasi_keys],true); ?>
                                <? foreach($szall as $h => $d): ?>
                                    <div class="col-md-4"><em><?=$nevek[$h]?></em></div>
                                    <div class="col-md-8"><?=(!empty($d)) ? $d : '-'?></div>
                                <? endforeach; ?>
                            </div>
                         </div>
                     </div>
                </div>
            </div>
            <? else: ?>
            <div class="box">
                <div class="noItem">
                    <div>Hibás megrendelés azonosító</div>
                </div>
            </div>
            <? endif; ?>
        </div>
    </div>
</div>
</div>
