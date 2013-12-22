<div id="title" class="dark">Registrati</div>

<div class="ghost gutter-plus">
  <p>Se possiedi già un'utenza su uno dei seguenti social network, clicca il banner corrispondente per registrarti.</p>
  <ul class="list social-grp">
    <li><a id="facebook-btn" rel="facebook" href="{{ baseUri }}/accedi/facebook/"><span class="logo"></span>Facebook</a></li>
    <li><a id="google-btn" rel="google" href="{{ baseUri }}/accedi/google/"><span class="logo"></span>Google+</a></li>
    <li><a id="linkedin-btn" rel="linkedin" href="{{ baseUri }}/accedi/linkedin/"><span class="logo"></span>LinkedIn</a></li>
    <li><a id="github-btn" rel="github" href="{{ baseUri }}/accedi/github/"><span class="logo"></span>GitHub</a></li>
    <li class="space"></li>
  </ul>
</div>

<div class="ghost">
  <p>In alternativa puoi registrarti su {{ serverName|capitalize }} semplicemente inserendo i dati richiesti qui di seguito.</p>
  <form action="{{ baseUri }}/registrati/" id="signinform" name="signinform" method="post" role="form">
    <ul class="list vertical mbottom10">
      <li>{{ text_field("username", "placeholder": "Nome utente") }}</li>
      <li>{{ email_field(["email", "placeholder": "Email"]) }}</li>
      <li>{{ password_field("password", "placeholder": "Password") }}</li>
      <li>{{ password_field("password2", "placeholder": "Ripeti la password") }}</li>
      <li><button type="submit" name="signin" class="btn blue">Registrati</button></li>
    </ul>
  </form>
</div>