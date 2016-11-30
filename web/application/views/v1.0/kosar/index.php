<? $k = $this->kosar; ?>
<div class="cart page-width">
	<div class="row np" id="cart">
    <div class="col-sm-12">
    	<div class="responsive-view full-width">
    		<? if($this->gets[1] != 'done'): ?>
	    	<form method="post" action="">
	            <div class="cartItems">
	            	<?
					$no_ppp_itemNum = 0;
						$szuperakcios_termekek_ara = 0;
					?>
					<div class="box">
						<div class="p10">
							<div style="float:right;">
								<? if($this->gets[1] == '' || $this->gets[1] == '0'): ?>
									<button class="btn btn-default btn-sm" name="clearCart" title="Kosár ürítése">kosár üritése <i class="fa fa-trash-o"></i></button>
								<? endif; ?>
							</div>
							<h1>KOSÁR</h1>
						</div>
						<?=$this->msg?>
						<div class="divider"></div>
						<div class="right">
							<button class="btn btn-danger btn-sm mustReload" onclick="document.location.reload(true);">A kosár tartalma megváltozott. <strong>Kattintson</strong> a frissítéshez!</button>
						</div>
						<div class="mobile-table-container overflowed">
						<table class="table table-bordered">
							<thead>
								<tr class="item-header">
									<th class="center">Termék</th>
									<th class="center">Me.</th>
									<th class="center" width="15%"><?=(defined("B2BLOGGED"))?'Nettó ':''?><?=($this->user[kedvezmeny] > 0) ? 'Kedvezményes egységár':'Egységár'?></th>
									<th class="center" width="15%"><?=(defined("B2BLOGGED"))?'Nettó ':''?><?=($this->user[kedvezmeny] > 0) ? 'Kedvezményes ár':'Ár'?></th>
									<th class="center"></th>
								</tr>
							</thead>
							<tbody>
								<? if( count($k[items]) > 0 ): ?>
								<? foreach($k[items] as $d):
									if($d[szuper_akcios] == 1){
										$szuperakcios_termekek_ara += $d[sum_ar];
									}
									if($d[pickpackszallitas] == 0) $no_ppp_itemNum++;
									if($d[elorendelheto] == 1) $preOrder_item++;
								?>
								<tr class="item">
									<td class="main">
										<div class="img img-thb" onclick="document.location.href='<?=$d[url]?>'">
											<span class="helper"></span>
											<a href="<?=$d[url]?>"><img src="<?=Images::getThumbImg(75, $d[profil_kep])?>" alt="<?=$d[termekNev]?>" /></a>
										</div>
										<div class="tinfo">
											<div class="nev"><a href="<?=$d[url]?>"><?=$d[termekNev]?></a></div>
											<div class="sel-types">
												<? if($d['meret']): ?><em>Méret:</em> <strong><?=$d['meret']?></strong><? endif;?>
												<? if($d['szin']): ?><em>Szín:</em> <strong><?=$d['szin']?></strong><? endif;?>

											</div>
											<div class="subLine">
												<span title="Termék elérhetősége"><i class="fa fa-truck"></i> <?=$d[allapot]?></span> |
												<span title="Kiszállítási idő"><i class="fa fa-clock-o"></i> <?=$d[szallitasIdo]?></span>
												<? if(defined("B2BLOGGED")): ?>
												| <span class="stock stock-<?=($d['raktar_keszlet'] > 0)?'more':'none'?>">Készleten: <strong><?=$d['raktar_keszlet']?> db</strong></span>
												<? endif; ?>
											</div>

										</div>
									</td>
									<td class="center"><span><?=$d[me]?> db</span><? if($d['me'] > $d['raktar_keszlet']): ?><div class="over-me-stock"><i class="fa fa-exclamation-triangle"></i> max. <?=$d['raktar_keszlet']?> db</div><? endif; ?></td>
									<td class="center"><span><?=Helper::cashFormat($d[ar])?> Ft <?=(defined("B2BLOGGED"))?'<span class="pafa">+ ÁFA</span>':''?></span></td>
									<td class="center"><span class="cash"><strong><?=Helper::cashFormat($d[sum_ar])?> Ft</strong> <?=(defined("B2BLOGGED"))?'<span class="pafa">+ ÁFA</span>':''?></span></td>
									<td class="center action">
										<? if($this->gets[1] == '' || $this->gets[1] == '0'): ?>
										<span>
											<i class="fa fa-minus-square cart-adder desc" title="Kevesebb" onclick="Cart.removeItem(<?=$d[termekID]?>)"></i>
											<i class="fa fa-plus-square cart-adder asc" title="Több" onclick="Cart.addItem(<?=$d[termekID]?>)"></i>
										</span>
										<? endif; ?>
									</td>
								</tr>
								<? endforeach;
								// Végső ár kiszámolása
								$calc_final_total = $k[totalPrice] - $szuperakcios_termekek_ara;
								//$calc_final_total = ($calc_final_total -(($this->user[kedvezmeny]/100)*$calc_final_total)) + $szuperakcios_termekek_ara;
								?>
								</div>
								<? else: ?>
									<tr>
										<td colspan="99" class="center noCartItem">
					                    	<div>A kosár üres</div>
					                    </td>
									</tr>
				                <? endif; ?>
							</tbody>
						</table>
						<? if( $this->not_reached_min_price_text ): ?>
						<div class="not-enought-price-for-order"><?=$this->not_reached_min_price_text?></div>
						<? endif; ?>
						</div>
	            </div>
	       	<? else: ?>
	        <div class="box orderDone">
	        	<a name="step"></a>
	        	<div class="p10">
					<?
						$vegosszeg 				= 0;
						$orderedProducts 	= array();

						foreach($this->orderInfo[items] as $d):
							$vegosszeg 			+= $d[subAr];
							$orderedProducts[] 	= $d[nev];
						endforeach;

						if($this->orderInfo[szallitasi_koltseg] > 0) $vegosszeg += $this->orderInfo[szallitasi_koltseg];
						if($this->orderInfo[kedvezmeny] > 0) $vegosszeg -= $this->orderInfo[kedvezmeny];

					?>
	            	<h1><i class="fa fa-check-circle"></i><br />Megrendelés elküldve</h1>
	                <h2>Köszönjük megrendelését!</h2>
	                <p>E-mail címére folyamatos tájékoztatást küldünk megrendelésének állapotáról.</p>

					<? if( $this->orderInfo['fizetesiModID'] == $this->settings['flagkey_pay_payu'] && $this->orderInfo['payu_fizetve'] == 0 ): ?>
						<br>
						<strong>Online bankkártyás fizetés indítása: </strong><br>
						<?php if (defined("B2BLOGGED")): ?>
							<strong style="color:#ff6e00;">A kártyás fizetést akkor teljesítheti, ha feldolgoztuk megrendelését és a megrendelés állapotát "Fizetésre vár" állapotra változtatjuk.</strong><br>

						<?php else: ?>
							<?=$this->payu_btn?>
						<?php endif; ?>
					<? endif; ?>

					<? // PayPal fizetés
					if($this->fizetes[Helper::getFromArrByAssocVal($this->fizetes,'ID',$this->orderInfo[fizetesiModID])][nev] == 'PayPal' && $this->orderInfo[paypal_fizetve] == 0):
					?>
						<div style="padding:10px 0;">

						</div>
					<? endif; ?>
					<br />
					<div align="center">
						<br>
						<a href="<?=DOMAIN?>order/<?=$this->orderInfo[accessKey]?>" class="btn btn-info">Megrendelés adatlapja <i class="fa fa-arrow-circle-right"></i></a>
					</div>
	            </div>
	        </div>
	       	<? endif; ?>
			<a name="step"></a>
	        <? if(count($k[items]) > 0): ?>
			<div class="nextOrded">
	            <div class="box">
	                <h2>Termékek megrendelése</h2>
	                <input type="hidden" name="no_ppp_itemNum" value="<?=$no_ppp_itemNum?>" />
	                <? if($this->gets[1] != '' && $this->gets[1] != '0'): ?>
	                	<div class="allStepView">
	                    	<ul>
	                   		  <li class="<?=((int)$this->gets[1] == 1)?'active':(((int)$this->gets[1] > 1)?'done':'')?> <?=(in_array(1,$this->orderMustFillStep) && $this->orderStep)?'want':''?>"><a href="/kosar/1"><span class="p1">Számlázási/Szállítási adatok</span></a></li>
	                          <li class="<?=((int)$this->gets[1] == 2)?'active':(((int)$this->gets[1] > 2)?'done':'')?> <?=(in_array(2,$this->orderMustFillStep) && $this->orderStep)?'want':''?>"><a href="/kosar/2"><span class="p2">Átvételi mód</span></a></li>
	                          <li class="<?=((int)$this->gets[1] == 3)?'active':(((int)$this->gets[1] > 3)?'done':'')?> <?=(in_array(3,$this->orderMustFillStep) && $this->orderStep)?'want':''?>"><a href="/kosar/3"><span class="p3">Fizetési mód</span></a></li>
	                          <li class="<?=((int)$this->gets[1] == 4)?'active':(((int)$this->gets[1] > 4)?'done':'')?> <?=(in_array(4,$this->orderMustFillStep) && $this->orderStep)?'want':''?>"><a href="/kosar/4"><span class="p4">Megrendelés leadása</span></a></li>
	                    	</ul>
	                    	<div class="clr"></div>
	                    </div>
	                <? endif; ?>
	                <!--ORDER STEP 0.-->
	                <div class="steps step0 <?=($this->gets[1] == '0' || $this->gets[1] == '')?'on':''?>">
	                <div class="row np">
	                    <div class="col-sm-6 col col1">
	                    	<? if(!$this->user): ?>
	                        <div class="offline">
	                        	<div class="p10">
	                            	<div class="head"><strong>Alapadatok megadása</strong></div>
	                            	<input type="text" class="form-control" name="nev" value="<?=($this->orderExc)?$_POST[nev]:$this->storedString[0][nev]?>"  placeholder="Az Ön neve" />
	                                <? if($this->orderExc && in_array('nev',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                <br />
	                                <input type="text" class="form-control" name="email" value="<?=($this->orderExc)?$_POST[email]:$this->storedString[0][email]?>" placeholder="Az Ön e-mail címe" />
	                                <? if($this->orderExc && in_array('email',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                <div class="regInfo">vagy jelentkezzen be <br> <i class="fa fa-angle-down"></i></div>
	                            </div>
	                        </div>
	                        <div class="logIn">
	                        	<fieldset>
	                            	<div class="head"><strong>Bejelentkezés</strong></div>
	                                <div>
	                                	<div class="kedvezmeny">
		                                	<div>
		                                        <i class="fa fa-star"></i>
		                                        <i class="fa fa-star"></i>
		                                        <i class="fa fa-star"></i>
		                                        <i class="fa fa-star"></i>
		                                        <i class="fa fa-star"></i>
		                                    </div>
		                                    <div class="row np">
		                                    	<div class="col-sm-6">
		                                    		<h4>Törzsvásárlói <br> kedvezmény</h4>
				                                	Minden vásárlás után, regisztrált törzsvásárlói partnerek részére.<br />
				                                    <a href="/p/torzsvasarloi_kedvezmeny" target="_blank">részletek</a>
		                                    	</div>
		                                    	<div class="col-sm-6">
		                                    		<h4>Arena Water Card<br>kedvezmény</h4>
					                                A Jövő Bajnokainak szánt kártya utáni kedvezmény.<br>
					                                <a href="/p/arena_water_card" target="_blank">részletek</a>
		                                    	</div>
		                                    </div>
		                                    <div>
		                                        <i class="fa fa-star"></i>
		                                        <i class="fa fa-star"></i>
		                                        <i class="fa fa-star"></i>
		                                        <i class="fa fa-star"></i>
		                                        <i class="fa fa-star"></i>
		                                    </div>
	                               		</div>
	                               		<br>
	                                    <a href="/user/regisztracio?return=<?=$_SERVER['REQUEST_URI']?>" class="text-input" style="color:#666; font-size:0.8em;">regisztráció</a> &nbsp;
	                                    <a href="/user/belepes?return=<?=$_SERVER['REQUEST_URI']?>" class="btn btn-info btn-sm">Bejelentkezés</a>
	                                </div>
	                            </fieldset>
	                        </div>
	                        <? else: ?>
	                       		<div class="online" align="center">
	                            	<div class="head orange">Bejelentkezve mint, <strong><?=$this->user[data][nev]?></strong>!</div>
	                                <div class="p10">
	                                    <div class="head"><strong>Alapadatok</strong></div>
	                                    <input type="text" class="form-control" name="nev" value="<?=($this->orderExc)?$_POST[nev]:$this->user[data][nev]?>" readonly="readonly"  placeholder="Az Ön neve" />
	                                    <? if($this->orderExc && in_array('nev',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    <br />
	                                    <input type="text" class="form-control" name="email" value="<?=($this->orderExc)?$_POST[email]:$this->user[data][email]?>" readonly="readonly" placeholder="Az Ön e-mail címe" />
	                                    <? if($this->orderExc && in_array('email',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                </div>
	                                <br>
																	<?php if (!defined("B2BLOGGED")): ?>
	                                <div class="row np">
	                                    <div class="col-sm-12">
	                                        <div class="discount-info">
	                                            <div class="head">Kedvezmények vásárlásai után</div>
	                                            <div class="row np">
	                                            	<div class="col-sm-6 left">
	                                            		<div class="list">
			                                        		<? foreach( $this->user['kedvezmenyek'] as $kedv ): ?>
															<div><?=$kedv['nev']?>: <span class="num"><?=$kedv['kedvezmeny']?>%</span> </div>
			                                        		<? endforeach; ?>
			                                        	</div>
	                                            	</div>
	                                            	<div class="col-sm-6 center">
	                                            		<strong>Az Ön kedvezménye:</strong>
			                                            <div class="dc-num"><?=$this->user[kedvezmeny]?>%</div>
	                                            	</div>
	                                            </div>

	                                        </div>
	                                    </div>
	                                </div>
																	<?php endif; ?>
	                            </div>
	                        <? endif; ?>
	                    </div>
	                    <div class="col-sm-6 col col2">
	                    	<div class="col2In">
	                            <div class="cartInfo">
	                                <div class="tetel"><?=$k[itemNum]?> db tétel</div>
	                                <div class="totalPrice">
	                                	<? if($this->user[kedvezmeny] > 0): ?>
	                                    	<div class="kedvPrice">kedvezményesen <strong><?=Helper::cashFormat($calc_final_total)?> Ft</strong></div>
	                                    <? else: ?>
	                                    	<?=$this->price_netbr?> <strong><?=Helper::cashFormat($calc_final_total)?></strong> Ft
																				<?php if (defined("B2BLOGGED")): ?>
																					<div>bruttó <strong><?=Helper::cashFormat($calc_final_total*AFA)?></strong> Ft</div>
																				<?php endif; ?>
	                                    <? endif;?>
	                                </div>
	                            </div>
	                            <div class="megrendel">
	                                <button name="orderState" value="start"  type="submit" class="btn btn-success">Megrendelés folytatása <i class="fa fa-arrow-circle-right"></i></button>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            	</div>
	                <!--/ORDER STEP 0.-->

	                <!--ORDER STEP 1.-->
	                <div class="steps step1 <?=($this->gets[1] == '1')?'on':''?>">
	                	<div class="row np">
	                    	<div class="col-sm-6 col1">
	                        	<div class="head">Számlázási adatok</div>
	                            <div class="p10 input-fields">
	                            	<div class="row">
	                            		<div class="col-sm-12">
	                                         <input type="text" class="form-control" name="szam_nev" value="<?=($this->orderExc)?$_POST[szam_nev]:(($this->storedString[1])?$this->storedString[1][szam_nev]:$this->user[data][nev])?>" placeholder="* Név" />
	                                         <? if($this->orderExc && in_array('szam_nev',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                            	</div>
	                            	<div class="row">
	                            		<div class="col-sm-4">
	                                         <input type="text" class="form-control" name="szam_irsz" value="<?=($this->orderExc)?$_POST[szam_irsz]:(($this->storedString[1])?$this->storedString[1][szam_irsz]:$this->user[szamlazasi_adat][irsz])?>" placeholder="* Irányítószám" />
	                                         <? if(!empty($this->buyer_inputs_hints['irsz'])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints['irsz']?></div><? endif; ?>
	                                         <? if($this->orderExc && in_array('szam_irsz',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                                    <div class="col-sm-2">
	                                         <input type="text" class="form-control" name="szam_kerulet" value="<?=($this->orderExc)?$_POST[szam_kerulet]:(($this->storedString[1])?$this->storedString[1][szam_kerulet]:$this->user[szamlazasi_adat][kerulet])?>" placeholder="Kerület" />
	                                         <? if(!empty($this->buyer_inputs_hints['kerulet'])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints['kerulet']?></div><? endif; ?>
	                                         <? if($this->orderExc && in_array('szam_kerulet',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                                    <div class="col-sm-6">
	                                         <input type="text" class="form-control" name="szam_city" value="<?=($this->orderExc)?$_POST[szam_city]:(($this->storedString[1])?$this->storedString[1][szam_city]:$this->user[szamlazasi_adat][city])?>" placeholder="* Település" />
	                                         <? if(!empty($this->buyer_inputs_hints['city'])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints['city']?></div><? endif; ?>
	                                         <? if($this->orderExc && in_array('szam_city',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                            	</div>
	                            	<div class="row">
	                            		<div class="col-sm-8">
	                                         <input type="text" class="form-control" name="szam_uhsz" value="<?=($this->orderExc)?$_POST[szam_uhsz]:(($this->storedString[1])?$this->storedString[1][szam_uhsz]:$this->user[szamlazasi_adat][uhsz])?>" placeholder="* Közterület neve" />
	                                         <? if(!empty($this->buyer_inputs_hints['uhsz'])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints['uhsz']?></div><? endif; ?>
	                                         <? if($this->orderExc && in_array('szam_uhsz',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                                    <div class="col-sm-4">
	                                    	 <select class="form-control" name="szam_kozterulet_jellege">
	                                    		<?
	                                    		$pval = $this->user[szamlazasi_adat][kozterulet_jellege];

	                                    		if($this->orderExc) {
	                                    			$pval = $_POST[szam_kozterulet_jellege];
	                                    		} else if($this->storedString[1]) {
	                                    			$pval = $this->storedString[1][szam_kozterulet_jellege];
	                                    		}

	                                    		?>
						                        <? foreach( $this->kozterulet_jellege as $kj ): ?>
						                            <option value="<?=$kj?>" <?=($pval == $kj) ? 'selected="selected"' : ''?>><?=$kj?></option>
						                        <? endforeach; ?>
						                     </select>
						                     <? if(!empty($this->buyer_inputs_hints['kozterulet_jellege'])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints['kozterulet_jellege']?></div><? endif; ?>
	                                         <? if($this->orderExc && in_array('szam_kozterulet_jellege',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                            	</div>
	                            	<div class="row">
	                            		<div class="col-sm-4">
	                                         <input type="text" class="form-control" name="szam_hazszam" value="<?=($this->orderExc)?$_POST[szam_hazszam]:(($this->storedString[1])?$this->storedString[1][szam_hazszam]:$this->user[szamlazasi_adat][hazszam])?>" placeholder="* Házszám" title="Házszám" />
	                                         <? if(!empty($this->buyer_inputs_hints['hazszam'])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints['hazszam']?></div><? endif; ?>
	                                         <? if($this->orderExc && in_array('szam_hazszam',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                                    <div class="col-sm-4">
	                                         <input type="text" class="form-control" name="szam_epulet" value="<?=($this->orderExc)?$_POST[szam_epulet]:(($this->storedString[1])?$this->storedString[1][szam_epulet]:$this->user[szamlazasi_adat][epulet])?>" placeholder="Épület" title="Épület" />
	                                         <? if(!empty($this->buyer_inputs_hints['epulet'])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints['epulet']?></div><? endif; ?>
	                                         <? if($this->orderExc && in_array('szam_epulet',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                                    <div class="col-sm-4">
	                                         <input type="text" class="form-control" name="szam_lepcsohaz" value="<?=($this->orderExc)?$_POST[szam_lepcsohaz]:(($this->storedString[1])?$this->storedString[1][szam_lepcsohaz]:$this->user[szamlazasi_adat][lepcsohaz])?>" placeholder="Lépcsőház" title="Lépcsőház" />
	                                         <? if(!empty($this->buyer_inputs_hints['lepcsohaz'])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints['lepcsohaz']?></div><? endif; ?>
	                                         <? if($this->orderExc && in_array('szam_lepcsohaz',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                            	</div>
	                            	<div class="row">
	                            		<div class="col-sm-4">
	                                         <input type="text" class="form-control" name="szam_szint" value="<?=($this->orderExc)?$_POST[szam_szint]:(($this->storedString[1])?$this->storedString[1][szam_szint]:$this->user[szamlazasi_adat][szint])?>" placeholder="Szint" title="Szint" />
	                                         <? if(!empty($this->buyer_inputs_hints['szint'])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints['szint']?></div><? endif; ?>
	                                         <? if($this->orderExc && in_array('szam_szint',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                                    <div class="col-sm-4">
	                                         <input type="text" class="form-control" name="szam_ajto" value="<?=($this->orderExc)?$_POST[szam_ajto]:(($this->storedString[1])?$this->storedString[1][szam_ajto]:$this->user[szamlazasi_adat][ajto])?>" placeholder="Ajtó" title="Ajtó" />
	                                         <? if(!empty($this->buyer_inputs_hints['ajto'])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints['ajto']?></div><? endif; ?>
	                                         <? if($this->orderExc && in_array('szam_ajto',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                            	</div>
                                    <div class="row">
                                    	 <div class="col-sm-12">
	                                    	<input type="checkbox" id="sameOfSzam"/><label for="sameOfSzam">a szállítási adatokkal megegyezik</label>
	                                    </div>
                                    </div>
	                            </div>
	                        </div>
	                      <div class="col-sm-6 divCol left col2">
	                        	<div class="head">Szállítási adatok</div>
	                            <div class="p10 input-fields">
	                            	<div class="row">
	                            		<div class="col-sm-12">
	                                         <input type="text" class="form-control" name="szall_nev" value="<?=($this->orderExc)?$_POST[szall_nev]:(($this->storedString[1])?$this->storedString[1][szall_nev]:$this->user[data][nev])?>" placeholder="* Név" />
	                                         <? if($this->orderExc && in_array('szall_nev',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                            	</div>
	                            	<div class="row">
	                            		<div class="col-sm-4">
	                                         <input type="text" class="form-control" name="szall_irsz" value="<?=($this->orderExc)?$_POST[szall_irsz]:(($this->storedString[1])?$this->storedString[1][szall_irsz]:$this->user[szallitasi_adat][irsz])?>" placeholder="* Irányítószám" />
	                                         <? if(!empty($this->buyer_inputs_hints['irsz'])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints['irsz']?></div><? endif; ?>
	                                         <? if($this->orderExc && in_array('szall_irsz',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                                    <div class="col-sm-2">
	                                         <input type="text" class="form-control" name="szall_kerulet" value="<?=($this->orderExc)?$_POST[szall_kerulet]:(($this->storedString[1])?$this->storedString[1][szall_kerulet]:$this->user[szallitasi_adat][kerulet])?>" placeholder="Kerület" />
	                                         <? if(!empty($this->buyer_inputs_hints['kerulet'])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints['kerulet']?></div><? endif; ?>
	                                         <? if($this->orderExc && in_array('szall_kerulet',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                                    <div class="col-sm-6">
	                                         <input type="text" class="form-control" name="szall_city" value="<?=($this->orderExc)?$_POST[szall_city]:(($this->storedString[1])?$this->storedString[1][szall_city]:$this->user[szallitasi_adat][city])?>" placeholder="* Település" />
	                                         <? if(!empty($this->buyer_inputs_hints['city'])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints['city']?></div><? endif; ?>
	                                         <? if($this->orderExc && in_array('szall_city',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                            	</div>
	                            	<div class="row">
	                            		<div class="col-sm-8">
	                                         <input type="text" class="form-control" name="szall_uhsz" value="<?=($this->orderExc)?$_POST[szall_uhsz]:(($this->storedString[1])?$this->storedString[1][szall_uhsz]:$this->user[szallitasi_adat][uhsz])?>" placeholder="* Közterület neve" />
	                                         <? if(!empty($this->buyer_inputs_hints['uhsz'])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints['uhsz']?></div><? endif; ?>
	                                         <? if($this->orderExc && in_array('szall_uhsz',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                                    <div class="col-sm-4">
	                                          <select class="form-control" name="szall_kozterulet_jellege">
	                                    		<?
	                                    		$pval = $this->user[szallitasi_adat][kozterulet_jellege];

	                                    		if($this->orderExc) {
	                                    			$pval = $_POST[szall_kozterulet_jellege];
	                                    		} else if($this->storedString[1]) {
	                                    			$pval = $this->storedString[1][szall_kozterulet_jellege];
	                                    		}

	                                    		?>
						                        <? foreach( $this->kozterulet_jellege as $kj ): ?>
						                            <option value="<?=$kj?>" <?=($pval == $kj) ? 'selected="selected"' : ''?>><?=$kj?></option>
						                        <? endforeach; ?>
						                     </select>
	                                         <? if(!empty($this->buyer_inputs_hints['kozterulet_jellege'])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints['kozterulet_jellege']?></div><? endif; ?>

	                                         <? if($this->orderExc && in_array('szall_kozterulet_jellege',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                            	</div>
	                            	<div class="row">
	                            		<div class="col-sm-4">
	                                         <input type="text" class="form-control" name="szall_hazszam" value="<?=($this->orderExc)?$_POST[szall_hazszam]:(($this->storedString[1])?$this->storedString[1][szall_hazszam]:$this->user[szallitasi_adat][hazszam])?>" placeholder="* Házszám" title="Házszám" />
	                                         <? if(!empty($this->buyer_inputs_hints['hazszam'])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints['hazszam']?></div><? endif; ?>
	                                         <? if($this->orderExc && in_array('szall_hazszam',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                                    <div class="col-sm-4">
	                                         <input type="text" class="form-control" name="szall_epulet" value="<?=($this->orderExc)?$_POST[szall_epulet]:(($this->storedString[1])?$this->storedString[1][szall_epulet]:$this->user[szallitasi_adat][epulet])?>" placeholder="Épület" title="Épület" />
	                                         <? if(!empty($this->buyer_inputs_hints['epulet'])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints['epulet']?></div><? endif; ?>
	                                         <? if($this->orderExc && in_array('szall_epulet',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                                    <div class="col-sm-4">
	                                         <input type="text" class="form-control" name="szall_lepcsohaz" value="<?=($this->orderExc)?$_POST[szall_lepcsohaz]:(($this->storedString[1])?$this->storedString[1][szall_lepcsohaz]:$this->user[szallitasi_adat][lepcsohaz])?>" placeholder="Lépcsőház" title="Lépcsőház" />
	                                         <? if(!empty($this->buyer_inputs_hints['lepcsohaz'])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints['lepcsohaz']?></div><? endif; ?>
	                                         <? if($this->orderExc && in_array('szall_lepcsohaz',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                            	</div>
	                            	<div class="row">
	                            		<div class="col-sm-4">
	                                         <input type="text" class="form-control" name="szall_szint" value="<?=($this->orderExc)?$_POST[szall_szint]:(($this->storedString[1])?$this->storedString[1][szall_szint]:$this->user[szallitasi_adat][szint])?>" placeholder="Szint" title="Szint" />
	                                         <? if(!empty($this->buyer_inputs_hints['szint'])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints['szint']?></div><? endif; ?>
	                                         <? if($this->orderExc && in_array('szall_szint',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                                    <div class="col-sm-4">
	                                         <input type="text" class="form-control" name="szall_ajto" value="<?=($this->orderExc)?$_POST[szall_ajto]:(($this->storedString[1])?$this->storedString[1][szall_ajto]:$this->user[szallitasi_adat][ajto])?>" placeholder="Ajtó" title="Ajtó" />
	                                         <? if(!empty($this->buyer_inputs_hints['ajto'])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints['ajto']?></div><? endif; ?>
	                                         <? if($this->orderExc && in_array('szall_ajto',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                                    <div class="col-sm-4">
	                                         <input type="text" class="form-control" name="szall_phone" value="<?=($this->orderExc)?$_POST[szall_phone]:(($this->storedString[1])?$this->storedString[1][szall_phone]:$this->user[szallitasi_adat][phone])?>" placeholder="* Telefonszám" />
	                                         <? if(!empty($this->buyer_inputs_hints['phone'])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints['phone']?></div><? endif; ?>
	                                         <? if($this->orderExc && in_array('szall_phone',$this->orderExc[input])): ?><span class="errMsg">Kérjük, töltse ki ezt a mezőt!</span><? endif; ?>
	                                    </div>
	                            	</div>
                                    <div class="row">
                                    </div>
	                            </div>
	                        </div>
	                    </div>
	                        <div class="clr"></div>
	                </div>
	                <!--/ORDER STEP 1.-->
	                <!--ORDER STEP 2.-->
	                <div class="steps step2 <?=($this->gets[1] == '2')?'on':''?>" style="padding:0;">
	                	<div class="row np">
	                    	<div class="col-sm-12">
	                        	<ul class="atvetel">
	                            	<?
																foreach($this->szallitas as $d):
																if (defined("B2BLOGGED")) {
																	if ($d['on_b2b'] == 0) {
																		continue;
																	}
																}
																?>
	                        		<li><input <?=($this->storedString[2][atvetel] == $d[ID])?'checked':''?> id="atvet_<?=$d[ID]?>" type="radio" name="atvetel" value="<?=$d[ID]?>" <?=($d[ID] == 2 && $no_ppp_itemNum != 0)?'disabled':''?>/><label for="atvet_<?=$d[ID]?>"><?=$d[nev]?> <em><?=Product::transTime($d[ID])?></em><? if($d[ID] == 2 && $no_ppp_itemNum != 0): ?><br /><span class="subtitle"><?=$no_ppp_itemNum?> db termék nem szállítható Pick Pack Pontra</span><? endif; ?></label>
	                                <?
	                                // PICK PACK PONT ÁTVÉTEL FORM
	                                if( $d['ID'] == $this->settings['flagkey_pickpacktransfer_id'] ): ?>
	                                <div class="pickpackpont" style="display:none;">
	                                	<input type="hidden" id="ppp_uzlet" name="ppp_uzlet" value="<?=$this->storedString[2][ppp_uzlet]?>">
	                                	<input type="hidden" id="ppp_uzlet_str" name="ppp_uzlet_n" value="<?=$this->storedString[2][ppp_uzlet_n]?>">
	                                	<iframe width="100%" height="504px" src="http://online.sprinter.hu/terkep/#/"></iframe>
	                                </div>
	                                <? endif;?>
	                                <?
	                                // PostaPont átvétel FORM
	                                if( $d['ID'] == '5' ):?>
	                                <div class="postapont" style="display:none;">
	                                	<input type="hidden" id="ugyfelform_iranyitoszam" value="<?=($this->orderExc)?$_POST[szall_irsz]:(($this->storedString[1])?$this->storedString[1][szall_irsz]:$this->user[szallitasi_adat][irsz])?>">
	                                	<input type="hidden" id="valasztott_postapont" name="pp_selected" value="">
	                                	<!-- Postapont választó (Ügyfél oldalra beépítendő rész) -->
										<div id="postapontvalasztoapi"></div>
										<div class="clr"></div>
										<script type="text/javascript">
											ppapi.setMarkers('20_molkut', false);
											ppapi.setMarkers('30_csomagautomata', false);
											ppapi.linkZipField('ugyfelform_iranyitoszam'); //<-- A megrendelő form input elemének a megjelölése (beállítása a kiválasztó számára)
											ppapi.insertMap('postapontvalasztoapi'); //<-- PostaPont választó API beillesztése ( ilyen azonosítóval rendelkező DOM objektumba)
											ppapi.onSelect = function(data){ //<-- Postapont kiválasztásra bekövetkező esemény lekötése
												// Minta! A kiválasztott PostaPont adatainak visszaírása a megrendelő form rejtett mezőjébe.
												$('#valasztott_postapont').val( data['name']+" ("+data['zip'] + " " + data['county']+", "+data['address']+")" );
												$('#selected_pp_data_info').html( data['name']+" ("+data['zip'] + " " + data['county']+", "+data['address']+")" )
												console.log(jQuery.param(data));
											};
										</script>
										<div id="pp-data-info">Kiválasztott PostaPont: <span id="selected_pp_data_info">nincs kiválasztva!</span></div>
										<!-- E:Postapont választó -->
	                                </div>
	                            	<? endif; ?>
	                                </li>
	                                <? endforeach; ?>
	                        	</ul>
	                        </div>
	                    </div>
	                </div>
	                <!--/ORDER STEP 2.-->
	                <!--ORDER STEP 3.-->
	                <div class="steps step3 <?=($this->gets[1] == '3')?'on':''?>" style="padding:0;">
	                	<div class="row np">
	                    	<div class="col-sm-12">
	                        	<ul class="atvetel">
	                            <? foreach($this->fizetes as $d): ?>
	                            	 <? if(in_array($this->storedString[2][atvetel],$d[in_szallitas_mod])): ?>
	                        		<li>
	                        			<input <?=($this->storedString[3][fizetes] == $d[ID])?'checked':''?> id="fizetes_<?=$d[ID]?>" type="radio" name="fizetes" value="<?=$d[ID]?>"/>
	                        			<label for="fizetes_<?=$d[ID]?>"><?=$d[nev]?> <? if($d['ID'] == $this->settings['flagkey_pay_payu']): ?> <a href="http://simplepartner.hu/PaymentService/Fizetesi_tajekoztato.pdf" target="_blank">
<img style="height:25px !important;" src="<?=IMG?>simple_logo_long.png" title="Simple - Online bankkártyás fizetés" alt="Simple vásárlói tájékoztató"> </a> <? endif; ?></label>
	                        		</li>
	                                <? endif; ?>
	                           	<? endforeach; ?>
	                            </ul>
	                        </div>
	                    </div>
	                </div>
	                <!--/ORDER STEP 3.-->
	                <!--ORDER STEP 4.-->
	                <div class="steps step4 <?=($this->gets[1] == '4')?'on':''?>">
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
	                		$szallias_informacio = $this->szallitas[Helper::getFromArrByAssocVal($this->szallitas,'ID',$this->storedString[2][atvetel])];

	                		$szallitasiKoltseg 	= (int)$szallias_informacio[koltseg];

	                		// Ingyenes szállítás, ha túlhalad az összeghatáron, amikortól már ingyenes a szállítás
	                		if( $szallias_informacio[osszeghatar] != '0' && ($k[totalPrice]-$szuperakcios_termekek_ara) > (int) $szallias_informacio[osszeghatar] ){
	                			$szallitasiKoltseg = 0;
	                		}

							$kedvezmeny 		= ($this->user && $this->user[kedvezmeny] > 0) ? (($k[totalPrice] - $szuperakcios_termekek_ara) * (($this->user[kedvezmeny]/100))) : 0;
							$vegosszeg 			= $calc_final_total;

						?>
	                	<div class="row np" style="margin-top:5px;">
	                    	<div class="col-sm-6 col1">
	                        	<div class="head"><h4>Számlázási adatok</h4></div>
	                            <? if($this->orderExc && in_array(1,$this->orderMustFillStep)): ?>
	                            	<div align="center" class="p10"><span class="mustSelect"><i class="fa fa-warning"></i> Figyelem! Hiányoznak a számlázási adatok. Kérjük pótolja!</span></div>
	                            <? else: ?>
	                            <div class="order-contact-info">
	                            	<? foreach($szmnev as $szkey => $szval): if($szkey == 'phone') continue; ?>
	                            	<div class="row np">
	                                	<div class="col-sm-5">
	                                    	<strong><?=$szval?></strong>
	                                    </div>
	                                    <div class="col-sm-7 right">
	                                    	<?=($this->storedString[1]['szam_'.$szkey]) ? $this->storedString[1]['szam_'.$szkey] : '&mdash;'?>
	                                    </div>
	                                </div>
	                            	<? endforeach; ?>
	                                <div class="row np">
	                                	<div class="col-sm-5">
	                                    	<strong>&nbsp;</strong>
	                                    </div>
	                                    <div class="col-sm-7 right">

	                                    </div>
	                                </div>
	                            </div>
	                            <? endif; ?>
	                        </div>
	                        <div class="col-sm-6 col2" style="border-left:1px solid #ddd;">
	                        	<div class="head"><h4>Szállítási adatok</h4></div>
	                             <? if($this->orderExc && in_array(1,$this->orderMustFillStep)): ?>
	                            	<div align="center" class="p10"><span class="mustSelect"><i class="fa fa-warning"></i> Figyelem! Hiányoznak a szállítási adatok. Kérjük pótolja!</span></div>
	                            <? else: ?>
	                            <div class="order-contact-info">
	                            	<? foreach($szmnev as $szkey => $szval):?>
	                            	<div class="row np">
	                                	<div class="col-sm-5">
	                                    	<strong><?=$szval?></strong>
	                                    </div>
	                                    <div class="col-sm-7 right">
	                                    	<?=($this->storedString[1]['szall_'.$szkey]) ? $this->storedString[1]['szall_'.$szkey] : '&mdash;'?>
	                                    </div>
	                                </div>
	                            	<? endforeach; ?>
	                            </div>
	                            <? endif; ?>
	                        </div>
	                    </div>
	                    <div class="row np topDiv">
	                    	<div class="col-sm-12">
	                        	<div class="p10">
	                            	<h4>Átvétel módja</h4>
	                                <div>
	                                	<? if($this->orderExc && in_array(2,$this->orderMustFillStep)): ?>
	                                    <span class="mustSelect"><i class="fa fa-warning"></i> Figyelem! Hiányzik az <strong>átvételi mód</strong>. Kérjük, hogy válassza ki az Önnek megfelelőt!</span>
	                                    <? endif; ?>

	                                    <?=$this->szallitas[Helper::getFromArrByAssocVal($this->szallitas,'ID',$this->storedString[2][atvetel])][nev]; ?>  <em><?=Product::transTime($this->storedString[2][atvetel])?></em>
	                                    <? // PostaPont info
	                                    if($this->storedString[2][atvetel] == '5'): ?>
	                                    <a href="/p/postapont" title="Részletek" target="_blank"><i class="fa fa-info-circle "></i></a>
	                                	<? endif; ?>
	                                	 <? // PickPackPont info
	                                    if($this->storedString[2][atvetel] == '2'): ?>
	                                    <a href="/p/pick_pack_pont" title="Részletek" target="_blank"><i class="fa fa-info-circle "></i></a>
	                                	<? endif; ?>
	                                	 <? // Házhoz szállítás info
	                                    if($this->storedString[2][atvetel] == '1'): ?>
	                                    <a href="/p/szallitasi_feltetelek"  title="Részletek" target="_blank"><i class="fa fa-info-circle "></i></a>
	                                	<? endif; ?>

	                                    <?
	                                    // PickPackPont átvétel
	                                    if($this->storedString[2][atvetel] == $this->settings['flagkey_pickpacktransfer_id']): ?>

	                                        <? if($this->storedString[2][ppp_uzlet_n] != ''): ?>
	                                        <input type="hidden" name="ppp_uzlet_done" value="<?=$this->storedString[2][ppp_uzlet_n]?>" />
	                                        <input type="hidden" name="ppp_uzlet_str" value="<?=$this->storedString[2][ppp_uzlet_n]?>" />
	                                    	<div class="showSelectedPickPackPont">
	                                        	<div class="head">Kiválasztott <strong>Pick Pack</strong> átvételi pont:</div>
	                                           	<div class="">Kiválasztott átvételi pont: <strong><?=$this->storedString[2][ppp_uzlet_n]?></strong></div>
	                                        </div>
	                                        <? else: ?>
	                                        	<div class="mustSelect"><i class="fa fa-warning"></i> Figyelem! Nincs kiválasztva a <string>Pick Pack átvételi Pont</string>. <a href="/kosar/2">Kérjük, hogy válassza ki!</a></div>
	                                        <? endif; ?>
	                                    <? endif; ?>

	                                    <?
	                                    // PostaPont átvétel
	                                    if($this->storedString[2][atvetel] == '5'): ?>
	                                    	<br>
	                                    	<img src="<?=IMG?>/icons/postapont_logos_big.png" alt="PostaPont" width="150">
	                                    	<br /><br />
	                                        <? if($this->storedString[2][pp_selected] != ''): ?>
	                                        <input type="hidden" name="pp_selected_point" value="<?=$this->storedString[2][pp_selected]?>" />

	                                    	<div class="showSelectedPostaPont">
	                                        	<div class="head">Kiválasztott <strong>PostaPont</strong>:</div>
	                                            <div class="p5">
	                                            	<div class="row np">
	                                                    <div class="col-sm-12 left">
	                                                    	<?=$this->storedString[2][pp_selected]?>
	                                                    </div>
	                                                </div>
	                                            </div>
	                                        </div>
	                                        <? else: ?>
	                                        	<span class="mustSelect"><i class="fa fa-warning"></i> Figyelem! Nincs kiválasztva a <string>PostaPont</string> átvételi pont. Kérjük, hogy válassza ki!</span>
	                                        <? endif; ?>
	                                    <? endif; ?>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="row np topDiv">
	                    	<div class="col-sm-12">
	                        	<div class="p10">
	                            	<h4>Fizetés módja</h4>
	                                <div>
	                                	<? if($this->orderExc && in_array(3,$this->orderMustFillStep)): ?>
	                                    <span class="mustSelect"><i class="fa fa-warning"></i> Figyelem! Hiányzik a <strong>fizetési mód</strong>. Kérjük, hogy válassza ki az Önnek megfelelőt!</span>
	                                    <? endif; ?>
	                                	 <?=$this->fizetes[Helper::getFromArrByAssocVal($this->fizetes,'ID',$this->storedString[3][fizetes])][nev]; ?>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
						<div class="row np topDiv">
	                    	<div class="col-sm-12">
	                        	<div class="p10">
	                            	<h4>Megjegyzés a megrendeléshez</h4>
	                                <div>
	                                	<textarea name="comment" placeholder="" class="form-control"></textarea>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                    <? if( $k[has_request_price] > 0 ): ?>
	                    <div class="has-requested-price">
				        	<i class="fa fa-exclamation-triangle"></i>
				        	<h4>FIGYELEM!</h4>
				        	<div>A "Termékek ára", "Szállítási költség" és a "Kedvezmény összege" adat nem mérvadó, csak tájékoztató jellegű, mivel megrendelt termékei közt van olyan termék, ahol <a href="/kapcsolat" target="_blank">érdeklődni</a> kell a vételár felől!</div>
				        </div>
				    	<? endif; ?>
	                    <div class="row np">
	                    	<div class="col-sm-12 price">
	                       		<div class="p10">
	                            	<div class="p inf">
	                                	<span class="n"><?=($this->user[kedvezmeny] > 0)?'Termékek kedvezményes ára:':'Termékek ára:'?></span>
	                                    <span class="a"><span class="ar"><?=($this->user[kedvezmeny] > 0) ? Helper::cashFormat(\PortalManager\Formater::discountPrice($k[totalPrice]), $this->user[kedvezmeny], true) : Helper::cashFormat($k[totalPrice])?></span> Ft</span>
	                                </div>
	                                <div class="p inf">
	                                	<span class="n">Kedvezmény:</span>
	                                    <span class="a"><span class="ar"><?=($this->user[kedvezmeny]> 0)? '<span class="kedv">'.$this->user[kedvezmeny].'%</span>':'</span>&mdash;'?></span>
	                                </div>
	                            	<div class="p">
																		<?php if (defined("B2BLOGGED")): $szallitasiKoltseg = 0; endif; ?>
	                                	<span class="n">Szállítási költség:</span>
	                                    <span class="a"><span class="ar"><?=($szallitasiKoltseg > 0)?'+'.Helper::cashFormat($szallitasiKoltseg):'0'?></span> Ft<?=(defined("B2BLOGGED"))?'*':''?></span>
	                                </div>
	                                <div class="p end">
	                                	<?
	                                    	if($szallitasiKoltseg > 0){	$vegosszeg += $szallitasiKoltseg; }
											//if($kedvezmeny > 0){	$vegosszeg -= $kedvezmeny; }
										?>
                                		<span class="n">Végösszeg:</span>
                                    <span class="a">
																			<?=$this->price_netbr?> <span class="ar"><?=Helper::cashFormat($vegosszeg)?></span> Ft
																			<?php if (defined("B2BLOGGED") && $this->price_netbr == 'nettó'): ?>
																			<div class="b2b-br-price">bruttó <span class="cash"><?=Helper::cashFormat($vegosszeg*AFA)?></span> Ft</div>
																			<?php endif; ?>
																		</span>
                                    <input type="hidden" name="kedvezmeny" value="<?=$this->user[kedvezmeny]?>" />
                                    <input type="hidden" name="szallitasi_koltseg" value="<?=$szallitasiKoltseg?>" />
	                               	</div>
	                            </div>
	                        </div>
	                        <? if(!$this->user): ?>
	                        <div class="col-sm-12 kedvezmeny">
	                       		<div>
	                            	<i class="fa fa-gift"></i> Tudta, ha regisztrált felhasználóként rendel a(z) <?=$this->settings['page_title']?> webáruházától, akkor kedvezményesebben vásárolhat? | <a target="_blank" href="/p/torzsvasarloi_kedvezmeny">Kattintson a részletekért <i class="fa fa-link"></i></a>
	                            </div>
	                        </div>
	                        <? endif; ?>
	                        <div class="col-sm-12">
														<?php if (defined("B2BLOGGED")): ?>
															<input type="hidden" name="b2b" value="1">
															<div class="divider"></div>
															<div class="b2b-trans-info">
																* FIGYELEM! A szállítási költség és a pontos végösszeg a megrendelés után változhat. A szállítási költség függ a megrendelt tételek jellegétől (mennyiségtől, súlytól), hogy milyen módon és formában tudjuk biztonságosan kiszállítani! A szállítás pontos összegéről az összekészítés után történő visszaigazoláskor értesítjük.
															</div>
														<?php endif; ?>
	                        	<div class="divider"></div>
	                        	<? if(false): ?>
                       			<div class="left"><input type="checkbox" checked="checked" id="subscribe" name="subscribe" /><label for="subscribe">Felirakozok hírlevélre!</label></div>
                       			<? endif; ?>
															<?php if (defined("B2BLOGGED")): ?>
																<div class="left"><input type="checkbox" id="aszf_ok" name="aszf_ok"><label for="aszf_ok">Megrendelésemmel elfogadom a(z) <?=$this->settings['page_title']?> mindenkor hatályos <a href="/p/b2b-aszf" target="_blank">B2B Általános Szerződési Feltételek</a>et!</label></div>
															<?php else: ?>
																<div class="left"><input type="checkbox" id="aszf_ok" name="aszf_ok"><label for="aszf_ok">Megrendelésemmel elfogadom a(z) <?=$this->settings['page_title']?> mindenkor hatályos <a href="/p/aszf" target="_blank">Általános Szerződési Feltételek</a>et!</label></div>
															<?php endif; ?>
	                        </div>
	                    </div>
	                </div>
	                <!--/ORDER STEP 4.-->
	                <div class="orderFooter">
	                	<? if($this->gets[1] != '' && $this->gets[1] != '0'): ?>
	                    <? if($this->gets[1] < 4): ?>
	                	<a href="/kosar/<?=((int)$this->gets[1] - 1)?>" class="btn-back"><i class="fa fa-arrow-circle-left"></i> Vissza</a>
	                	<button name="orderState" value="next" class="btn-next">Tovább <i class="fa fa-arrow-circle-right"></i></button>
	                    <? else: ?>
	                    	<a href="/kosar/<?=((int)$this->gets[1] - 1)?>" class="btn-back"><i class="fa fa-arrow-circle-left"></i> Vissza</a>
	                        <? if($this->canOrder): ?>
	                        <input type="hidden" name="orderUserID" value="<?=$this->user[data][ID]?>" />
	                    	<button name="orderState" value="end" class="btn-order">MEGRENDELÉS <i class="fa fa-arrow-circle-right"></i></button>
	                        <? endif; ?>
	                    <? endif;?>
	                  <? endif;?>
	                </div>
	            </div>
	            <div class="clr"></div>
	        </div>
			</form>
	    	<? endif; ?>
    	</div>
    </div>
</div>

</div>
</div>
<script type="text/javascript">
	var selectedAtvetel = '<?=$this->storedString[2][atvetel]?>';

	$(function(){
		if(selectedAtvetel == "<?=$this->settings['flagkey_pickpacktransfer_id']?>"){
			$('.pickpackpont').css({
				display : 'block'
			});
			$('select[name=ppp_megye]').focus();
		}else if( selectedAtvetel == '5'){
			$('.postapont').css({
				display : 'block'
			});
		}else{
			$('.pickpackpont').css({
				display : 'none'
			});
			$('.pickpackpont .atvetelAdat').css({display:'none'});
			$('select[name=ppp_varos]').attr('disabled',true);
			$('select[name=ppp_uzlet]').attr('disabled',true);
		}

		$('.cart-adder').click(function(){
			$('button.mustReload').css({visibility:'visible'});
		});

		$('.col2').css({
			height : $('.col1').height()+'px'
		});


		$('#sameOfSzam').click(function(){
			var cls = $(this).is(':checked');

			if(cls){
				$('input[name^=szam_]').each(function(){
					var e = $(this).attr('name');
					$('input[name=szall_'+e.replace('szam_','')+']').val($(this).val());
				});
				var megye_id = $('#szam_state').val();

				$('#szall_state option:contains("'+megye_id+'")').prop('selected', true);
				console.log(megye_id);
			}else{

			}
		});

		$('input[type=radio][name=atvetel]').change(function(){
			var v = $(this).val();

			$('.pickpackpont').css({
				display : 'none'
			});
			$('.postapont').css({
				display : 'none'
			});

			switch(v){
				case '<?=$this->settings["flagkey_pickpacktransfer_id"]?>':
					$('.pickpackpont').css({
						display : 'block'
					});
					$('select[name=ppp_megye]').focus();
				break;
				case '5':
					$('.postapont').css({
						display : 'block'
					});
					ppapi.mapInitialize();
					ppapi.reloadPP();
				break;
				default:
					$('#valasztott_postapont').val('');
					$('#selected_pp_data_info').text('nincs kiválasztva');
					$('.pickpackpont .atvetelAdat').css({display:'none'});
					$('select[name=ppp_varos]').attr('disabled',true);
					$('select[name=ppp_uzlet]').attr('disabled',true);
				break;
			}
		});

		// Pick Pack Pont event
		function pppSelecting ( e ) {
			var data = jQuery.parseJSON( e.data );
			console.log(e.data);
			$('#ppp_uzlet_str').val(data.zipCode+" " +data.city+", "+data.address+" ("+data.shopType+")");
			$('#ppp_uzlet').val(data.pppShopname);
		}
		window.addEventListener( "message", pppSelecting, false );

	})
</script>
