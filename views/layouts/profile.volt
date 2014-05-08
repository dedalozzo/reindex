{% extends "index.volt" %}

{% block scrollable %}
{% set lastVisit = doc.getLastvisit() %}
{% set hitsCount = doc.getHitsCount() %}
<div id="scrollable">
  <div id="content" style="background-image: url(//tourists360.com/wp-content/uploads/2014/02/Paradise-Island-7.jpg); background-size: 970px 410px; background-repeat: no-repeat;">
    <div class="ghost gutter-plus" style="margin-top: 200px;">
      <img id="avatar" class="img-polaroid pull-left" src="{{ doc.getGravatar(doc.email) }}&s=160">
      <div class="pull-right" style="margin-top: 148px;">
        <a class="btn small cyan" href="#"><i class="icon-file-text"></i> CAMBIA FOTO</a>
        {% if currentUser is defined and (doc.id == currentUser.id or currentUser.isAdmin()) %}
        <a class="btn small orange" href="#"><i class="icon-file-text"></i> MODIFICA</a>
        {% endif %}
        {% if currentUser is defined and currentUser.isAdmin() %}
        <a class="btn small red" href="#"><i class="icon-flag"></i> BANNA</a>
        {% endif %}
        {% if currentUser is defined and (doc.id == currentUser.id or currentUser.isAdmin()) %}
        <a class="btn small red" href="#"><i class="icon-trash"></i> ELIMINA</a>
        {% endif %}
        {% if currentUser is defined and doc.id == currentUser.id %}
        <a class="btn small blue" href="{{ baseUri }}/disconnetti/"><i class="icon-signout"></i> DISCONNETTI</a>
        {% endif %}
      </div>
    </div>

    {% include "partials/navigation/sections/profile.volt" %}
    {% include "partials/navigation/section-menu.volt" %}
    {% include "partials/navigation/subsection-menu.volt" %}

    {{ flash.output() }}

    <div class="column-left">

    {% include "partials/list-of-posts.volt" %}

    </div> <!-- /column-left -->

    <div class="column-right">

      <div class="banner"><a href="#"><img src="/img/300x250.gif" /></a></div>
      {% include "partials/widgets/counter.volt" %}
      {% include "partials/widgets/tags.volt" %}
      <div class="banner"><a href="#"><img src="/img/300x250cro.jpeg" /></a></div>
      {% include "partials/widgets/badges.volt" %}

      <div class="reputation big">
        <div>2345</div>
        <div><span>REPUTAZIONE</span></div>
        <div><i class="icon-certificate gold"></i> 12<i class="icon-certificate silver"></i> 10<i class="icon-certificate bronze"></i> 10</div>
      </div>

      <!--<blockquote>
        {% if doc.firstName is defined %}
          Mi chiamo {{ doc.firstName }} {{ doc.lastName }}.
        {% else %}
          {{ doc.displayName }}
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

  {% include "partials/navigation/footer.volt" %}

</div> <!-- /scrollable -->

  <script>
    $('html, body').animate({scrollTop: '+=220px'}, 1);
  </script>

{% endblock %}
