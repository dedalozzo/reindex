<div id="page-title">{{ title }}</div>

{{ flash.output() }}

<div class="ghost gutter-plus">
  <p>Possiedi un'utenza su {{ serverName|capitalize }}? Accedi usando le credenziali in tuo possesso.</p>
  <form action="//{{ serverName }}/accedi/" id="signinform" name="signinform" method="post" role="form">
    <input type="hidden" name="{{ security.getTokenKey() }}" value="{{ security.getToken() }}"/>
    <ul class="list vertical mbottom10">
      <li>
        {{ email_field("email", "placeholder": "E-mail") }}
        <label>{{ validation.first("email") }}</label>
      </li>
      <li>
        {{ password_field("password", "placeholder": "Password") }}
        <label>{{ validation.first("password") }}</label>
      </li>
      <li><button type="submit" name="signin" class="btn blue">Accedi</button></li>
    </ul>
  </form>
</div>

<div class="ghost">
  <p>Se sei già registrato su uno dei seguenti social network, clicca il banner corrispondente per accedere.</p>
  <ul class="list social">
    <li><a id="facebook-btn" rel="facebook" href="//{{ serverName }}/accedi/facebook/"><span class="logo"></span>Facebook</a></li>
    <li><a id="google-btn" rel="google" href="//{{ serverName }}/accedi/google/"><span class="logo"></span>Google+</a></li>
    <li><a id="linkedin-btn" rel="linkedin" href="//{{ serverName }}/accedi/linkedin/"><span class="logo"></span>LinkedIn</a></li>
    <li><a id="github-btn" rel="github" href="//{{ serverName }}/accedi/github/"><span class="logo"></span>GitHub</a></li>
    <li class="space"></li>
  </ul>
</div>