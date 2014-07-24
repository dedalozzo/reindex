{% extends "templates/structure/double-column.volt" %}

{% block sectionMenu %}
  {% include "partials/navigation/categories.volt" %}
  {{ partial("partials/navigation/sections/index/"~arraycolumn(categories, 'section', 'filter')[filter]) }}
{% endblock %}

{% block columnRight %}

  <div class="banner"><a href="#"><img src="/img/300x250.gif" /></a></div>

  {% include "partials/widgets/stats.volt" %}

  {% include "partials/widgets/updates.volt" %}

  <div class="banner"><a href="#"><img src="/img/300x250cro.jpeg" /></a></div>

  {% include "partials/widgets/tags.volt" %}

  <div class="banner"><a href="#"><img src="/img/300x250cro.jpeg" /></a></div>

  {% include "partials/widgets/badges.volt" %}

{% endblock %}