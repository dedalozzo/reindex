<nav class="topbar">
  <ul class="list">
    {% include "partials/brand.volt" %}
    <li>
      <form class="topbar-search" method="get" action="search.php" autocomplete="off" name="form_search">
        <input class="" type="search" placeholder="Cerca" autocomplete="on" id="keyword" name="keyword">
        <i class="icon-search"></i>
      </form>
    </li>
    <li><a class="link" href="//{{ domainName }}/tour/">Tour</a></li>
    <li><a class="link" href="//{{ domainName }}/aiuto/">Aiuto</a></li>
    <li class="space"></li>
    {% if user.isMember() %}
      {% set userUri = '//'~domainName~'/'~user.username %}
    <li>
      <button class="btn btn-icon blue" data-dropdown="#dropdown-user"><img class="gravatar" src="{{ user.getGravatar(user.email) }}&s=20"> {{ user.username }}</button>
      <div id="dropdown-user" class="dropdown dropdown-relative dropdown-anchor-right dropdown-tip">
        <ul class="dropdown-menu">
          <li><a href="{{ userUri }}"><i class="icon-home"></i>Timeline</a></li>
          <li class="dropdown-divider"></li>
          <li><a href="{{ userUri }}/profilo/"><i class="icon-user"></i>Profilo</a></li>
          <li><a href="{{ userUri }}/connessioni/"><i class="icon-group"></i>Connessioni</a></li>
          <li><a href="//{{ domainName }}/preferiti/"><i class="icon-star"></i>Preferiti</a></li>
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
    <li><button class="btn btn-icon blue" title="messaggi e notifiche" data-dropdown="#dropdown-inbox"><i class="icon-inbox icon-large"></i></button></li>
    <li>
      <button class="btn btn-icon blue" title="collabora" data-dropdown="#dropdown-plus"><i class="icon-plus icon-large"></i></button>
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
    {% endif %}
  </ul>

  <!-- <a href="#" data-toggle="modal" data-target="#myModal">Registrati</a> -->
  <!-- Button trigger modal -->
</nav>