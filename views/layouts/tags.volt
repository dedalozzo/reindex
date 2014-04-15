{% include "partials/navigation/sections/tags.volt" %}

{% include "partials/navigation/section-menu.volt" %}

{% include "partials/navigation/subsection-menu.volt" %}

<div class="ghost gutter">Un tag è un'etichetta che relaziona un contenuto con altri simili, inerenti lo stesso argomento. Un corretto utilizzo dei tag permette agli utenti di trovare contenuti afferenti ai propri interessi, agevolandoli nella selezione delle domande a cui potrebbero essere in grado di rispondere.</div>

{% if actionName == 'byName' %}
<div class="ghost gutter">
  <form method="get" action="search.php" autocomplete="off" name="form_search">
    <label>Cerca tra i <b>{{ entriesCount }}</b> tags: </label><input placeholder="nome tag" autocomplete="on" id="keyword" name="keyword" />
  </form>
</div>
{% endif %}

{% if entries is defined %}
  {% for entry in entries %}
    {% set modulus = loop.index % 4 %}
    {% if loop.first %}
    <ul class="list gutter">
    {% endif %}
      <li style="width: 25%;"><a class="tag" href="#">{{ entry.name }}</a><span class="popularity"> × {{ entry.postsCount }}</span><br>{{ entry.excerpt }}</li>
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