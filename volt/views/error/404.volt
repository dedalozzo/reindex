{% extends "templates/base.volt" %}

{% block topbar %}{% endblock %}
{% block container %}fixed{% endblock %}
{% block billboard %}{% endblock %}

{% block header %}
  <ul class="list auto">
    {% include "partials/brand.volt" %}
  </ul>
{% endblock %}

{% block content %}
  <div id="content">

    <div id="page-title">{{ title }}</div>
    <hr class="fade-long">

    <div class="welcome">
      <img src="//{{ serverName }}/pit-bootstrap/dist/img/404.jpg" width="530" height="261">
      <p>
        La pagina richiesta non è disponibile. Il link che hai seguito non è funzionante o la pagina è stata rimossa.
      </p>
    </div>

    {% if currentUser is defined %}
      {% set username = currentUser.username %}
    {% else %}
      {% set username = 'ospite' %}
    {% endif %}

    <aside class="prompt gutter">
      <p>
        <span class="at">{{ username }}@pit</span>:<span class="tilde">~</span>$&nbsp;{{ method }}&nbsp;{{ url }}<br>
        <span class="at">{{ username }}@pit</span>:<span class="tilde">~</span>$&nbsp;Error&nbsp;404&nbsp;(Page&nbsp;Not&nbsp;Found)<br>
        <span class="at">{{ username }}@pit</span>:<span class="tilde">~</span>$&nbsp;<blink>&#95</blink>
      </p>
    </aside>

  </div> <!-- /content -->
{% endblock %}