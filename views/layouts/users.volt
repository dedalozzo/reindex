{% include "partials/navigation/sections/users.volt" %}

{% include "partials/navigation/section-menu.volt" %}

{% include "partials/navigation/subsection-menu.volt" %}

{% if actionName == 'byName' %}
<div class="ghost gutter">
  <form method="get" action="search.php" autocomplete="off" name="form_search">
    <label>Cerca tra i <b>{{ usersCount }}</b> utenti registrati: </label><input type="text" placeholder="nome utente" autocomplete="on" id="keyword" name="keyword" />
  </form>
</div>
{% endif %}
{% if entries is defined %}
  {% for entry in entries %}
    {% set modulus = loop.index % 4 %}
    {% set url = '//'~serverName~'/'~entry.id %}
    {% if loop.first %}
    <ul class="list gutter">
    {% endif %}
      <li style="width: 25%;">
        <section class="item-user">
          <a class="avatar" href="{{ url }}"><img class="img-polaroid" src="{{ entry.gravatar }}&s=48" /></a>
          <div class="reputation">
            <div>2345</div>
            <div>REPUTAZIONE</div>
            <div><i class="icon-certificate gold"></i> 12<i class="icon-certificate silver"></i> 14<i class="icon-certificate bronze"></i> 122</div>
          </div>
          <a class="username" href="{{ url }}">{{ entry.displayName }}</a>
        </section>
      </li>
    {% if loop.last %}
      {% for i in 1..modulus  %}
      <li style="width: 25%;"></li>
      {% endfor  %}
    </ul>

    <hr>
    {% elseif (modulus == 0) %}
    </ul>

    <hr>

    <ul class="list gutter">
    {% endif %}
    {% elsefor %}
    <div class="alert alert-info">Siamo spiacenti, la ricerca non ha prodotto alcun risultato.</div>
  {% endfor %}
{% endif %}