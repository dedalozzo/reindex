{% extends "templates/base.volt" %}

{% block content %}
<div id="content">
  {{ partial("partials/navigation/tabs/"~controllerName) }}
  {% include "partials/navigation/tabs.volt"%}
  {% include "partials/navigation/pills.volt" %}

  <div class="column-left">
    {% set showUser = TRUE %}
    {% include "partials/list-of-posts.volt" %}
  </div> <!-- /column-left -->

  <aside class="column-right">
    {% include "partials/widgets/stats.volt" %}
    <div class="banner"><a href="#"><img src="/img/300x250.gif" /></a></div>
    {% include "partials/widgets/updates.volt" %}
    {# {% include "partials/widgets/tags.volt" %} #}
    <div class="banner"><a href="#"><img src="/img/300x250cro.jpeg" /></a></div>
    {% include "partials/widgets/badges.volt" %}
  </aside> <!-- /column-right -->
</div> <!-- /content -->
{% endblock %}