{% extends "templates/base.volt" %}

{% block topbar %}
  {% include "partials/navigation/topbar.volt" %}
{% endblock %}

{% block billboard %}
{% endblock %}

{% block content %}
<div id="content" style="background-image: url(//www.utepprintstore.com/wp-content/uploads/Desktop-Background-1024x768.jpg); background-size: 970px 410px; background-repeat: no-repeat;">
  <div class="ghost gutter-plus" style="margin-top: 200px;">
    <div style="position: relative;">
      <img id="avatar" class="img-polaroid pull-left" src="{{ user.getGravatar(user.email) }}&s=160">
      {% if currentUser is defined and user.id == currentUser.id %}
      <a class="change-avatar" href="http://it.gravatar.com/"><i class="icon-camera"></i>CAMBIA FOTO</a>
      {% endif %}
    </div>
    <div class="full-name">{% if user.firstName is defined %}{{ user.firstName }}{% endif %} {% if user.lastName is defined %}{{ user.lastName }}{% endif %}</div>
    <div class="pull-right" style="margin-top: 148px;">
      {% if currentUser is defined and (user.id == currentUser.id or currentUser.isAdmin()) %}
      <a class="btn blue" href="#"><i class="icon-user"></i> MODIFICA</a>
      {% endif %}
      {% if currentUser is defined and currentUser.isAdmin() %}
      <a class="btn" href="#"><i class="icon-flag"></i> BANNA</a>
      {% endif %}
    </div>
  </div>

  {% set resourceName = user.username %}
  {% set controllerPath = '/' %}
  {% include "partials/navigation/tabs/profile.volt" %}
  {% include "partials/navigation/tabs.volt" %}
  {% include "partials/navigation/pills.volt" %}

  {{ flash.output() }}

  <div class="column-left">

    {% include "partials/list-of-posts.volt" %}

  </div> <!-- /column-left -->

  <div class="column-right">
    {% set lastVisit = user.getLastvisit() %}
    {% set hitsCount = user.getHitsCount() %}

    <div class="reputation big">
      <table>
        <tr><td>2345</td></tr>
        <tr><td>REPUTAZIONE</td></tr>
        <tr><td><span class="badges"><i class="icon-certificate gold"></i> 12<i class="icon-certificate silver"></i> 14<i class="icon-certificate bronze"></i> 122</span></td></tr>
      </table>
    </div>

    {% include "partials/widgets/stats.volt" %}
    <div class="banner"><a href="#"><img src="/img/300x250.gif" /></a></div>
    {% include "partials/widgets/badges.volt" %}

    {#
    <blockquote>
      {% if user.firstName is defined %}
        Mi chiamo {{ user.firstName|upper }} {{ user.lastName|upper }}.
      {% else %}
        {{ user.username }}
      {% endif %}
      {% if user.birthday is defined %}
        Ho {{ user.getAge() }} anni.
      {% endif %}
      Mi sono iscritto il {{ user.getElapsedTimeSinceRegistration()|lower }}.
      {% if lastVisit != "" %}
        La mia ultima visita risale al {{ lastVisit|lower }}.
      {% endif %}
      Il mio profilo è stato visualizzato {% if hitsCount == 1 %}una volta{% else %}{{ hitsCount }} volte{% endif %}.
    </blockquote>
    #}

  </div> <!-- /column-right -->

</div> <!-- /content -->
{% endblock %}

{% block script %}
<script>
  $('html, body').animate({scrollTop: '+=252px'}, 1);
</script>
{% endblock %}