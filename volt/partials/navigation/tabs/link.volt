{% extends "partials/navigation/tabs/index.volt" %}
{% block vars %}
  {% if etag is defined %}
    {% set resource = etag.name~'/links' %}
  {% else %}
    {% set resource = 'links' %}
  {% endif %}
  {% set buttonLabel = 'nuovo' %}
  {% set buttonLink = 'nuovo/link/' %}
{% endblock %}