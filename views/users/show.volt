{% set lastVisit = doc.getLastvisit() %}
{% set hitsCount = doc.getHitsCount() %}

  <div class="user-profile">
    <img class="img-polaroid" src="{{ doc.getGravatar(doc.email) }}&s=240" />
    <div class="user-name">{% if doc.sex == 0 %}<i class="icon-male"></i>{% else %}<i class="icon-female"></i>{% endif %}&nbsp;{{ doc.displayName }}</div>
    <div class="reputation big">
      <div>2345</div>
      <div><span>REPUTAZIONE</span></div>
      <div><i class="icon-certificate gold"></i> 12<i class="icon-certificate silver"></i> 10<i class="icon-certificate bronze"></i> 10</div>
    </div>

    <blockquote>
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
    </blockquote>

    <ul class="list item-links">
      {% if user is defined and (doc.id == user.id or user.isAdmin()) %}
      <li><a class="btn mini blue" href="#"><i class="icon-file-text"></i> MODIFICA</a></li>
      {% endif %}
      {% if user is defined and user.isAdmin() %}
      <li><a class="btn mini red" href="#"><i class="icon-flag"></i> BANNA</a></li>
      {% endif %}
      {% if user is defined and (doc.id == user.id or user.isAdmin()) %}
      <li><a class="btn mini red" href="#"><i class="icon-trash"></i> ELIMINA</a></li>
      {% endif %}
      {% if user is defined and doc.id == user.id %}
      <li><a class="btn mini red" href="{{ baseUri }}/disconnetti/"><i class="icon-signout"></i> DISCONNETTI</a></li>
      {% endif %}
      <li class="space"></li>
    </ul>
  </div>

  <div class="user-inventory">
    {{ flash.output() }}
    <ul class="list tabs">
      <li class="active"><a href="http://tags.programmazione.me/sinonimi/">REPUTAZIONE</a></li>
      <li><a href="http://tags.programmazione.me/per-nome/">CONTRIBUTI</a></li>
      <li><a href="http://tags.programmazione.me/per-nome/">PREFERITI</a></li>
      <li><a href="http://tags.programmazione.me/popolari/">TAGS</a></li>
      <li><a href="http://tags.programmazione.me/sinonimi/">BADGES</a></li>
      <li><a href="http://tags.programmazione.me/nuovi/">BOUNTIES</a></li>
      <li><a href="http://tags.programmazione.me/sinonimi/">ATTIVITÀ</a></li>
    </ul>
  </div>