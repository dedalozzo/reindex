{% include "partials/navigation/sections/users.volt" %}

{% include "partials/navigation/section-menu.volt" %}

{% include "partials/navigation/subsection-menu.volt" %}

{% if actionName == 'byName' %}
<div class="ghost gutter">
  <form method="get" action="search.php" autocomplete="off" name="form_search">
    <label>Cerca tra i <b>{{ usersCount }}</b> utenti registrati: </label><input placeholder="nome utente" autocomplete="on" id="keyword" name="keyword" />
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
      <li class="avatar" style="width: 25%;">
        <a href="{{ url }}"><img class="gravatar" src="{{ entry.gravatar }}&s=64" /></a>
        <div class="avatar-info">
          <div><a href="{{ url }}">{{ entry.displayName }}</a></div>
          <div class="reputation"><b>2345</b><i class="icon-certificate gold"></i> 12<i class="icon-certificate silver"></i> 10<i class="icon-certificate bronze"></i> 10</div>
          <div class="when">iscritto il {{ entry.when }}</div>
        </div>
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