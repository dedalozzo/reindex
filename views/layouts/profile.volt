{% extends "index.volt" %}

{% block topbar %}
  {% set sectionName = 'users' %}
  {% include "partials/navigation/topbar.volt" %}
{% endblock %}

{% block billboard %}
{% endblock %}

{% block content %}
<div id="content" style="background-image: url(//www.utepprintstore.com/wp-content/uploads/Desktop-Background-1024x768.jpg); background-size: 970px 410px; background-repeat: no-repeat;">
  <div class="ghost gutter-plus" style="margin-top: 200px;">
    <div style="position: relative;">
      <img id="avatar" class="img-polaroid pull-left" src="{{ doc.getGravatar(doc.email) }}&s=160">
      {% if currentUser is defined and doc.id == currentUser.id %}
      <a class="change-avatar" href="http://it.gravatar.com/"><i class="icon-camera"></i>CAMBIA FOTO</a>
      {% endif %}
    </div>
    <div class="pippo">{{ doc.firstName }} {{ doc.lastName }}</div>
    <div class="pull-right" style="margin-top: 148px;">
      {% if currentUser is defined and (doc.id == currentUser.id or currentUser.isAdmin()) %}
      <a class="btn blue" href="#"><i class="icon-user"></i> MODIFICA</a>
      {% endif %}
      {% if currentUser is defined and currentUser.isAdmin() %}
      <a class="btn" href="#"><i class="icon-flag"></i> BANNA</a>
      {% endif %}
    </div>
  </div>

  {% set resourceName = doc.username %}
  {% include "partials/navigation/sections/profile.volt" %}
  {% include "partials/navigation/section-menu.volt" %}
  {% include "partials/navigation/section-submenu.volt" %}

  {{ flash.output() }}

  <div class="column-left">

    {% include "partials/list-of-posts.volt" %}

  </div> <!-- /column-left -->

  <div class="column-right">
    {% set lastVisit = doc.getLastvisit() %}
    {% set hitsCount = doc.getHitsCount() %}

    <div class="reputation big">
      <table>
        <tr><td>2345</td></tr>
        <tr><td>REPUTAZIONE</td></tr>
        <tr><td><span class="badges"><i class="icon-certificate gold"></i> 12<i class="icon-certificate silver"></i> 14<i class="icon-certificate bronze"></i> 122</span></td></tr>
      </table>
    </div>

    <div class="banner"><a href="#"><img src="/img/300x250.gif" /></a></div>
    {% include "partials/widgets/stats.volt" %}
    {% include "partials/widgets/tags.volt" %}
    <div class="banner"><a href="#"><img src="/img/300x250cro.jpeg" /></a></div>
    {% include "partials/widgets/badges.volt" %}

    <!--<blockquote>
      {% if doc.firstName is defined %}
        Mi chiamo {{ doc.firstName|upper }} {{ doc.lastName|upper }}.
      {% else %}
        {{ doc.username }}
      {% endif %}
      {% if doc.birthday is defined %}
        Ho {{ doc.getAge() }} anni.
      {% endif %}
      Mi sono iscritto il {{ doc.getElapsedTimeSinceRegistration()|lower }}.
      {% if lastVisit != "" %}
        La mia ultima visita risale al {{ lastVisit|lower }}.
      {% endif %}
      Il mio profilo è stato visualizzato {% if hitsCount == 1 %}una volta{% else %}{{ hitsCount }} volte{% endif %}.
    </blockquote>-->

  </div> <!-- /column-right -->

</div> <!-- /content -->
{% endblock %}

{% block script %}
<script>
  $('html, body').animate({scrollTop: '+=220px'}, 1);
</script>
{% endblock %}