<div class="b2b-view login-page">
  <div class="page-width">
    <br>
    <?=$this->rmsg?>
    <div class="row">
      <div class="col-md-6">
        <div class="info-block">
          <div class="img">
            <img src="<?=IMG?>logo_800x_black.png" alt="Arena® sportáruház">
          </div>
          <div class="head">
            <h3>B2B Partnerprogram</h3>
          </div>
          <div class="desc">
            <p><strong>Az Arena Magyarország Kft. online nagykereskedelmi rendelési rendszere.</strong></p>
            <p>Amennyiben a B2B partnerprogram regisztrált felhasználója szeretne lenni, kérjük, vegye fel a kapcsolatot ügyfélszolgálatunkkal.</p>
            <a href="/p/kapcsolatfelvetel" class="btn btn-info">Kapcsolat</a>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="login-module">
          <h1>Partner bejelentkezés</h1>
          <form action="" method="post">
            <div class="formbox">
              <div class="row">
                <div class="col-md-12">
                  <label for="email">Azonosító</label>
                  <div class="loginname"><input autocomplete="off" type="text" id="email" name="email" class="form-control"></div>
                </div>
              </div>
              <br>
              <div class="row">
                <div class="col-md-12">
                  <label for="pw">Jelszó</label>
                  <div class="loginpw"><input autocomplete="off" type="password" id="pw" name="pw" class="form-control"></div>
                </div>
              </div>
              <br>
              <div class="row">
                <div class="col-md-12 right">
                  <button type="submit" class="btn btn-success" name="authB2B" value="1">Bejelentkezés <i class="fa fa-lock"></i></button>
                </div>
              </div>
            </div>
            <div class="login-footer">
              <div class="auth-info">
                <div class="ico"><i class="fa fa-shield"></i></div>
                <div class="text">
                  <strong>Biztonságos, 2 lépcsős bejelentkezés!</strong> <br>
                  Bejelentkezést követően e-mailben megküldött azonosító link segítségével tud rendszerünkhöz hozzáférni.
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
