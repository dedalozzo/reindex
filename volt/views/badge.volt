{% extends "templates/base.volt" %}

{% block content %}
<div id="content">
  {% include "partials/navigation/menu/badge.volt" %}
  {% include "partials/navigation/menu.volt" %}

  <div class="column-left">
  {% include "partials/list-of-badges.volt" %}
  </div> <!-- /column-left -->

  <aside class="column-right">
    {% include "partials/widgets/stats.volt" %}
    <ul class="list vertical gutter even">
      <li><a class="badge" href="//{{ serverName }}/bronzo/"><i class="icon-certificate bronze"></i> Bronzo</a></li>
      <li>Servono ad incoraggiare gli utenti a provare nuove funzionalità del sito. Sono facili da ottenere se vuoi tentare!</li>
      <li><a class="badge" href="//{{ serverName }}/argento/"><i class="icon-certificate silver"></i> Argento</a></li>
      <li>Quelli d'argento sono meno facili da ottenere rispetto ai badges di bronzo. Devi partecipare attivamente alla vita della comunità perché ti vengano assegnati.</li>
      <li><a class="badge" href="//{{ serverName }}/oro/"><i class="icon-certificate gold"></i> Oro</a></li>
      <li>L'oro è il metallo più prezioso, pertanto questi badges li ottengono unicamente coloro che forniscono un importante contributo ai membri della comunità. Vengono raramente assegnati.</li>
    </ul>
  </aside> <!-- /column-right -->
</div>
{% endblock %}