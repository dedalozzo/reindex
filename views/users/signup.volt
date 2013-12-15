<div id="title" class="dark">Registrati</div>

<div class="ghost gutter-plus">
  <p>Se possiedi già un'utenza su uno dei seguenti social network, clicca il banner corrispondente per registrarti.</p>
  <ul class="list social-grp">
    <li><a id="facebook-btn" rel="facebook" href="{{ baseUri }}/login/facebook/"><span class="logo"></span>Facebook</a></li>
    <li><a id="google-btn" rel="google" href="{{ baseUri }}/login/google/"><span class="logo"></span>Google+</a></li>
    <li><a id="linkedin-btn" rel="linkedin" href="{{ baseUri }}/login/linkedin/"><span class="logo"></span>LinkedIn</a></li>
    <li><a id="github-btn" rel="github" href="{{ baseUri }}/login/github/"><span class="logo"></span>GitHub</a></li>
    <li class="space"></li>
  </ul>
</div>

<div class="ghost">
  <p>In alternativa puoi registrarti su {{ serverName|capitalize }} semplicemente inserendo i dati richiesti qui di seguito.</p>
  <ul class="list vertical">
    <li>
      <form action="{{ baseUri }}/registrati/" id="signinform" name="signinform" method="post" role="form">
        <ul class="list vertical mbottom10">
          <li><input type="text" name="username" placeholder="Nome utente"></li>
          <li><input type="email" name="email" placeholder="E-mail"></li>
          <li><input type="password" name="password" placeholder="Password"></li>
          <li><input type="password" name="password2" placeholder="Ripeti la password"></li>
          <li><button type="submit" name="signin" class="btn blue">Registrati</button></li>
        </ul>
      </form>
    </li>
  </ul>
</div>