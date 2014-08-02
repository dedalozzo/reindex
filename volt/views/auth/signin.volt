{% extends "templates/base.volt" %}

{% block topbar %}{% endblock %}
{% block container %}fixed{% endblock %}
{% block billboard %}{% endblock %}

{% block header %}
  <ul class="list auto">
    {% include "partials/brand.volt" %}
  </ul>
{% endblock %}

{% block content %}
  <div id="content">

    <div id="page-title">{{ title }}</div>
    <hr class="fade-long">

    {{ flash.output() }}

    <div class="welcome">
      <img src="//{{ serverName }}/pit-bootstrap/dist/img/languages.jpg" width="530" height="261">
      <p>
        Stai connesso con amici, colleghi, altri geeks come te, con la medesima passione per l'informatica.
        Tieniti aggiornato sulle cose che ti interessano. Apprendi dagli altri e insegna loro ciò che già sai.
        È del tutto <b>gratuito</b> e lo sarà sempre!
      </p>
    </div>

    <aside class="registration">
      <section class="notebook gutter" id="sign">
        <ul class="list tabs no-gutter">
          <li class="active"><a href="#signin" data-toggle="tab">ACCEDI</a></li>
          <li><a href="#signup" data-toggle="tab">REGISTRATI</a></li>
        </ul>
        <div class="notebook-page active" id="signin">
          <div>
            <p>Se possiedi già un'utenza sul sito, puoi accedere usando le tue credenziali.</p>
            <form action="//{{ serverName }}/accedi/" id="signinform" name="signinform" method="post" role="form">
              <input type="hidden" name="{{ security.getTokenKey() }}" value="{{ security.getToken() }}"/>
              <ul class="list vertical mbottom10">
                <li>
                  {{ email_field("email", "placeholder": "E-mail", "style": "width: 100%") }}
                  <label>{{ validation.first("email") }}</label>
                </li>
                <li>
                  {{ password_field("password", "placeholder": "Password") }}
                  <button type="submit" name="signin" class="btn blue">Accedi</button>
                  <label>{{ validation.first("password") }}</label>
                </li>
              </ul>
            </form>
          </div>
          <div>
            <p>Se non sei ancora registrato, puoi accedere usando uno dei seguenti social network.</p>
            <ul class="list social half-gutter">
              <li><a id="facebook-btn" rel="facebook" href="//{{ serverName }}/accedi/facebook/"><span class="logo"></span>Facebook</a></li>
              <li><a id="google-btn" rel="google" href="//{{ serverName }}/accedi/google/"><span class="logo"></span>Google+</a></li>
              <li class="space"></li>
            </ul>
            <ul class="list social">
              <li><a id="linkedin-btn" rel="linkedin" href="//{{ serverName }}/accedi/linkedin/"><span class="logo"></span>LinkedIn</a></li>
              <li><a id="github-btn" rel="github" href="//{{ serverName }}/accedi/github/"><span class="logo"></span>GitHub</a></li>
              <li class="space"></li>
            </ul>
          </div>
        </div>
        <div class="notebook-page" id="signup">
          <div>
            <form action="//{{ serverName }}/registrati/" id="signinform" name="signinform" method="post" role="form">
              <ul class="list vertical mbottom10">
                <li>{{ text_field("username", "placeholder": "Nome utente") }}</li>
                <li>{{ email_field("email", "placeholder": "E-mail") }}</li>
                <li>{{ password_field("password", "placeholder": "Password") }}</li>
                <li>{{ password_field("password2", "placeholder": "Ripeti la password") }}</li>
                <li><button type="submit" name="signin" class="btn blue">Registrati</button></li>
              </ul>
            </form>
          </div>
        </div>
      </section>
    </aside>

  </div> <!-- /content -->
{% endblock %}