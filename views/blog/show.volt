<div class="column-left">
  {% set usersBaseUrl = 'http://utenti.'~serverName~'/' %}
  {% set userUrl = usersBaseUrl~doc.userId %}
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

  <div class="item">
    <div class="item-section">
      <a href="#">{{ doc.getPublishingType() }}</a>
    </div>

    <div id="title"><span>{{ doc.title }}</span></div>

    <div class="item-tools">
      <a href="#" title ="Questo articolo è interessante"><i class="icon-arrow-up icon-large"></i></a>{{ doc.getScore() }}<a href="#" title="Questo articolo è poco chiaro e di dubbia utilità"><i class="icon-arrow-down icon-large"></i></a>
      <a href="#"><i class="icon-comments icon-large"></i></a>{{ replaysCount }}
    </div>

    <div class="item-container shift">
      <div class="item-hits"><b>{{ doc.getHitsCount() }}</b> lettori</div>
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
        <img class="" src="http://programmazione.it/picture.php?idItem=48456&amp;id=52558c0458cae" alt="Copertina" />
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
      <div class="item-body">
        {{ doc.html }}
        {% if doc.type == 'book' %}
        <div class="positive">
          {{ markdown.render(doc.positive) }}
        </div>
        <div class="negative">
          {{ markdown.render(doc.negative) }}
        </div>
        {% endif %}
      </div>
      <ul class="list item-tags">
        {% set tags = doc.getTags() %}
        {% for tag in tags %}
          <li><a class="tag" href="/tag/">{{ tag['value'] }}</a></li>
        {% endfor  %}
      </ul>
      <div class="item-info pull-right">
        <div>{{ doc.whenHasBeenPublished() }}, {{ label }} da</div>
        <a href="{{ userUrl }}"><img class="gravatar" src="{{ doc.getGravatar() }}&s=32" /></a>
        <div>
          <a href="{{ userUrl }}">{{ doc.getDisplayName() }}</a>
          <div class="reputation"><b>2345</b><i class="icon-certificate gold"></i> 12<i class="icon-certificate silver"></i> 10<i class="icon-certificate bronze"></i> 10</div>
        </div>
      </div>
      <div class="list item-links">
        <li><a class="btn mini blue" href="#"><i class="icon-file-text"></i> MODIFICA</a></li>
        <li><a class="btn mini blue" href="#"><i class="icon-flag"></i> FLAGGA</a></li>
        <li><a class="btn mini red" href="#"><i class="icon-trash"></i> ELIMINA</a></li>
        <li><a class="btn mini orange" href="#"><i class="icon-unlock"></i> BLOCCA</a></li>
        <li><a class="btn mini orange" href="#"><i class="icon-umbrella"></i> PROTEGGI</a></li>
        <li><a class="btn mini blue" href="#"><i class="icon-pushpin"></i> APPUNTA</a></li>
        <li class="space"></li>
      </div>
    </div>
  </div>

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

  <div class="item">
    <div class="item-container shift">
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
      <div class="item-body">
        {{ replay.html }}
      </div>
      <div class="item-info pull-right">
        <div>{{ replay.whenHasBeenPublished() }}</div>
        <a href="{{ userUrl }}"><img class="gravatar" src="{{ replay.getGravatar() }}&s=32" /></a>
        <div>
          <a href="{{ userUrl }}">{{ replay.getDisplayName() }}</a><br>
          <div class="reputation"><b>2345</b><i class="icon-certificate gold"></i> 12<i class="icon-certificate silver"></i> 10<i class="icon-certificate bronze"></i> 10</div>
        </div>
      </div>
      <div class="list item-links">
        <li><a class="btn mini blue" href="#"><i class="icon-file-text"></i> MODIFICA</a></li>
        <li><a class="btn mini blue" href="#"><i class="icon-flag"></i> FLAGGA</a></li>
        <li><a class="btn mini red" href="#"><i class="icon-trash"></i> ELIMINA</a></li>
        <li><a class="btn mini blue" href="#"><i class="icon-unlock"></i> RISPONDI</a></li>
        <li class="space"></li>
      </div>
    </div>
  </div>

  {% endfor %}

</div> <!-- /column-left -->

<div class="column-right">

<div class="banner"><a href="#"><img src="/img/300x250cro.jpeg" /></a></div>

</div> <!-- /column-right -->