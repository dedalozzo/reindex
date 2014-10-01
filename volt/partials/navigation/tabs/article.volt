{% extends "partials/navigation/tabs/index.volt" %}
{% block vars %}
  {% if resource is defined %}
    {% set resource = resource~'/articoli' %}
  {% else %}
    {% set resource = 'articoli' %}
  {% endif %}
  {% set button = 'nuovo' %}
{% endblock %}