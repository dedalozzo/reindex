<div id="title">/*&nbsp;<a href="">{{ doc.title }}</a>&nbsp;*/</div>

<div class="column-left">

  <ul class="list toolbar no-border">
    <li class="toolgroup">
      <a href="#" title ="Questo articolo è interessante"><i class="icon-arrow-up"></i></a>
      <span>{{ doc.getScore() }}</span>
      <a href="#" title="Questo articolo è poco chiaro e di dubbia utilità"><i class="icon-arrow-down"></i></a>
    </li>
    <li class="toolgroup">
      <a href="#" title="Aggiungi ai preferiti"><i class="icon-star-empty"></i></a>
      <span>{{ doc.getStarsCount() }}</span>
    </li>
    <li class="toolgroup">
      <a href="#" title="Sottoscrivi la discussione"><i class="icon-eye-open"></i></a>
      <span>{{ doc.getSubscribersCount() }}</span>
    </li>
    <li class="toolgroup">
      <a href="#" title="Condividi su Twitter"><i class="icon-twitter"></i></a>
      <a href="#" title="Condividi su Facebook"><i class="icon-facebook"></i></a>
      <a href="#" title="Condividi su Google+"><i class="icon-google-plus"></i></a>
      <span>{{ doc.getStarsCount() }}</span>
    </li>
    <li class="toolgroup">
      <a href="#"><i class="icon-unlock"></i></a>
      <a href="#"><i class="icon-umbrella"></i></a>
      <a href="#"><i class="icon-pushpin"></i></a>
      <a href="#"><i class="icon-flag"></i></a>
    </li>
    <li class="tool"><a href="#"><i class="icon-trash"></i></a></li>
    <li class="tool"><a href="#"><i class="icon-stackexchange"></i></a></li>
    <li class="tool"><a href="#"><i class="icon-file-text"></i></a></li>
  </ul>

  <div class="item">
    <div class="item-container">
      <div class="item-body">
        {{ doc.html }}
      </div>
      <ul class="list item-tags">
        {% set tags = doc.getTags() %}
        {% for tag in tags %}
          <li><a class="tag" href="/tag/">{{ tag['value'] }}</a></li>
        {% endfor  %}
      </ul>
      <div class="item-info pull-right">
        <div>{{ doc.whenHasBeenPublished() }}, <b>{{ doc.getHitsCount() }}</b> lettori</div>
        <img class="gravatar" src="{{ doc.getGravatar() }}&s=32" />
        <div>
          <a href="#">{{ doc.getDisplayName() }}</a>
          <div class="reputation"><b>2345</b><i class="icon-certificate gold"></i> 12<i class="icon-certificate silver"></i> 10<i class="icon-certificate bronze"></i> 10</div>
        </div>
      </div>
    </div>
  </div>

  <ul class="list tabs">
    <li><span><b>COMMENTI</b></span></li>
    <li class="pull-right"><a href="#">PIÙ VOTATI</a></li>
    <li class="active pull-right"><a href="#">RECENTI</a></li>
  </ul>

  {% for replay in replays %}
  <ul class="list toolbar">
    <li class="tool"><a href="#"><i class="icon-ok"></i></a></li>
    <li class="toolgroup">
      <a href="#"><i class="icon-arrow-up"></i></a>
      <span>{{ replay.getScore() }}</span>
      <a href="#"><i class="icon-arrow-down"></i></a>
    </li>
    <li class="toolgroup">
      <a href="#"><i class="icon-twitter"></i></a>
      <a href="#"><i class="icon-facebook"></i></a>
      <a href="#"><i class="icon-google-plus"></i></a>
    </li>
    <li class="toolgroup">
      <a href="#"><i class="icon-unlock"></i></a>
      <a href="#"><i class="icon-umbrella"></i></a>
      <a href="#"><i class="icon-flag"></i></a>
    </li>
    <li class="tool"><a href="#"><i class="icon-trash"></i></a></li>
    <li class="tool"><a href="#"><i class="icon-stackexchange"></i></a></li>
    <li class="tool"><a href="#"><i class="icon-file-text"></i></a></li>
  </ul>

  <div class="item">
    <div class="item-container">
      <div class="item-body">
        {{ replay.html }}
      </div>
      <div class="item-info pull-right">
        <div>{{ replay.whenHasBeenPublished() }}</div>
        <img class="gravatar" src="{{ replay.getGravatar() }}&s=32" />
        <div>
          <a href="#">{{ replay.getDisplayName() }}</a><br>
          <div>
            <a href="#">{{ entry.displayName }}</a><br>
            <div class="reputation"><b>2345</b><i class="icon-certificate gold"></i> 12<i class="icon-certificate silver"></i> 10<i class="icon-certificate bronze"></i> 10</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {% endfor %}

</div> <!-- /column-left -->

<div class="column-right">

<div class="banner"><a href="#"><img src="/img/300x250cro.jpeg" /></a></div>

</div> <!-- /column-right -->