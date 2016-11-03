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

    $missed_details = array();
    if( isset($_GET['missed_details']) ) {
        $missed_details = explode(",",$_GET['missed_details']);
    }
?>
<div class="account page-width">
    <? $this->render('user/inc/account-side', true); ?>
    <div class="responsive-view">
        <? if( count( $missed_details ) > 0 ): ?>
            <?=Helper::makeAlertMsg('pError', 'Az Ön adatai hiányosak. Mielőtt bármit is tenne, kérjük, hogy pótolja ezeket!' );?>
        <? endif; ?>
        <h1>Beállítások</h1>
        <?=$this->msg['alapadat']?>
        <div class="form-rows">
            <form action="#alapadat" method="post">
                <div class="row np">
                    <div class="col-md-3"><strong>E-mail cím:</strong></div>
                    <div class="col-md-9"><?=$this->user[email]?></div>
                </div>
                <div class="row np">
                    <div class="col-md-3 form-text-md"><strong>Név</strong></div>
                    <div class="col-md-5"><input name="nev" type="text" class="form-control" value="<?=$this->user[data][nev]?>" /></div>
                </div>
                <div class="row np">
                    <div class="col-md-3"><strong>Utoljára belépve</strong></div>
                    <div class="col-md-5"><?=$this->user[data][utoljara_belepett]?> (<?=Helper::distanceDate($this->user[data][utoljara_belepett])?>)</div>
                </div>
                <div class="row np">
                    <div class="col-md-3"><strong>Regisztráció</strong></div>
                    <div class="col-md-5"><?=$this->user[data][regisztralt]?> (<?=Helper::distanceDate($this->user[data][regisztralt])?>)</div>
                </div>
                <div class="row np">
                    <div class="col-md-12">
                        KEDVEZMÉNYEK
                    </div>
                </div>
                <? foreach( $this->user['kedvezmenyek'] as $kedv ): ?>
                <div class="row np">
                    <div class="col-md-3"><strong><?=$kedv['nev']?></strong></div>
                    <div class="col-md-5"><a href="<?=$kedv['link']?>" title="részletek"><?=$kedv['kedvezmeny']?>%</a> <? if($kedv['nev'] === 'Arena Water Card' && $kedv['kedvezmeny'] === 0): ?> <a href="javascript:void(0);" onclick="$('#add-watercard').slideToggle(400);" class="add-water-card">kártya regisztrálása</a> <? endif; ?> </div>
                </div>
                <? if($kedv['nev'] === 'Arena Water Card'): ?>
                <div class="row np" id="add-watercard" style="display:none;">
                    <div class="col-md-12 watercard" style="padding: 8px;">
                        <h3>ARENA WATER CARD <span style="color:#333;">// Kártya regisztrálása</span></h3>
                        <input type="hidden" name="watercard[userid]" value="<?=$this->user[data][ID]?>">
                        <input type="hidden" name="watercard[email]" value="<?=$this->user[email]?>">
                        <div class="info">A kedvezmény feltételeiről bővebben <a href="/p/arena_water_card" target="_blank">itt olvashat</a>!</div>
                        <? if( !$this->user[arena_water_card][registered] ): ?>
                        <div class="watercard-card-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="wc-data-id">Kártya száma</label>
                                    <input name="watercard[id]" class="form-control" type="text">
                                </div>
                                <div class="col-md-6">
                                    <label for="wc-data-id">Egyesület</label>
                                    <input name="watercard[egyesulet]" class="form-control" type="text">
                                </div>
                            </div>

                        </div>
                        <div style="float:right; margin-top:10px;">
                            <button class="btn btn-success" name="addWatercard">Kártya regisztrálása</button>
                        </div>
                        <? else: ?>
                        <div class="watercard-card-data">
                            <div class="row">
                                <div class="col-md-12">
                                    <strong>Kártya: </strong><u><?=$this->user[arena_water_card][data][kartya_szam]?></u> &mdash; Kártyája aktiválása folyamatban van. Kis türelmét kérjük!
                                </div>
                            </div>
                        </div>
                        <? endif; ?>
                    </div>
                </div>
                <? endif; ?>
                <? endforeach; ?>
                <div class="row np">
                    <div class="col-md-12" align="left"><button name="saveDefault" class="btn btn-info btn-sm"><i class="fa fa-save"></i> Változások mentése</button></div>

                </div>
            </form>
        </div>

        <div class="divider"></div>
        <? if( isset( $_GET['missed_details']) && in_array( 'szallitasi', $missed_details) ): ?>
            <?=Helper::makeAlertMsg('pWarning', '<BR><strong>HIÁNYZÓ ADAT:</strong><BR>Kérjük, hogy pótolja a hiányzó SZÁLLÍTÁSI adatait.' );?>
        <? endif; ?>
        <? if( isset( $_GET['missed_details']) && in_array( 'szallitasirefill', $missed_details) ): ?>
            <?php
                $errmsg = '<BR><strong>TISZTELT FELHASZNÁLÓNK!</strong><BR>A pontosabb szállítás érdekében kibővítettük a szállítási adatok megadására szolgáló mezőket. Kérjük, hogy értelemszerűen és pontosan töltse ki az alábbi mezőket.<br>A "Közterület neve" mezőben ténylegesen csak a közterület neve szerepeljen, a közterület jellege és a házszám NE!<br><br><strong>Kötelező mezők hiányoznak:</strong><br>';

                if (empty($this->user[szallitasi_adat]['kozterulet_jellege'])){
                    $errmsg .= '<i class="fa fa-times"></i> közterület jellege (pl.: utca, út, tér, stb...) <br>';
                }

                if (empty($this->user[szallitasi_adat]['hazszam'])){
                    $errmsg .= '<i class="fa fa-times"></i> házszám<br>';
                }
            ?>
            <?=Helper::makeAlertMsg('pWarning', $errmsg)?>
        <? endif; ?>
        <h4>Szállítási adatok</h4>
        <em>Azokat a mezőket hagyja üresen, ami nem jellemző az Ön adataira.</em>
        <?=$this->msg['szallitasi']?>
        <div class="form-rows">
            <form action="#szallitasi" method="post">
            <? foreach($szmnev as $dk => $dv):
                $val = ($this->user[szallitasi_adat]) ? $this->user[szallitasi_adat][$dk] : '';
            ?>
            <div class="row np">
                <div class="col-md-3 form-text-md"><strong><?=$szmnev[$dk]?></strong></div>
                <div class="col-md-5">
                   <? if($dk == 'state'): ?>
                    <select name="<?=$dk?>" class="form-control" id="szall_state">
                        <? foreach( $this->states as $s ): ?>
                            <option value="<?=$s?>" <?=($val == $s) ? 'selected="selected"' : ''?>><?=$s?></option>
                        <? endforeach; ?>
                    </select>
                    <? elseif($dk == 'kozterulet_jellege'): ?>
                    <select name="<?=$dk?>" class="form-control" id="szall_state">
                        <? foreach( $this->kozterulet_jellege as $kj ): ?>
                            <option value="<?=$kj?>" <?=($val == $kj) ? 'selected="selected"' : ''?>><?=$kj?></option>
                        <? endforeach; ?>
                    </select>
                    <? else: ?>
                    <input name="<?=$dk?>" type="text" class="form-control" value="<?=$val?>" />
                    <? if(!empty($this->buyer_inputs_hints[$dk])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints[$dk]?></div>  <? endif; ?>
                    <? endif; ?>
                </div>
            </div>
            <? endforeach; ?>
            <div class="row np">
                <div class="col-md-12" align="left"><button name="saveSzallitasi" class="btn btn-info btn-sm"><i class="fa fa-save"></i> Változások mentése</button></div>

            </div>
            </form>
        </div>

        <div class="divider"></div>
        <? if( isset( $_GET['missed_details']) && in_array( 'szamlazasi', $missed_details) ): ?>
            <?=Helper::makeAlertMsg('pWarning', '<BR><strong>HIÁNYZÓ ADAT:</strong><BR>Kérjük, hogy pótolja a hiányzó SZÁMLÁZÁSI adatait.' );?>
        <? endif; ?>

        <? if( isset( $_GET['missed_details']) && in_array( 'szamlazasirefill', $missed_details) ): ?>
            <?php
                $errmsg = '<BR><strong>TISZTELT FELHASZNÁLÓNK!</strong><BR>A pontosabb számlázás érdekében kibővítettük a számlázási adatok megadására szolgáló mezőket. Kérjük, hogy értelemszerűen és pontosan töltse ki az alábbi mezőket.<br>A "Közterület neve" mezőben ténylegesen csak a közterület neve szerepeljen, a közterület jellege és a házszám NE!<br><br><strong>Kötelező mezők hiányoznak:</strong><br>';

                if (empty($this->user[szamlazasi_adat]['kozterulet_jellege'])){
                    $errmsg .= '<i class="fa fa-times"></i> közterület jellege (pl.: utca, út, tér, stb...)  <br>';
                }

                if (empty($this->user[szamlazasi_adat]['hazszam'])){
                    $errmsg .= '<i class="fa fa-times"></i> házszám<br>';
                }
            ?>

            <?=Helper::makeAlertMsg('pWarning', $errmsg)?>
        <? endif; ?>
        <h4>Számlázási adatok</h4>
        <em>Azokat a mezőket hagyja üresen, ami nem jellemző az Ön adataira.</em>
        <?=$this->msg['szamlazasi']?>
        <div class="form-rows">
            <form action="#szamlazasi" method="post">
            <? foreach($szmnev  as $dk => $dv):
             $val = ($this->user[szamlazasi_adat]) ? $this->user[szamlazasi_adat][$dk] : '';
            ?>
            <div class="row np">
                <div class="col-md-3 form-text-md"><strong><?=$szmnev[$dk]?></strong></div>
                <div class="col-md-5">
                    <? if($dk == 'state'): ?>
                    <select name="<?=$dk?>" class="form-control" id="szall_state">
                        <? foreach( $this->states as $s ): ?>
                            <option value="<?=$s?>" <?=($val == $s) ? 'selected="selected"' : ''?>><?=$s?></option>
                        <? endforeach; ?>
                    </select>
                    <? elseif($dk == 'kozterulet_jellege'): ?>
                    <select name="<?=$dk?>" class="form-control" id="szall_state">
                        <? foreach( $this->kozterulet_jellege as $kj ): ?>
                            <option value="<?=$kj?>" <?=($val == $kj) ? 'selected="selected"' : ''?>><?=$kj?></option>
                        <? endforeach; ?>
                    </select>
                    <? else: ?>
                    <input name="<?=$dk?>" type="text" class="form-control" value="<?=$val?>" />
                    <? if(!empty($this->buyer_inputs_hints[$dk])): ?> <div class="text-hint"><?=$this->buyer_inputs_hints[$dk]?></div>  <? endif; ?>
                    <? endif; ?>
                </div>
            </div>
            <? endforeach; ?>
            <div class="row np">
                <div class="col-md-12" align="left"><button name="saveSzamlazasi" class="btn btn-info btn-sm"><i class="fa fa-save"></i> Változások mentése</button></div>

            </div>
            </form>
        </div>

    </div>
</div>
