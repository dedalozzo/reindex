{% set usersBaseUrl = '//utenti.'~domainName~'/' %}
{% set userUrl = usersBaseUrl~doc.userId %}
{% set hitsCount = doc.getHitsCount() %}
{% set replaysCount = doc.getReplaysCount() %}

{% if doc.type == 'question' %}
  {% set label = 'formulata' %}
{% elseif doc.type == 'link' %}
  {% set label = 'inserito' %}
{% elseif doc.type == 'article' %}
  {% set label = 'scritto' %}
{% else %}
  {% set label = 'recensito' %}
{% endif %}

<div class="column-left">
  <div id="page-title">{{ doc.title }}</div>

  <div class="item-tools">
    <a href="#" title ="Questo articolo è interessante"><i class="icon-arrow-up icon-large"></i></a>{{ doc.getScore() }}<a href="#" title="Questo articolo è poco chiaro e di dubbia utilità"><i class="icon-arrow-down icon-large"></i></a>
    <a href="#"><i class="icon-comments icon-large"></i></a>{{ replaysCount }}
  </div>

  <section class="item-content shift">
    <div class="item-hits"><b>{{ doc.hitsCount }}</b>{% if hitsCount == 1 %} lettore{% else %} lettori{% endif %}</div>
    <ul class="list toolbar">
      <li class="toolgroup">
        <a href="#" title="Aggiungi ai preferiti"><i class="icon-star-empty"></i></a>
        <span>{{ doc.getStarsCount() }}</span>
      </li>
      <li class="toolgroup break">
        <a href="#" title="Sottoscrivi la discussione"><i class="icon-eye-open"></i></a>
        <span>{{ doc.getSubscribersCount() }}</span>
      </li>
      <li class="toolgroup">
        <a href="#" title="Condividi su Twitter"><i class="icon-twitter"></i></a>
        <span>{{ doc.getStarsCount() }}</span>
      </li>
      <li class="toolgroup">
        <a href="#" title="Condividi su Facebook"><i class="icon-facebook"></i></a>
        <span>{{ doc.getStarsCount() }}</span>
      </li>
      <li class="toolgroup break">
        <a href="#" title="Condividi su Google+"><i class="icon-google-plus"></i></a>
        <span>{{ doc.getStarsCount() }}</span>
      </li>
    </ul>

    {% if doc.type == 'book' %}
    <div class="item-meta">
      <img class="" src="//programmazione.it/picture.php?idItem=48456&amp;id=52558c0458cae" alt="Copertina" />
      <span>ISBN: </span>{{ doc.isbn }}<br>
      <span>Autori: </span>{{ doc.authors }}<br>
      <span>Editore: </span>{{ doc.publisher }}<br>
      <span>Lingua: </span>{{ doc.language }}<br>
      <span>Anno: </span>{{ doc.year }}<br>
      <span>Pagine: </span>{{ doc.pages }}<br>
      <span>Allegati: </span>{{ doc.attachments is empty ? 'nessuno' : doc.attachments }}
      <div class="clear"></div>
    </div>
    {% endif %}
    <section class="item-body">
      {{ doc.html }}
      {% if doc.type == 'book' %}
      <div class="positive">
        {{ markdown.render(doc.positive) }}
      </div>
      <div class="negative">
        {{ markdown.render(doc.negative) }}
      </div>
      {% endif %}
    </section>
    <div class="ghost gutter">
      <ul class="list item-tags">
        <li><span class="tag {{ doc.type }}">{{ doc.getPublishingType() }}</span></li>
        {% set tags = doc.getTags() %}
        {% for tag in tags %}
        <li><a class="tag" href="/tag/">{{ tag['value'] }}</a></li>
        {% endfor  %}
      </ul>
      <section class="item-user pull-right">
        <a class="avatar" href="{{ userUrl }}"><img class="img-polaroid" src="{{ doc.getGravatar() }}&s=48" /></a>
        <div class="reputation">
          <div>2345</div>
          <div>REPUTAZIONE</div>
          <div><i class="icon-certificate gold"></i> 12<i class="icon-certificate silver"></i> 14<i class="icon-certificate bronze"></i> 122</div>
        </div>
        <a class="username" href="{{ userUrl }}">{{ doc.getDisplayName() }}</a>
      </section>
    </div>
    <ul class="list pills gutter-plus">
      <li><a class="blue" href="//{{ serverName~'/'~doc.id~'/modifica/' }}"><i class="icon-file-text"></i></a></li>
      <li><a class="blue" href="//{{ serverName~'/flagga/'~doc.id }}"><i class="icon-flag"></i></a></li>
      <li><a class="red" href="//{{ serverName~'/elimina/'~doc.id }}"><i class="icon-trash"></i></a></li>
      <li><a class="orange" href="//{{ serverName~'/blocca/'~doc.id }}"><i class="icon-unlock"></i></a></li>
      <li><a class="orange" href="//{{ serverName~'/proteggi/'~doc.id }}"><i class="icon-umbrella"></i></a></li>
      <li><a class="blue" href="//{{ serverName~'/appunta/'~doc.id }}"><i class="icon-pushpin"></i></a></li>
      <li class="space"></li>
      <li><a class="red" href="//{{ serverName~'/appunta/'~doc.id }}">COMMENTA</a></li>
    </ul>
  </section>

  <ul class="list tabs">
    <li><span><b>{{ replaysCount }}{% if replaysCount == 1 %} COMMENTO{% else %} COMMENTI{% endif %}</b></span></li>
    <li class="pull-right"><a href="#">PIÙ VOTATI</a></li>
    <li class="active pull-right"><a href="#">RECENTI</a></li>
  </ul>

  {% for replay in replays %}
  {% set userUrl = usersBaseUrl~replay.userId %}

  {% if not loop.first %}
  <div class="line"></div>
  {% endif %}

  <div class="item-tools">
    <a href="#" title ="Questo articolo è interessante"><i class="icon-arrow-up icon-large"></i></a>{{ replay.getScore() }}<a href="#" title="Questo articolo è poco chiaro e di dubbia utilità"><i class="icon-arrow-down icon-large"></i></a>
    <a href="#"><i class="icon-ok icon-large"></i></a>
  </div>

  <div class="item-content shift">
    <!--
    <ul class="list toolbar">
      <li class="toolgroup">
        <a href="#" title="Condividi su Twitter"><i class="icon-twitter"></i></a>
        <span>{{ doc.getStarsCount() }}</span>
      </li>
      <li class="toolgroup">
        <a href="#" title="Condividi su Facebook"><i class="icon-facebook"></i></a>
        <span>{{ doc.getStarsCount() }}</span>
      </li>
      <li class="toolgroup">
        <a href="#" title="Condividi su Google+"><i class="icon-google-plus"></i></a>
        <span>{{ doc.getStarsCount() }}</span>
      </li>
    </ul>
    -->
    <div class="item-body">
      {{ replay.html }}
    </div>
    <section class="item-user">
      <a class="avatar" href="{{ userUrl }}"><img class="img-polaroid" src="{{ replay.getGravatar() }}&s=48" /></a>
      <div class="reputation">
        <div>2345</div>
        <div>REPUTAZIONE</div>
        <div><i class="icon-certificate gold"></i> 12<i class="icon-certificate silver"></i> 14<i class="icon-certificate bronze"></i> 122</div>
      </div>
      <a class="username" href="{{ userUrl }}">{{ replay.getDisplayName() }}</a>
    </section>
    <ul class="list item-links gutter">
      <li><a class="btn mini blue" href="#"><i class="icon-file-text"></i> MODIFICA</a></li>
      <li><a class="btn mini blue" href="#"><i class="icon-flag"></i> FLAGGA</a></li>
      <li><a class="btn mini red" href="#"><i class="icon-trash"></i> ELIMINA</a></li>
      <li class="space"></li>
      <li><a class="btn mini blue" href="#"><i class="icon-unlock"></i> RISPONDI</a></li>
    </ul>
  </div>

  {% endfor %}

</div> <!-- /column-left -->

<aside class="column-right">

<div class="banner"><a href="#"><img src="/img/300x250cro.jpeg" /></a></div>

</aside> <!-- /column-right -->