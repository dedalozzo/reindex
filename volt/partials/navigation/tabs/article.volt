{% extends "partials/navigation/tabs/index.volt" %}
{% block vars %}
  {% if etag is defined %}
    {% set resource = etag.name~'/articoli' %}
  {% else %}
    {% set resource = 'articoli' %}
  {% endif %}
  {% set buttonLabel = 'nuovo' %}
{% endblock %}