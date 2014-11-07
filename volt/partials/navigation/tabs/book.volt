{% extends "partials/navigation/tabs/index.volt" %}
{% block vars %}
  {% if etag is defined %}
    {% set resource = etag.name~'/libri' %}
  {% else %}
    {% set resource = 'libri' %}
  {% endif %}
  {% set buttonLabel = 'nuovo' %}
  {% set buttonLink = '/libri/aggiungi' %}
{% endblock %}