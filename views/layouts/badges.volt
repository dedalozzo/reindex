{% extends "templates/structure/double-column.volt" %}

{% block sectionMenu %}
  {% include "partials/navigation/sections/badges.volt" %}
{% endblock %}

{% block columnLeft %}
  {% include "partials/list-of-badges.volt" %}
{% endblock %}

{% block columnRight %}
  <ul class="list vertical gutter even">
    <li><a class="badge" href="//{{ serverName }}/bronzo/"><i class="icon-certificate bronze"></i> Bronzo</a></li>
    <li>Servono ad incoraggiare gli utenti a provare nuove funzionalità del sito. Sono facili da ottenere se vuoi tentare!</li>
    <li><a class="badge" href="//{{ serverName }}/argento/"><i class="icon-certificate silver"></i> Argento</a></li>
    <li>Quelli d'argento sono meno facili da ottenere rispetto ai badges di bronzo. Devi partecipare attivamente alla vita della comunità perché ti vengano assegnati.</li>
    <li><a class="badge" href="//{{ serverName }}/oro/"><i class="icon-certificate gold"></i> Oro</a></li>
    <li>L'oro è il metallo più prezioso, pertanto questi badges li ottengono unicamente coloro che forniscono un importante contributo ai membri della comunità. Vengono raramente assegnati.</li>
  </ul>
{% endblock %}