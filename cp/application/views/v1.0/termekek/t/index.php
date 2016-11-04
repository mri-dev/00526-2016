<? if($this->gets[2] == 'del'): ?>
<form action="" method="post">
	<input type="hidden" name="delTermId" value="<?=$this->gets[3]?>" />
	<div class="row">
		<div class="col-md-12">
	    	<div class="panel panel-danger">
	        	<div class="panel-heading">
	            <h2><i class="fa fa-times"></i> Termék törlése</h2>
	            </div>
	        	<div class="panel-body">
	            	<div style="float:right;">
	                	<a href="/termekek/-/1" class="btn btn-danger"><i class="fa fa-times"></i> NEM</a>
	                    <button class="btn btn-success">IGEN <i class="fa fa-check"></i> </button>
	                </div>
	            	<strong>Biztos, hogy törli a terméket?</strong>
	            </div>
	        </div>
	    </div>
	</div>
</form>
<? elseif($this->gets[2] == 'delListingFromKat'):?>
<form action="" method="post">
	<input type="hidden" name="delKatItemID" value="<?=$this->gets[3]?>" />
	<div class="row">
		<div class="col-md-12">
	    	<div class="panel panel-danger">
	        	<div class="panel-heading">
	            <h2><i class="fa fa-times"></i> Termék listázás törlése</h2>
	            </div>
	        	<div class="panel-body">
	            	<div style="float:right;">
	                	<a href="/termekek/t/edit/<?=$this->gets[4]?>" class="btn btn-danger"><i class="fa fa-times"></i> NEM</a>
	                    <button class="btn btn-success">IGEN <i class="fa fa-check"></i> </button>
	                </div>
	            	<strong>Biztos, hogy törli a termék kategória listázását?</strong>
	            </div>
	        </div>
	    </div>
	</div>
</form>
<? elseif($this->gets[2] == 'imagesdelete'): ?>
<h1>Termék képek eltávolítása</h1>
<div class="con">
	<h3>A #<?=$this->gets[3]?> termék képei:</h3>
	<? if($this->imageset): ?>
	<br>
	<br>
	<form class="" action="" method="post">
	<? foreach($this->imageset as $image ): ?>
	<div class="row">
		<div class="col-md-12">
			<div style="width: 50px; height: 50px; text-align: center;float: left; ">
				<img src="<?=$image['view']?>" style="height: 100%;" alt="" />
			</div>
			<strong style="font-size: 15px; line-height: 2; color: black;"><?=$image['src']?></strong>
			<div class="">
				A szerveren: <strong><?=($image['exists'])?'<span style="color: green;">IGEN</span>':'<span style="color: red;">NEM</span>'?></strong>
			</div>
			<div class="clr"></div>
		</div>
	</div>
	<? if($image['exists']): ?>
	<input type="hidden" name="image_remove[]" value="<?=$image['src']?>">
	<? endif; ?>
	<div class="divider"></div>
	<br>
	<? endforeach; ?>
	<br>
	<div class="float: right;">
		<input type="hidden" name="id" value="<?=$this->gets[3]?>">
		<button type="submit" name="removeImagesOnTermek" class="btn btn-danger">Képek törlése a szerverről. Képhivatkozások lecsatolás a termékről.</button>
	</div>
	</form>
	<? else: ?>
	<strong style="color: green;">Nincsennek képek a termékhez csatolva.</strong>
	<? endif; ?>
</div>
<? elseif($this->gets[2] == 'edit'): ?>
<div style="float:right;">
	<a href="/termekek/-/1" class="btn btn-default btn-2x"><i class="fa fa-arrow-left"></i> mégse</a>
	<a href="/termekek/uj" class="btn btn-info"><i class="fa fa-plus"></i> új termék</a>
</div>
<h1>Termék szerkesztés</h1>
<?=$this->bmsg?>
<div class="clr"></div>
<div class="editForm create-product">
	<div class="row">
		<form action="" method="post" role="form">

		<div class="col-md-12 np">
			<div class="con">
				<div class="row np">
					<div class="col-md-12" align="right">
						<button class="btn btn-success btn-2x" name="saveTermek">Változások mentése <i class="fa fa-save"></i></button>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-8" style="padding-left:0;">
				<input type="hidden" name="tid" value="<?=$this->termek[ID]?>">
				<div class="con">
					<div class="row np checkprim">
						<div class="col-md-4">
							<label><input type="checkbox" name="lathato" <?=($this->termek['lathato'] == 1)?'checked':''?>/> Aktív / Látható</label>
					    </div>
						<div class="col-md-4">
							<label><input type="checkbox" name="akcios" id="akciosTgl" onclick="javascript:if($(this).is(':checked')){$('#vakcios').show(0);}else{$('#vakcios').hide(0);}" <?=($this->termek['akcios'] == 1)?'checked':''?>  /> Akciós</label>
					    </div>
						<div class="col-md-4">
							 <label><input type="checkbox" name="ujdonsag" <?=($this->termek['ujdonsag'] == 1)?'checked':''?>/> Újdonság</label>
						</div>
						<div class="col-md-4">
							<label><input type="checkbox" name="argep" <?=($this->termek['argep'] == 1)?'checked':''?>/> ÁRGÉP listába</label>
						</div>
						<div class="col-md-4">
							<label><input type="checkbox" name="arukereso" <?=($this->termek['arukereso'] == 1)?'checked':''?>/> ÁRUKERESŐ listába</label>
						</div>
						<div class="col-md-4">
							<label><input type="checkbox" <?=($this->termek['pickpackszallitas'] == 1)?'checked':''?> name="pickpackszallitas" /> Pick Pack Pont-ra szállítható</label>
						</div>
					</div>
				</div>

				<div class="con">
					<h3>Raktár adatok</h3>
					<div class="row">
						<div class="col-md-4">
							<label for="raktar_articleid">articleid</label>
							<input type="text" name="raktar_articleid" id="raktar_articleid" value="<?=$this->termek[raktar_articleid]?>" class="form-control">
						</div>
						<div class="col-md-4">
							<label for="raktar_variantid">variantid</label>
							<input type="text" name="raktar_variantid" id="raktar_variantid" value="<?=$this->termek[raktar_variantid]?>" class="form-control">
						</div>
						<div class="col-md-4">
							<label for="raktar_number">suppliertid</label>
							<input type="text" name="raktar_supplierid" id="raktar_supplierid" value="<?=$this->termek[raktar_supplierid]?>" class="form-control">
						</div>
						<div class="col-md-4">
							<label for="raktar_number">number</label>
							<input type="text" name="raktar_number" id="raktar_number" value="<?=$this->termek[raktar_number]?>" class="form-control">
						</div>
					</div>
				</div>

				<div class="con">
					<div class="row">
						<h3>Termék ár</h3>
						<div class="col-md-6">
							<div>
								<label for="ar_by">Eredeti ár</label>
								<select name="ar_by" id="ar_by" class="form-control">
									<option value="">-- válasszon: módosítás mint --</option>
									<option value="netto">Nettó ár</option>
									<option value="brutto">Bruttó ár</option>
								</select>
								<br />
								<div class="input-group col-md-12">
									<input type="text" name="netto_ar" value="<?=$this->termek[netto_ar]?>" class="form-control">
									<span class="input-group-addon">nettó ár</span>
								</div>
								<br />
								<div class="input-group col-md-12">
									<input type="text" name="brutto_ar" value="<?=$this->termek[brutto_ar]?>" class="form-control">
									<span class="input-group-addon">bruttó ár</span>
									<span class="input-group-addon">
									= <strong title="Fogyasztói ár"><?=Helper::cashFormat($this->termek[ar])?> Ft</strong>
									(<?=number_format(($this->termek[ar] - $this->termek[brutto_ar]) / ($this->termek[ar] / 100),2,"."," ")?>%)
									</span>
								</div>

							</div>
						</div>
						<div class="col-md-6">
							<div>
								<label for="akcios_ar_by">Akciós ár</label>
								<select name="akcios_ar_by" class="form-control">
									<option value="">-- válasszon: módosítás mint --</option>
									<option value="netto">Nettó ár</option>
									<option value="brutto">Bruttó ár</option>
								</select>
								<br />
								<div class="input-group col-md-12">
									<input type="text" name="akcios_netto_ar" value="<?=$this->termek[akcios_netto_ar]?>" class="form-control">
									<span class="input-group-addon">nettó ár</span>
								</div>
								<br />
								<div class="input-group col-md-12">
									<input type="text" name="akcios_brutto_ar" value="<?=$this->termek[akcios_brutto_ar]?>" class="form-control">
									<span class="input-group-addon">bruttó ár</span>
									<span class="input-group-addon">
									= <strong title="Fogyasztói ár"><?=Helper::cashFormat($this->termek[akcios_ar])?> Ft</strong>
									(<?=number_format(($this->termek[akcios_ar] - $this->termek[akcios_brutto_ar]) / ($this->termek[akcios_ar] / 100),2,"."," ")?>%)
									</span>
								</div>
							</div>
						</div>
					</div>
					<br />
				</div>
				<div class="con">
					<h3>Alapadatok</h3>
					<div class="row">
						<div class="form-group col-md-3">
							<label for="cikkszam">Nagyker kód / Cikkszám</label>
							<input class="form-control" id="cikkszam" type="text" value="<?=$this->termek['cikkszam']?>"  name="cikkszam">
						</div>
						<div class="form-group col-md-6">
							<label for="nev">Termék neve*</label>
							<input type="text" class="form-control" name="nev" id="nev" value="<?=$this->termek[nev]?>">
						</div>
						<div class="form-group col-md-3">
							<label for="nev">Termék márka*</label>
							<select name="marka" id="marka" class="form-control">
								<option value="">-- termék márka kiválasztása --</option>
								<option value="" disabled></option>
								<? foreach($this->markak as $d): ?>
								<option value="<?=$d[ID]?>" <?=($this->termek[marka] == $d[ID])?'selected':''?> nb="<?=$d[brutto]?>"><?=$d[neve]?> (<?=($d[brutto] == '1')?'Bruttó':'Nettó'?>)</option>
								<? endforeach; ?>
							</select>
						</div>
					</div>

					<div class="row">
						<div class="form-group col-md-3">
							<label for="meret">Méret</label>
							<input type="text" class="form-control" name="meret" id="meret" value="<?=$this->termek['meret']?>">
						</div>
						<div class="form-group col-md-3">
							<label for="szin">Szín</label>
							<input type="text" class="form-control" name="szin" id="szin" value="<?=$this->termek['szin']?>">
						</div>
						<div class="form-group col-md-3">
							<label for="raktar_keszlet">Raktárkészlet</label>
							<input type="number" class="form-control" name="raktar_keszlet" value="<?=$this->termek['raktar_keszlet']?>" id="raktar_keszlet">
						</div>
						<div class="form-group col-md-2">
							<label for="fotermek">Főtermék <?=\PortalManager\Formater::tooltip('Több szín és méret esetén kijelölhetjük, hogy melyik legyen az alapértelmezett, ami megjelenjen a terméklistázásban. A Főtermék-nek NEM jelölt termékek nem fognak megjelenni a listában, hanem csak mint variáció a kapcsolódó terméklapon!')?></label>
							<input type="checkbox" class="form-control" name="fotermek" id="fotermek" <?=($this->termek && $this->termek['fotermek'] == 1)?'checked="checked"':''?>>
						</div>
					</div>

					<div class="row">
						<div class="form-group col-md-12">
							<label for="kulcsszavak">Kulcsszavak: <?=\PortalManager\Formater::tooltip('A kulcsszavak meghatározása fontos dolog, mivel ezek alapján tud pontosabb keresési találatot kapni a felhasználó. <br> <strong>A kulcsszavakat szóközzel elválasztva adja meg. Pl.: fekete úszó rövidnadrág</strong>')?></label>
							<input type="text" class="form-control" name="kulcsszavak" id="kulcsszavak" value="<?=$this->termek['kulcsszavak']?>">
						</div>
					</div>

					<div class="row">
						<div class="form-group col-md-12">
							<label for="rovid_leiras">Termék rövid ismeretető</label>
							<textarea name="rovid_leiras" class="form-control" id="rovid_leiras"><?=$this->termek['rovid_leiras']?></textarea>
						</div>
					</div>

					<div class="row">
						<div class="form-group col-md-12">
							<label for="leiras">Termék részletes leírása</label>
							<textarea name="leiras" class="form-control" id="leiras"><?=$this->termek['leiras']?></textarea>
						</div>
					</div>
				</div>
				<div class="con">
					<h3>Tulajdonságok</h3>
					<div class="row np">
						<div class="col-md-12">
							<div class="form-group col-md-6">
								<label for="szall">Szállítási idő*</label>
								<select name="szallitasID" id="szall" class="form-control">
									<option value="">-- válasszon --</option>
									<option value="" disabled="disabled"></option>
									<? foreach($this->szallitas as $sz): ?>
									<option value="<?=$sz[ID]?>" <?=($this->termek['szallitasID'] == $sz[ID])?'selected':''?>><?=$sz['elnevezes']?></option>
									<? endforeach; ?>
								</select>
							</div>
							<div class="form-group col-md-6 ">
								<label for="keszlet">Állapot*</label>
								<select name="keszletID" id="keszlet" class="form-control">
									<option value="">-- válasszon --</option>
									<option value="" disabled="disabled"></option>
									<? foreach($this->keszlet as $k): ?>
									<option value="<?=$k['ID']?>" <?=($this->termek['keszletID'] == $k[ID])?'selected':''?>><?=$k['elnevezes']?></option>
									<? endforeach; ?>
								</select>
							</div>
						</div>
					</div>
					<!--
					<div class="row">
						<div class="form-group col-md-12">
							<label for="garancia">Garancia (hónap; -1 = élettartam)</label>
							<input class="form-control" type="number" id="garancia" value="<?=$this->termek['garancia_honap']?>" min="-1" max="100" name="garancia">
						</div>
					</div>
				-->
				</div>
				<div class="con">
					<h3>Csatolt hivatkozások</h3>
					<div class="row">
						<div class="col-md-12">
							<div class="alink">
								<? $linkek = Product::attackedLink($this->termek[linkek]); ?>
								<? $k = 0; foreach($linkek as $lk): $k++; ?>
								<div class="row np link">
									<div class="col-md-1"><?=$k?>#</div>
									<div class="col-md-4"><input type="text" name="linkNev[]" class="form-control" value="<?=$lk[nev]?>" placeholder="Felirat" /></div>
									<div class="col-md-7"><input type="text" name="linkUrl[]" class="form-control" value="<?=$lk[url]?>" placeholder="URL"/></div>
								</div>
								<br />
								<? endforeach; ?>
								<div class="row np link">
									<div class="col-md-1">új</div>
									<div class="col-md-4"><input type="text" name="linkNev[]" class="form-control" value="" placeholder="Felirat" /></div>
									<div class="col-md-7"><input type="text" name="linkUrl[]" class="form-control" value="" placeholder="URL"/></div>
								</div>
								<div class="row np" style="margin-top:8px;">
									<div class="col-md-6">
										<a href="javascript:void(0);" id="addMoreLink"><i class="fa fa-plus"></i> új hivatkozás hozzáadása</a>
									</div>
									<div class="col-md-6 right">
										<a href="javascript:void(0);" onclick="$('#linksetstr').slideToggle(400);" style="font-size:0.85em; color:red;" id="addMoreLink">hivatkozás nyers adat mutatása/másolás <i class="fa fa-question"></i></a>
									</div>
								</div>
								<div class="row np" id="linksetstr" style="display:none;">
									<input type="text" onclick="$(this).select();" class="form-control" value="<?=$this->termek[linkek]?>">
								</div>
							</div>
						</div>
					</div>
				</div>
	    </div>

	    <div class="col-md-4"  style="padding-right:0;">
	    	<? if( false ): ?>
	    	<div class="con">
	        	<h3>Alapértelmezett kategória <?=\PortalManager\Formater::tooltip('Válasszuk ki az alapértelmezett termék kategóriát. A kiválasztott alapértelmezett kategória lesz a terméknél a hivatkozó kategória. Pl.: termék alaplapon, egyéb termék ajánlatok')?> <em class="info">A termék elsődleges, alapértelmezett kategóriája.</em></h3>
	            <div class="row" style="">
					<div class="col-md-12">
						<select class="form-control" name="alapertelmezett_kategoria">
							<option value="">-- válasszon --</option>
							<option value="" disabled="disabled"></option>
							<? if( count($this->termek['in_cat_ids']) > 0 ): foreach ( $this->termek['in_cat_ids'] as $key => $kids ) { ?>
								<option value="<?=$kids?>" <?=($kids == $this->termek['alapertelmezett_kategoria']) ? 'selected="selected"': ''?>><?=$this->termek['in_cat_names'][$key]?></option>
							<? } endif;  ?>

						</select>
					</div>
	            </div>
	        </div>
	    	<? endif; ?>


            <div class="con" style="display:none;">
				<h3>Kategória, amibe megjelenjen (<?=count($this->termek['in_cat_ids'])?>) <em class="info"><a href="/kategoriak" target="_blank"><i class="fa fa-gear"></i> kategóriák szerkesztése</a></em></h3>
				<div style="padding:0 0 15px 15px;">
					<div class="tree overflowed">
						<? while( $this->categories->walk() ): $item = $this->categories->the_cat(); ?>
						<div class="item deep<?=$item['deep']?>">
							<label><input name="cat[]" value="<?=$item['ID']?>" type="checkbox" <?=(in_array($item['ID'], $this->termek['in_cat_ids']))?'checked="checked"':''?>><?=$item['neve']?></label>
						</div>
						<? endwhile; ?>
					</div>
				</div>
			</div>


	</form>

            <div class="con nb">
				<h3>
					<i class="fa fa-upload hbtn" title="új kép feltöltése" key="upImg"></i>
					Képek (<?=count($this->termek[images])?>)
					<em class="info">A képre kattintva lecserélheti a profilképet!</em>
				</h3>
                <div class="row">
                	<div class="col-md-12 upImg" style="display:none;">
                		<div class="newWire">
							<form action="" method="post" enctype="multipart/form-data">
								<input type="hidden" name="dir" value="<?=$this->termek['kep_mappa']?>">
								<input type="hidden" name="tid" value="<?=$this->termek['ID']?>">
								<button style="float:right;" name="uploadImg">feltöltés</button>
								<input type="file" name="img[]" multiple />
								<div class="clr"></div>
							</form>
                		</div>
                	</div>
                </div>

				<div class="row">
					<div class="col-md-12">
		                <div class="images">
		                	<? foreach($this->termek['images'] as $i): ?>
		                    	<div del="0" class="item <?=( \PortalManager\Formater::productImage($i) == $this->termek['profil_kep'])?'main':''?>"><img isrc="<?=$i?>" src="<?=\PortalManager\Formater::productImage($i)?>" alt=""></div>
		                    <? endforeach; ?>
		                    <div class="clr"></div>
		                </div>
					</div>
				</div>
        <div class="row">
        	<div class="col-md-12 right">
        		<span class="delimgmode label label-danger" title="Bepipálva a képkre kattintva törölhető a termék kép!">képtörlő mód <input type="checkbox" id="imgDelMode" /></span>
        	</div>
        </div>
				<div class="divider" style="margin: 10px 0;"></div>
				<div class="row">
        	<div class="col-md-12 right">
        		<a href="/termekek/t/imagesdelete/<?=$this->termek['ID']?>" class="btn btn-danger btn-sm">Képek végleges lecsatolása, törlése a szerverről.</a>
        	</div>
        </div>
				<br>
	        </div>

			<? if( false ): ?>
	        <div class="con nb">
            	<h3>Termék másolat <?=\PortalManager\Formater::tooltip('Javasolt termék variációhoz, ahol a termék adatai nagy részében megegyeznek.')?> <em class="info">Lemásolhatja tetszőletes számban a terméket</em></h3>
                <div class="row" style="">
					<div class="col-md-12">
						<?=$this->copyMsg?>
						<form action="" method="post" role="form">
							<input type="hidden" name="tid" value="<?=$this->termek[ID]?>" />
							<div class="input-group">
								<input type="number" class="form-control" min="0" value="0" name="copyNum" />
								<span class="input-group-addon">darab</span>
								<span class="input-group-btn"><button class="btn btn-danger" name="copyTermek">másolás</button></span>
							</div>
						</form>
					</div>
                </div>
            </div>
       		<? endif; ?>

			<? if( false ): ?>
	        <div class="con nb">
            	<h3>Termék kapcsolatok <em class="info">Azonos termékek összekapcsolása, mint termék variáció</em></h3>
               	<div class="row">
               		<div class="col-md-12">
               			<label for="productRelativesText">Keresés</label>
               			<input type="text" id="productRelativesText" exc-id="<?=$this->termek['ID']?>" value="<?=$this->termek['nev']?>" class="form-control">
               		</div>
               	</div>
               	<br>
               	<div class="row">
               		<div class="col-md-12">
               			<label for="">Lehetséges termékek (<span id="productRelativesNumber">0</span>)</label>
               			<div class="productRelativesList" id="productRelativesList"></div>
               		</div>
               	</div>

               	<div class="row">
               		<div class="col-md-12">
               			<label for="">Kapcsolt termékek (<?=(count($this->termek['hasonlo_termek_ids']))?>)</label>
               			<div><?=$this->kapcsolatok?></div>
               		</div>
               	</div>
            </div>
        	<? endif; ?>

	    </div>
	</div>

<script type="text/javascript">
	$(function(){
		// Termék ajánlások becsatoláshoz
		//loadProductRelatives();

		$('#productRelativesText').bind( 'change keyup', function(){
			loadProductRelatives();
		} );

		$('.modkat i').click(function(){
			$('.modkat .shinkat').hide(0);
			$('.modkat i').removeClass('showed');
			var key = $(this).attr('key');
			var sh 	= $(this).attr('sh');


			if(sh == 0){
				$('.modkat #inkatid'+key).show(0).html('<div style="padding:10px; text-align:center;"><i class="fa fa-spinner fa-spin"></i> betöltés...</div>');
				$(this).attr('sh',1);
				$(this).addClass('showed');
				loadKatValaszto(key);
			}else{
				$('.modkat #inkatid'+key).hide(0);
				$(this).attr('sh',0);
			}
		});

		$('.images .item').click(function(e){
			var del = $(this).attr('del');
			$('.images .item').removeClass('main');

			if(del == '0'){
				setMainImage($(this));
			}else{
				delImage($(this));
			}
		});

		$('#imgDelMode').bind('change',function(){
			var ch = $(this).is(':checked');

			if(ch){
				$('.images .item').attr('del','1');
			}else{
				$('.images .item').attr('del','0');
			}
		});

		$('#addMoreGyujtkat').click(function(){
			addNewLister();
		});
		$('#addMoreLink').click(function(){
			addNewLinkRow();
		});
	})

	var newKat = 0;
	function loadKatValaszto(tid){
		$.post("<?=AJAX_GET?>",{
			type : 'loadCheckKat',
			id 	: tid,
		},function(d){
			$('.modkat #inkatid'+tid).html(d);
		},"html");
	}
	function addNewLister(){
		newKat++;
		$('.inkat .item:last').after('<div class="item new"><div class="selModszer i'+newKat+'"></div><div class="selGyujto i'+newKat+'"></div></div>');
		loadModszerek();
	}
	function loadProductRelatives ( callback ) {
		var handler = $('#productRelativesText');
		var excid = handler.attr('exc-id');
		var srctext = handler.val();

		$('#productRelativesList').html( '<i class="fa fa-spinner fa-spin"></i> betöltés...' );

		$.post("<?=AJAX_POST?>",{
			type 	: 'loadProducts',
			by  	: 'nev',
			val 	: srctext,
			template : 'relatives',
			fromid 	: excid,
			mode 	: 'json'
		},function(d){
			var ret = jQuery.parseJSON(d);
			$('#productRelativesList').html( ret.result );
			$('#productRelativesNumber').text( ret.info.results );
			callback();
		},"html");
	}

	function connectProductRelatives( e, foid, tid ) {
		$.post("<?=AJAX_POST?>",{
			type 	: 'addProductConnects',
			idfrom  : foid,
			idto 	: tid
		},function(d){
			loadProductRelatives();
		},"html");
	}

	function removeProductRelatives( e, foid, tid ) {
		var rtarget = $('.product-li-items.mode-remove').find('li.item.item_'+foid+"_"+tid);

		rtarget.css({ opacity: 0.5 });
		rtarget.find('button').removeClass('btn-danger').addClass('btn-success');
		rtarget.find('i').removeClass('fa-minus-circle').addClass('fa-spinner fa-spin');

		$.post("<?=AJAX_POST?>",{
			type 	: 'removeProductConnects',
			idfrom  : foid,
			idto 	: tid
		},function(d){
			loadProductRelatives( function(){
				rtarget.remove();
			} );
		},"html");
	}

	function addNewLinkRow(){
		var e ='<br />'+
			'<div class="row np link">'+
				'<div class="col-md-1"><em>új</em></div>'+
				'<div class="col-md-4"><input type="text" name="linkNev[]" class="form-control" value=""  placeholder="Felirat"/></div>'+
			   ' <div class="col-md-7"><input type="text" name="linkUrl[]" class="form-control" value="" placeholder="URL"/></div>'+
			'</div>';

		$(e).insertAfter('.alink .link:last');
	}

	function delImage(e){
		e.addClass('del');
		var c = confirm('Biztos, hogy törli a képet?');

		if(c){
			$.post('<?=AJAX_POST?>',{
				type : 'termekChangeActions',
				mode : 'delTermekImg',
				tid  : '<?=$this->termek[ID]?>',
				i : e.find('img').attr('isrc')
			},function(d){
				e.remove();
			},"html");

		}else{
			e.removeClass('del');
		}
	}

	function loadModszerek(){
		$.post('/ajax/get/',{
			type : 'loadModszerek',
			i : newKat
		},function(d){
			$('.item .selModszer.i'+newKat).html(d);
		},"html");
	}

	function setMainImage(e){
		var img = e.find('img').attr('isrc');
		e.addClass('prog');
		$.post("<?=AJAX_POST?>",{
			type 	: 'termekChangeActions',
			mode 	: 'changeTermekKep',
			id 		: '<?=$this->termek[ID]?>',
			i 		: img
		},function(d){
			e.removeClass('prog').addClass('main');
		},"html");
	}
</script>
<? endif; ?>
