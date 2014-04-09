  <div class="topbar">
    <ul class="list topbar-elements">
      <li class="topbar-brand">
        <ul class="list">
          <li><a class="topbar-brand-logo" href="{{ baseUri }}"></a></li>
          <li><a href="{{ baseUri }}">{{ serverName|upper }}</a></li>
        </ul>
      </li>
      <li class="topbar-search">
        <form method="get" action="search.php" autocomplete="off" name="form_search">
          <i class="icon-search"></i>
          <input placeholder="Cerca" autocomplete="on" id="keyword" name="keyword">
          <i class="icon-remove"></i>
        </form>
      </li>
      <li><a href="{{ baseUri }}/tour/">Tour</a></li>
      <li><a href="{{ baseUri }}/aiuto/">Aiuto</a></li>
      <li class="space"></li>
      <li class="topbar-dropdown">
        <ul>
          <li>
            <span><i class="icon-inbox"></i>&nbsp;<i class="icon-caret-down"></i></span>
            <ul class="">
              <li><a href="#"><i class="icon-question"></i>&nbsp;Fai una domanda...</a></li>
              <li><a href="#"><i class="icon-link"></i>&nbsp;Inserisci un link...</a></li>
              <li><a href="#"><i class="icon-code"></i>&nbsp;Scrivi sul blog...</a></li>
            </ul>
          </li>
        </ul>
      <li class="topbar-dropdown">
        <ul>
          <li>
            <span><i class="icon-plus"></i>&nbsp;<i class="icon-caret-down"></i></span>
            <ul class="">
              <li><a href="#"><i class="icon-question"></i>&nbsp;Fai una domanda...</a></li>
              <li><a href="#"><i class="icon-link"></i>&nbsp;Inserisci un link...</a></li>
              <li><a href="#"><i class="icon-code"></i>&nbsp;Scrivi sul blog...</a></li>
            </ul>
          </li>
        </ul>
      </li>
      {% if user is defined %}
        {% set userUri = 'http://utenti.'~serverName~'/'~user.id %}
      <li><a href="{{ userUri }}"><img class="gravatar" src="{{ user.getGravatar(user.email) }}&s=20" />&nbsp;{{ user.displayName }}</a></li>
      {% else %}
      <li><a href="{{ baseUri }}/accedi/">Accedi</a></li>
      <li><a href="{{ baseUri }}/registrati/">Registrati</a></li>
      {% endif %}
      <!-- <li><a href="#" data-toggle="modal" data-target="#myModal">Registrati</a></li> -->
      <!-- Button trigger modal -->
    </ul>
  </div>