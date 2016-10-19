<? $this->render('b2b/users/menu'); ?>
<div>
  <strong><?=$this->b2busers->listCurrentPage()?>. oldal</strong> / <?=$this->b2busers->listMaxPage()?> &nbsp; | &nbsp; <strong><?=$this->b2busers->totalUsers()?> felhasználó</strong>
</div>
<?=$this->navigator?>
<table class="table termeklista table-bordered b2b-users-table">
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
      <form class="" action="" method="post">
      <tr class="search <? if($_COOKIE[filtered] == '1'): ?>filtered<? endif;?>">
         <td><input type="text" name="ID" class="form-control" value="<?=$_COOKIE[filter_ID]?>" /></td>
         <td><input type="text" name="nev" class="form-control" placeholder="felhasználó neve..." value="<?=$_COOKIE[filter_nev]?>" /></td>
         <td><input type="text" name="email" class="form-control" placeholder="e-mail cím..." value="<?=$_COOKIE[filter_email]?>" /></td>
         <td></td>
         <td></td>
         <td></td>
         <td align="center">
            <button name="filterList" class="btn btn-default"><i class="fa fa-search"></i></button>
         </td>
      </tr>
      </form>
      <? if($this->users->count > 0): while( $this->users->walk() ): $u = $this->users->item(); ?>
      <tr>
         <td align="center"><?=$u->ID()?></td>
         <td>
            <div class="ceg"><?=$u->Name()?> <span class="addr" title="Cég telephely">(<?=$u->Address()?>)</span></div>
            <div class="contact">
              <span title="Kapcsolattartó neve"><i class="fa fa-user"></i> <?=$u->ContactName()?></span>
              <span title="Kapcsolat telefonszám"><i class="fa fa-phone"></i> <?=$u->ContactPhone()?></span>
            </div>
         </td>
         <td align="center"><?=$u->Email()?></td>
         <td align="center"><?=($u->Active())?'<i title="Engedélyezve" mode="engedelyezve" class="fa fa-check vtgl" fid="'.$u->ID().'"></i>':'<i mode="engedelyezve" class="fa fa-times vtgl" fid="'.$u->ID().'" title="Tiltva"></i>'?></td>
         <td align="center"><?=Helper::softDate($u->Lastlogin())?><br><em>(<?=Helper::distanceDate($u->Lastlogin())?>)</em></td>
         <td align="center"><?=Helper::softDate($u->CreatedAt())?> <br><em>(<?=Helper::distanceDate($u->CreatedAt())?>)</em></td>
         <td align="center">
           <a href="/b2b/users/edit/<?=$u->ID()?>" title="Szerkesztés"><i class="fa fa-gear"></i></a>
         </td>
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
<?=$this->navigator?>
