<div id="title" class="dark">Accedi</div>

{{ flash.output() }}

<div class="ghost gutter-plus">
  <p>Possiedi un'utenza su {{ serverName|capitalize }}? Accedi usando le credenziali in tuo possesso.</p>
  <form action="{{ baseUri }}/accedi/" id="signinform" name="signinform" method="post" role="form">
    <input type="hidden" name="{{ security.getTokenKey() }}" value="{{ security.getToken() }}"/>
    <ul class="list vertical mbottom10">
      <li>{{ email_field("email", "placeholder": "Email") }}&nbsp;&nbsp;<a class="small" href="{{ baseUri }}/invia-email-attivazione/" target="_self">Non hai ricevuto l'e-mail di attivazione?</a></li>
      <li>{{ password_field("password", "placeholder": "Password") }}&nbsp;&nbsp;<a class="small" href="{{ baseUri }}/resetta-password/" target="_self">Hai dimenticato la password?</a></li>
      <li><button type="submit" name="signin" class="btn blue">Accedi</button></li>
    </ul>
  </form>
</div>

<div class="ghost">
  <p>Se sei già registrato su uno dei seguenti social network, clicca il banner corrispondente per accedere.</p>
  <ul class="list social-grp">
    <li><a id="facebook-btn" rel="facebook" href="{{ baseUri }}/accedi/facebook/"><span class="logo"></span>Facebook</a></li>
    <li><a id="google-btn" rel="google" href="{{ baseUri }}/accedi/google/"><span class="logo"></span>Google+</a></li>
    <li><a id="linkedin-btn" rel="linkedin" href="{{ baseUri }}/accedi/linkedin/"><span class="logo"></span>LinkedIn</a></li>
    <li><a id="github-btn" rel="github" href="{{ baseUri }}/accedi/github/"><span class="logo"></span>GitHub</a></li>
    <li class="space"></li>
  </ul>
</div>