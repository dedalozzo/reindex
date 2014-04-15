{% extends "templates/list.volt" %}

{% block sectionMenu %}
  {% include "partials/navigation/sections/blog.volt" %}
{% endblock %}

{% block columnRight %}
  <div class="banner"><a href="#"><img src="/img/300x250.gif" /></a></div>

  {% include "partials/widgets/counter.volt" %}

  {% include "partials/widgets/tags.volt" %}

  <div class="banner"><a href="#"><img src="/img/300x250cro.jpeg" /></a></div>

  {% include "partials/widgets/badges.volt" %}
{% endblock %}