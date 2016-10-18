<? $this->render('b2b/users/menu'); ?>
<table class="table termeklista table-bordered">
   <thead>
      <tr>
         <th title="Felhasználó ID" width="40">#</th>
         <th>Partner</th>
         <th width="200">E-mail</th>
         <th width="100">Engedélyezve</th>
         <th width="120">Utoljára belépett</th>
         <th width="120">Létrehozva</th>
         <th width="20"></th>
      </tr>
   </thead>
   <tbody>
      <tr class="search <? if($_COOKIE[filtered] == '1'): ?>filtered<? endif;?>">
         <td><input type="text" name="ID" class="form-control" value="<?=$_COOKIE[filter_ID]?>" /></td>
         <td><input type="text" name="nev" class="form-control" placeholder="felhasználó neve..." value="<?=$_COOKIE[filter_nev]?>" /></td>
         <td><input type="text" name="email" class="form-control" placeholder="e-mail cím..." value="<?=$_COOKIE[filter_email]?>" /></td>
         <td></td>
         <td>
            <select class="form-control"  name="engedelyezve" style="max-width:100px;">
               <option value="" <?=(!$_COOKIE[filter_engedelyezve])?'selected':''?>># Mind</option>
               <option value="0" <?=($_COOKIE[filter_engedelyezve] == '0')?'selected':''?>>Nem</option>
               <option value="1" <?=($_COOKIE[filter_engedelyezve] == '1')?'selected':''?>>Igen</option>
            </select>
         </td>
         <td>
            <select class="form-control"  name="aktivalva" style="max-width:100px;">
               <option value="" selected># Mind</option>
               <option value="0" <?=($_COOKIE[filter_aktivalva] == '0')?'selected':''?>>Nem</option>
               <option value="1" <?=($_COOKIE[filter_aktivalva] == '1')?'selected':''?>>Igen</option>
            </select>
         </td>
         <td align="center">
            <button name="filterList" class="btn btn-default"><i class="fa fa-search"></i></button>
         </td>
      </tr>
      <? if($this->users->count > 0): while( $this->users->walk() ): $user = $this->users->item(); ?>
      <tr>
         <td align="center"><?=$d[ID]?></td>
         <td>
            <strong><?=$d[nev]?></strong>
         </td>
         <td align="center"><?=$d[email]?></td>
         <td align="center"><?=($d[engedelyezve] == 1)?'<i title="Engedélyezve" mode="engedelyezve" class="fa fa-check vtgl" fid="'.$d[ID].'"></i>':'<i mode="engedelyezve" class="fa fa-times vtgl" fid="'.$d[ID].'" title="Tiltva"></i>'?></td>
         <td align="center"><?=Helper::softDate($d[utoljara_belepett])?>	<br><em>(<?=Helper::distanceDate($d[utoljara_belepett])?>)</em></td>
         <td align="center"><?=Helper::softDate($d[regisztralt])?> <br><em>(<?=Helper::distanceDate($d[regisztralt])?>)</em></td>
         <td></td>
      </tr>
      <? endwhile; else: ?>
      <tr>
         <td colspan="15" align="center">
            <div style="padding:25px;">Nincs találat!</div>
         </td>
      </tr>
      <? endif; ?>
   </tbody>
</table>
