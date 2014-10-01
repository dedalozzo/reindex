{% extends "partials/navigation/tabs/index.volt" %}
{% block vars %}
  {% if resource is defined %}
    {% set resource = resource~'/links' %}
  {% else %}
    {% set resource = 'links' %}
  {% endif %}
  {% set button = 'nuovo' %}
{% endblock %}