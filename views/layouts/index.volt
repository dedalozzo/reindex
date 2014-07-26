{% extends "templates/structure/double-column.volt" %}

{% block sectionMenu %}
  {% include "partials/navigation/categories.volt" %}
  {% if postType is defined %}
    {{ partial("partials/navigation/sections/home/"~postType) }}
  {% else %}
    {% include "partials/navigation/sections/home/none.volt" %}
  {% endif %}
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