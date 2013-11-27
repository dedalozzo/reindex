<div class="topbar">
  <ul class="list topbar-elements">
    <li class="topbar-brand">
      <ul class="list">
        <li>
          <a class="topbar-brand-logo" href="{{ baseUri }}"></a>
        </li>
        <li>
          <a href="{{ baseUri }}">{{ serverName|upper }}</a>
        </li>
      </ul>
    </li>
    <li class="topbar-search">
      <form method="get" action="search.php" autocomplete="off" name="form_search">
        <i class="icon-search"></i>
        <input placeholder="Cerca" autocomplete="on" id="keyword" name="keyword" />
        <i class="icon-remove"></i>
      </form>
    </li>
    <li><a href="{{ baseUri }}/chi-siamo">Chi siamo</a></li>
    <li><a href="{{ baseUri }}/aiuto">Aiuto</a></li>
    <li class="space"></li>
    <li><a href="#" data-toggle="modal" data-target="#myModal">Accedi</a></li>
    <li><a href="#" data-toggle="modal" data-target="#myModal">Registrati</a></li>
    <!-- <li><a href="#"><img class="gravatar" src="http://gravatar.com/avatar/6e8f028adc23ca57bf0e730c4c7f7ae8?d=identicon&s=20" />&nbsp;dedalo</a></li> -->
    <!-- Button trigger modal -->

  </ul>
</div>

<!-- <li class="dropdown-wrapper"><a id="blog" data-toggle="dropdown" href="#">BLOG&nbsp;<span class="toggle" /></a>
<ul class="dropdown" role="list" aria-labelledby="drop3">
  <li><a tabindex="-1" href="#">Pensiero Digitale</a></li>
</ul>
</li> -->
<!-- <li class="dropdown-wrapper clean"><a data-toggle="dropdown" class="icon-user"></i>pippo</a>
  <ul class="dropdown dropdown-arrow" role="list" aria-labelledby="user">
    <li><a tabindex="-1" href="#"><span class="icon-cog" />&nbsp;Impostazioni</a></li>
    <li><a tabindex="-1" href="#"><span class="icon-user" />&nbsp;Profilo</a></li>
    <li class="divider"></li>
    <li><a tabindex="-1" href="#"><span class="icon-signout" />&nbsp;Disconnetti</a></li>
  </ul>
</li> -->
<!-- <li class="dropdown-wrapper clean"><span data-toggle="dropdown" class="icon-search icon-large"></span>
  <ul class="dropdown dropdown-arrow" role="list" aria-labelledby="search">
    <li><a tabindex="-1" href="#"><span class="icon-cog" />&nbsp;Pippo</a></li>
  </ul>
</li> -->
<!-- <li class="clean"><a href="#" class="icon-inbox icon-large"></a></li> -->