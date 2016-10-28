<? require "head.php"; ?>
<h1>Tisztelt <?=$user->Name()?>!</h1>
<strong>Ön sikersen elindította a bejelentkezési folyamatot weboldalunkon.</strong>
<br><br>
<div style="text-align:center;">
  <div style="height: 1px; background-color: #888888; width: 50%; margin: 15px auto;"></div>
  <h3>Bejelentkezési URL</h3>
  <div style="padding: 20px; margin: 10px auto; width: 80%; font-size: 16px; font-weight: bold; background-color: #f5f5f5; border: 1px solid #eaeaea; border-radius: 25px;">
    <a href="<?=$loginurl?>" style="color: #0ac8ff;"><?=$loginurl?></a>
  </div>
  <small style="font-size: 12px; color: #9c9c9c; margin: 10px; 0"><em>A hivatkozás az azonosító generálásától számítva <?=\B2B\B2BAuth::VALIDETOSEC?> percig érvényes. Utána nem tud bejelentkezni ezzel a hivatkozással.</em></small>
  <div style="height: 1px; background-color: #888888; width: 50%; margin: 15px auto;"></div>
</div>
<br>
Amennyiben nem Ön vagy megbízottja indította el a bejelentkezési folyamatot, jelezze ügyfélszolgálatunknak.
<br><br>
--<br>
Azonosító érvényességi ideje: <strong><?=date('Y / m / d H:i', $ervenyes)?></strong><br>
Azonosító generálás ideje: <strong><?=date('Y / m / d H:i', strtotime(NOW))?></strong>
<? require "footer.php"; ?>
