{% set usersBaseUrl = '//utenti.'~domainName~'/' %}
{% set userUrl = usersBaseUrl~doc.username %}
{% set hitsCount = doc.getHitsCount() %}
{% set repliesCount = doc.getRepliesCount() %}
{% set postType = doc.type %}
{% if currentUser is defined %}
  {% set liked = doc.didUserVote(currentUser) %}
{% else %}
  {% set liked = FALSE %}
{% endif %}
{% set score = doc.getScore() %}

{% include "partials/navigation/categories.volt" %}
{% set tagPaths = arraycolumn(categories, 'path', 'filter') %}
{% set tagLabels = arraycolumn(categories, 'label', 'filter') %}

<div id="page-title"><a href="#" title="Aggiungi ai preferiti"><i class="icon-star-empty"></i></a> {{ doc.title }}</div>
<hr class="fade-long">
<div class="column-left">

  <section class="item-content">
    <div class="item-time">{{ doc.whenHasBeenPublished() }}</div>

    {% if postType == 'book' %}
    <div class="item-meta">
      <img class="img-polaroid" src="//programmazione.it/picture.php?idItem=48456&amp;id=52558c0458cae" alt="Copertina">
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
      {% if postType == 'book' %}
      <div class="positive">
        {{ markdown.parse(doc.positive) }}
      </div>
      <div class="negative">
        {{ markdown.parse(doc.negative) }}
      </div>
      {% endif %}
    </section>
    <div class="ghost gutter">
      <ul class="list item-tags">
        <li><a class="tag {{ postType }}" href="//{{ domainName~tagPaths[postType] }}">{{ tagLabels[postType] }}</a></li>
        {% set tags = doc.getTags() %}
        {% for tag in tags %}
        <li><a class="tag" href="/tag/">{{ tag['value'] }}</a></li>
        {% endfor  %}
      </ul>
      <section class="item-user pull-right">
        <a class="avatar" href="{{ userUrl }}"><img class="img-polaroid" src="{{ doc.getGravatar() }}&s=48" /></a>
        <div class="reputation ext">
          <table>
            <tr><td>2345</td></tr>
            <tr><td>REPUTAZIONE</td></tr>
            <tr><td><span class="badges"><i class="icon-certificate gold"></i> 12<i class="icon-certificate silver"></i> 14<i class="icon-certificate bronze"></i> 122</span></td></tr>
          </table>
        </div>
        <a class="username" href="{{ userUrl }}">{{ doc.username }}</a>
      </section>
    </div>
    <ul class="list item-buttons gutter">
      <li><button class="btn btn-like {% if liked %} active {% endif %} red" title="mi piace"><i class="icon-thumbs-up icon-largest"></i></button></li>
      <li><button class="btn btn-link score">{{ score }}</button></li>
      <li class="space"></li>
      <li><button class="btn btn-icon active yellow" title="aggiungi la domanda ai preferiti"><i class="icon-star icon-large"></i></button></li>
      <li>
        <button class="btn btn-icon blue" title="condividi la domanda" data-dropdown="#dropdown-share"><i class="icon-share icon-large"></i></button>
        <div id="dropdown-share" class="dropdown dropdown-relative dropdown-tip">
          <ul class="dropdown-menu">
            <li><button title="condividi la domanda su Facebook"><i class="icon-facebook"></i>Condividi su Facebook</button></li>
            <li><button title="condividi la domanda su Twitter"><i class="icon-twitter"></i>Condividi su Twitter</button></li>
            <li><button title="condividi la domanda su Google+"><i class="icon-google-plus"></i>Condividi su Google+</button></li>
            <li class="dropdown-divider"></li>
            <li><button title="manda il link via e-mail"><i class="icon-mail-forward"></i>Condividi via e-mail</button></li>
            <li><button title="copia il link permanente negli appunti"><i class="icon-link"></i>Permalink</button></li>
          </ul>
        </div>
      </li>
      <li><button class="btn btn-icon blue" title="segnala un problema riguardante la domanda"><i class="icon-flag icon-large"></i></button></li>
      <li><a class="btn btn-icon trans blue" title="migliora la domanda modificandone il contenuto" href="//{{ serverName~'/'~doc.id~'/modifica/' }}"><i class="icon-file-text icon-large"></i></a></li>
      <li>
        <button class="btn btn-icon orange" title="strumenti di amministrazione" data-dropdown="#dropdown-admin"><i class="icon-gear icon-large"></i></button>
        <div id="dropdown-admin" class="dropdown dropdown-relative dropdown-anchor-right dropdown-tip">
          <ul class="dropdown-menu">
            <li><button title="impedisci che vengono aggiunte ulteriori risposte alla domanda"><i class="icon-unlock"></i>Chiudi</button></li>
            <li><button title="proteggi la domanda da eventuali modifiche"><i class="icon-umbrella"></i>Proteggi</button></li>
            <li><button title="proteggi la domanda da eventuali modifiche"><i class="icon-eye-close"></i>Nascondi</button></li>
            <li class="dropdown-divider"></li>
            <li><button title="appunta la domanda"><i class="icon-pushpin"></i>Appunta</button></li>
            <li><button title="elimina la domanda"><i class="icon-trash"></i>Elimina</button></li>
          </ul>
        </div>
      </li>
      <li><button class="btn blue"><i class="icon-reply"></i> RISPONDI</button></li>
    </ul>

    <ul class="list item-actors">
      {% set usersHaveVoted = doc.getUsersHaveVoted() %}
      {% for userHasVoted in usersHaveVoted %}
      <li><a href="{{ usersBaseUrl~userHasVoted.id }}"><img class="img-polaroid" title="{{ userHasVoted.username }}" src="{{ userHasVoted.gravatar }}&s=20" /></a></li>
      {% endfor  %}
    </ul>
  </section>

  <ul class="list tabs">
    <li><span><b>{{ repliesCount }}{% if repliesCount == 1 %} COMMENTO{% else %} COMMENTI{% endif %}</b></span></li>
    <li class="pull-right"><a href="#">PIÙ VOTATI</a></li>
    <li class="active pull-right"><a href="#">RECENTI</a></li>
  </ul>

  {% for reply in replies %}
  {% set username = reply.username %}
  {% set userUrl = usersBaseUrl~username %}

  {% if not loop.first %}
  {% endif %}

  <hr class="fade-short">
  <div class="item-time">{{ reply.whenHasBeenPublished() }}</div>
  <div class="item-content">
    <div class="item-body">
      {{ reply.html }}
    </div>
    <div class="ghost gutter">
      <section class="item-user pull-right">
        <a class="avatar" href="{{ userUrl }}"><img class="img-polaroid" src="{{ reply.getGravatar() }}&s=48" /></a>
        <div class="reputation ext">
          <table>
            <tr><td>2345</td></tr>
            <tr><td>REPUTAZIONE</td></tr>
            <tr><td><span class="badges"><i class="icon-certificate gold"></i> 12<i class="icon-certificate silver"></i> 14<i class="icon-certificate bronze"></i> 122</span></td></tr>
          </table>
        </div>
        <a class="username" href="{{ userUrl }}">{{ username }}</a>
      </section>
    </div>
    <ul class="list item-buttons gutter">
      <li><button class="btn btn-like {% if liked %} active {% endif %} red" title="la risposta mi piace"><i class="icon-thumbs-up icon-largest"></i></button></li>
      <li><button class="btn btn-link score">{{ reply.getScore() }}</button></li>
      <li><button class="btn btn-like {% if liked %} active {% endif %} red" title="la risposta mi piace"><i class="icon-ok icon-largest"></i></button></li>
      <li class="space"></li>
      <li><button class="btn btn-icon blue" title="condividi la risposta"><i class="icon-link icon-large"></i></button></li>
      <li><button class="btn btn-icon blue"><i class="icon-comment icon-large"></i></button></li>
      <li><button class="btn btn-icon blue" title="segnala un problema riguardante la risposta"><i class="icon-flag icon-large"></i></button></li>
      <li><a class="btn btn-icon blue" title="migliora la risposta modificandone il contenuto" href="//{{ serverName~'/'~doc.id~'/modifica/' }}"><i class="icon-file-text icon-large"></i></a></li>
      <li><button class="btn btn-icon red" title="elimina la risposta"><i class="icon-trash icon-large"></i></button></li>
    </ul>
  </div>

  {% endfor %}

</div> <!-- /column-left -->

<aside class="column-right">
  <div id="stats"><div>{{ doc.hitsCount }}</div>{% if hitsCount == 1 %} VISUALIZZAZIONE{% else %} VISUALIZZAZIONI{% endif %}</div>
  <div class="banner"><a href="#"><img src="/img/300x250cro.jpeg" /></a></div>
</aside> <!-- /column-right -->