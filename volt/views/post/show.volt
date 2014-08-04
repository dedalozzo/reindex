{% extends "templates/base.volt" %}

{% block content %}
<div id="content">
  {% include "partials/types.volt" %}
  {% set usersBaseUrl = '//'~domainName~'/' %}
  {% set userUrl = usersBaseUrl~post.username %}
  {% set hitsCount = post.getHitsCount() %}
  {% set repliesCount = post.getRepliesCount() %}

  <div id="page-title"><button class="btn btn-star {% if post.isStarred(currentUser) %} active{% endif %}" title="aggiungi ai preferiti"><i class="icon-star icon-largest"></i></button> {{ post.title }}</div>
  <hr class="fade-long">
  <div class="column-left">

    <article id="{{ post.id }}">
      <section class="item-content">
        <div class="item-time">{{ post.whenHasBeenPublished() }}</div>

        {% if post.type == 'book' %}
        <div class="item-meta">
          <img class="img-polaroid" src="//programmazione.it/picture.php?idItem=48456&amp;id=52558c0458cae" alt="Copertina">
          <span>ISBN: </span>{{ post.isbn }}<br>
          <span>Autori: </span>{{ post.authors }}<br>
          <span>Editore: </span>{{ post.publisher }}<br>
          <span>Lingua: </span>{{ post.language }}<br>
          <span>Anno: </span>{{ post.year }}<br>
          <span>Pagine: </span>{{ post.pages }}<br>
          <span>Allegati: </span>{{ post.attachments is empty ? 'nessuno' : post.attachments }}
          <div class="clear"></div>
        </div>
        {% endif %}
        <section class="item-body">
          {{ post.html }}
          {% if post.type == 'book' %}
          <div class="positive">
            {{ markdown.parse(post.positive) }}
          </div>
          <div class="negative">
            {{ markdown.parse(post.negative) }}
          </div>
          {% endif %}
        </section>
        <div class="ghost gutter">
          <ul class="list item-tags">
            <li><a class="tag {{ post.type }}" href="//{{ domainName~'/'~types[post.type] }}/">{{ types[post.type] }}</a></li>
            {% set tags = post.getTags() %}
            {% for tag in tags %}
            <li><a class="tag" href="/tag/">{{ tag['value'] }}</a></li>
            {% endfor  %}
          </ul>
          <section class="item-user pull-right">
            <a class="avatar" href="{{ userUrl }}"><img class="img-polaroid" src="{{ post.getGravatar() }}&s=48" /></a>
            <div class="reputation ext">
              <table>
                <tr><td>2345</td></tr>
                <tr><td>REPUTAZIONE</td></tr>
                <tr><td><span class="badges"><i class="icon-certificate gold"></i> 12<i class="icon-certificate silver"></i> 14<i class="icon-certificate bronze"></i> 122</span></td></tr>
              </table>
            </div>
            <a class="username" href="{{ userUrl }}">{{ post.username }}</a>
          </section>
        </div>
        <ul class="list item-buttons gutter">
          <li><button class="btn btn-like {% if post.didUserVote(currentUser) %} active{% endif %}" title="mi piace"><i class="icon-thumbs-up icon-largest"></i></button></li>
          <li><button class="btn btn-link score">{{ post.getScore() }}</button></li>
          <li class="space"></li>
          <li><button class="btn btn-star {% if post.isStarred(currentUser) %} active{% endif %}" title="aggiungi ai preferiti"><i class="icon-star icon-large"></i></button></li>
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
          <li><a class="btn btn-icon blue" title="migliora la domanda modificandone il contenuto" href="//{{ serverName~'/'~post.id~'/modifica/' }}"><i class="icon-file-text icon-large"></i></a></li>
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
          {% set usersHaveVoted = post.getUsersHaveVoted() %}
          {% for userHasVoted in usersHaveVoted %}
          <li><a href="{{ usersBaseUrl~userHasVoted.id }}"><img class="img-polaroid" title="{{ userHasVoted.username }}" src="{{ userHasVoted.gravatar }}&s=20" /></a></li>
          {% endfor  %}
        </ul>
      </section>
    </article>

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
        <li><button class="btn btn-like {% if reply.didUserVote(currentUser) %} active {% endif %} red" title="la risposta mi piace"><i class="icon-thumbs-up icon-largest"></i></button></li>
        <li><button class="btn btn-link score">{{ reply.getScore() }}</button></li>
        <li><button class="btn btn-accept" title="accetta la risposta"><i class="icon-ok icon-largest"></i></button></li>
        <li class="space"></li>
        <li><button class="btn btn-icon blue" title="condividi la risposta"><i class="icon-link icon-large"></i></button></li>
        <li><button class="btn btn-icon blue"><i class="icon-comment icon-large"></i></button></li>
        <li><button class="btn btn-icon blue" title="segnala un problema riguardante la risposta"><i class="icon-flag icon-large"></i></button></li>
        <li><a class="btn btn-icon blue" title="migliora la risposta modificandone il contenuto" href="//{{ serverName~'/'~post.id~'/modifica/' }}"><i class="icon-file-text icon-large"></i></a></li>
        <li><button class="btn btn-icon red" title="elimina la risposta"><i class="icon-trash icon-large"></i></button></li>
      </ul>
    </div>

    {% endfor %}

  </div> <!-- /column-left -->

  <aside class="column-right">
    <div id="stats"><div>{{ hitsCount }}</div>{% if hitsCount == 1 %} VISUALIZZAZIONE{% else %} VISUALIZZAZIONI{% endif %}</div>
    <div class="banner"><a href="#"><img src="/img/300x250cro.jpeg" /></a></div>
  </aside> <!-- /column-right -->
</div>
{% endblock %}