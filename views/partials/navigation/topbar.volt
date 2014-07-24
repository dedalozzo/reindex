{% include "partials/navigation/main-menu.volt" %}
<nav class="topbar">
  <ul class="list">
    {% include "partials/brand.volt" %}
    <li>
      <ul class="list pills no-gutter">
        {% for item in mainMenu %}
          <li{{ (item['name'] == sectionName) ? ' class="active"' : '' }}><a href="//{{ item['path']~domainName }}"><i class="icon-{{ item['icon'] }}"></i>&nbsp;{{ item['label'] }}</a></li>
        {% endfor %}
        <!-- <li><a href="//{{ domainName }}/tour/">Tour</a></li>
        <li><a href="//{{ domainName }}/aiuto/">Aiuto</a></li> -->

        <!-- <li class="icon"><a href="http://twitter.com/prg_it"><i class="icon-twitter icon-large"></i></a></li>
        <li class="icon"><a href="http://facebook.com/programmazione.it"><i class="icon-facebook icon-large"></i></a></li>
        <li class="icon"><a href="#"><i class="icon-google-plus icon-large"></i></a></li> -->
      </ul>
    </li>
    <li class="space"></li>
    {% if currentUser is defined %}
    <li>
      <ul class="list">
        <li><button class="btn btn-icon blue" title="cerca" data-dropdown="#dropdown-search"><i class="icon-search icon-large"></i></button></li>
        <li>
          <button class="btn btn-icon blue" title="collabora" data-dropdown="#dropdown-plus"><i class="icon-file icon-large"></i></button>
          <div id="dropdown-plus" class="dropdown dropdown-relative dropdown-anchor-right dropdown-tip">
            <ul class="dropdown-menu">
              <li><button><i class="icon-link"></i>Aggiungi un link</button></li>
              <li><button><i class="icon-question"></i>Fai una domanda</button></li>
              <li class="dropdown-divider"></li>
              <li><button><i class="icon-pencil"></i>Scrivi un articolo</button></li>
              <li><button><i class="icon-pencil"></i>Recensisci un libro</button></li>
              <li class="dropdown-divider"></li>
              <li><button><i class="icon-tag"></i>Aggiungi un tag</button></li>
            </ul>
          </div>
        </li>
          {% set userUri = '//utenti.'~domainName~'/'~currentUser.username %}
        <li><button class="btn btn-icon blue" title="messaggi e notifiche" data-dropdown="#dropdown-inbox"><i class="icon-inbox icon-large"></i></button></li>
        <li>
          <button class="btn btn-icon blue" data-dropdown="#dropdown-user"><img class="gravatar" src="{{ currentUser.getGravatar(currentUser.email) }}&s=20"></button>
          <div id="dropdown-user" class="dropdown dropdown-relative dropdown-anchor-right dropdown-tip">
            <ul class="dropdown-menu">
              <li><a href="{{ userUri }}"><i class="icon-home"></i>Timeline</a></li>
              <li class="dropdown-divider"></li>
              <li><a href="{{ userUri }}/profilo/"><i class="icon-user"></i>Profilo</a></li>
              <li><a href="{{ userUri }}/connessioni/"><i class="icon-group"></i>Connessioni</a></li>
              <li><a href="{{ userUri }}/preferiti/"><i class="icon-star"></i>Preferiti</a></li>
              <li><a href="{{ userUri }}/progetti/"><i class="icon-github"></i>Progetti</a></li>
              <li><a href="{{ userUri }}/attivita/"><i class="icon-tasks"></i>Attività</a></li>
              <li class="dropdown-divider"></li>
              <li><button><i class="icon-wrench"></i>Impostazioni</button></li>
              <li><button><i class="icon-gears"></i>Amministrazione</button></li>
              <li class="dropdown-divider"></li>
              <li><a href="//{{ domainName }}/disconnetti/"><i class="icon-signout"></i>Disconnetti</a></li>
            </ul>
          </div>
        </li>
        {% endif %}
      </ul>
    </li>
  </ul>

  <!-- <a href="#" data-toggle="modal" data-target="#myModal">Registrati</a> -->
  <!-- Button trigger modal -->
</nav>

<!-- <form class="topbar-search" method="get" action="search.php" autocomplete="off" name="form_search">
  <input type="search" placeholder="Cerca" autocomplete="on" id="keyword" name="keyword">
  <i class="icon-search"></i>
</form> -->
