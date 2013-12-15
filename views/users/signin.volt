<div id="title" class="dark">Accedi</a></div>

<div class="ghost gutter-plus">
  <p>Possiedi un'utenza su {{ serverName|capitalize }}? Accedi usando le credenziali in tuo possesso.</p>
  <ul class="list vertical">
    <li>
      <form action="{{ baseUri }}/accedi/" id="signinform" name="signinform" method="post" role="form">
        <ul class="list vertical mbottom10">
          <li><input type="email" name="email" placeholder="E-mail">&nbsp;&nbsp;<a class="small" href="{{ controllerPath }}/invia-email-attivazione/" target="_self">Non hai ricevuto l'e-mail di attivazione?</a></li>
          <li><input type="password" name="password" placeholder="Password">&nbsp;&nbsp;<a class="small" href="{{ controllerPath }}/resetta-password/" target="_self">Hai dimenticato la password?</a></li>
          <li><button type="submit" name="signin" class="btn blue">Accedi</button></li>
        </ul>
      </form>
    </li>
  </ul>
</div>

<div class="ghost">
  <p>Se sei già registrato su uno dei seguenti social network, clicca il banner corrispondente per accedere.</p>
  <ul class="list social-grp">
    <li><a id="facebook-btn" rel="facebook" href="{{ baseUri }}/login/facebook/"><span class="logo"></span>Facebook</a></li>
    <li><a id="google-btn" rel="google" href="{{ baseUri }}/login/google/"><span class="logo"></span>Google+</a></li>
    <li><a id="linkedin-btn" rel="linkedin" href="{{ baseUri }}/login/linkedin/"><span class="logo"></span>LinkedIn</a></li>
    <li><a id="github-btn" rel="github" href="{{ baseUri }}/login/github/"><span class="logo"></span>GitHub</a></li>
    <li class="space"></li>
  </ul>
</div>