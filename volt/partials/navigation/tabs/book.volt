{% extends "partials/navigation/tabs/index.volt" %}
{% block vars %}
  {% if resource is defined %}
    {% set resource = resource~'/libri' %}
  {% else %}
    {% set resource = 'libri' %}
  {% endif %}
  {% set button = 'nuovo' %}
{% endblock %}