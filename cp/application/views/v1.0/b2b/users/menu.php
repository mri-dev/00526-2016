<h1>B2B / Felhasználók <span><strong><?
  switch ($this->gets['2']) {
    case 'create':
      echo 'Új felhasználó létrehozása';
    break;
    case 'edit':
      echo 'Felhasználó szerkesztés';
    break;
    default:
      echo 'Felhasználók listázása';
    break;
  }
?></strong></span></h1>
<div class="tab-menu">
  <ul>
    <li class="base"><a href="/b2b/"><i class="fa fa-home"></i> Dashboard</a></li>
    <li class="<?=($this->gets['2'] == '' || is_numeric($this->gets['2']))?'on':''?>"><a href="/b2b/users">Felhasználók</a></li>
    <li class="<?=($this->gets['2'] == 'create')?'on':''?>"><a href="/b2b/users/create">Új felhasználó</a></li>
  </ul>
</div>
