<section class="topbar">
  <nav>
    <div class="pull-left">
      <a class="topbar-brand-logo" href="{{ baseUri }}"></a>
      <a class="topbar-brand-name" href="//{{ domainName }}">PROGRAMMAZIONE.IT</a>
      <form class="topbar-search" method="get" action="search.php" autocomplete="off" name="form_search">
        <input type="search" placeholder="Cerca" autocomplete="on" id="keyword" name="keyword">
        <i class="icon-search"></i>
      </form>
      <a href="{{ baseUri }}/tour/">Tour</a>
      <a href="{{ baseUri }}/aiuto/">Aiuto</a>
    </div>
    <div class="pull-right">
      <span><i class="icon-inbox"></i>&nbsp;<i class="icon-caret-down"></i></span>
      <span><i class="icon-plus"></i>&nbsp;<i class="icon-caret-down"></i></span>
    {% if user is defined %}
      {% set userUri = '//utenti.'~domainName~'/'~user.id %}
      <a href="{{ userUri }}"><img class="gravatar" src="{{ user.getGravatar(user.email) }}&s=20">&nbsp;{{ user.displayName }}</a>
    {% else %}
      <a href="{{ baseUri }}/accedi/">Accedi</a>
      <a href="{{ baseUri }}/registrati/">Registrati</a>
    {% endif %}
    <!-- <a href="#" data-toggle="modal" data-target="#myModal">Registrati</a> -->
    <!-- Button trigger modal -->
      <span class="fixsize"></span>
    </div>
  </nav>
</section>