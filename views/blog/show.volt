<div class="page-title">{{ doc.title }}</div>

<div class="column-left">

  <ul class="list toolbar">
    <li class="toolgroup">
      <a class="button" href="#"><i class="icon-arrow-up icon-large"></i></a>
      <span class="pippo">{{ doc.getScore() }}</span>
      <a class="button" href="#"><i class="icon-arrow-down icon-large"></i></a>
    </li>
    <li class="toolgroup">
      <a class="button" href="#"><i class="icon-star-empty icon-large"></i></a>
      <span class="pippo">{{ doc.getStarsCount() }}</span>
    </li>
    <li class="toolgroup">
      <a class="button" href="#"><i class="icon-eye-open icon-large"></i></a>
      <span class="pippo">{{ doc.getSubscribersCount() }}</span>
    </li>
    <li class="toolgroup" style="">
      <a class="button" href="#"><i class="icon-twitter icon-large"></i></a>
      <a class="button" href="#"><i class="icon-facebook icon-large"></i></a>
      <a class="button" href="#"><i class="icon-google-plus icon-large"></i></a>
    </li>
    <li class="toolgroup">
      <a class="button" href="#"><i class="icon-flag icon-large"></i></a>
      <a class="button" href="#"><i class="icon-unlock icon-large"></i></a>
      <a class="button" href="#"><i class="icon-off icon-large"></i></a>
    </li>
    <li class="toolgroup">
      <a class="button" href="#"><i class="icon-pushpin icon-large"></i></a>
      <a class="button" href="#"><i class="icon-stackexchange icon-large"></i></a>
      <a class="button" href="#"><i class="icon-trash icon-large"></i></a>
    </li>
    <li class="toolgroup">
      <a class="button" href="#"><i class="icon-file-alt icon-large"></i></a>
    </li>
  </ul>

  <div class="item">
    <div class="item-body">
      {{ doc.html }}
    </div>
    <ul class="list item-tags">
      {% set tags = doc.getTags() %}
      {% for tag in tags %}
        <li><a class="tag" href="/tag/">{{ tag['value'] }}</a></li>
      {% endfor  %}
    </ul>
    <div class="item-info">
      <div>{{ doc.whenHasBeenPublished() }}, <b>{{ doc.getHitsCount() }}</b> lettori</div>
      <img class="gravatar" src="{{ doc.getGravatar() }}&s=32" />
      <div>
        <a href="#">{{ doc.getDisplayName() }}</a><br>
        <span><b>2345</b></span><span><i class="icon-certificate gold"></i> 12</span><span><i class="icon-certificate silver"></i> 10</span><span><i class="icon-certificate bronze"></i> 10</span>
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
    <li class="toolgroup">
      <a class="button" href="#"><i class="icon-arrow-up icon-large"></i></a>
      <span class="pippo">{{ replay.getScore() }}</span>
      <a class="button" href="#"><i class="icon-arrow-down icon-large"></i></a>
    </li>
    <li class="toolgroup">
      <a class="button" href="#"><i class="icon-twitter icon-large"></i></a>
      <a class="button" href="#"><i class="icon-facebook icon-large"></i></a>
      <a class="button" href="#"><i class="icon-google-plus icon-large"></i></a>
    </li>
    <li class="toolgroup">
      <a class="button" href="#"><i class="icon-unlock icon-large"></i></a>
      <a class="button" href="#"><i class="icon-pushpin icon-large"></i></a>
      <a class="button" href="#"><i class="icon-flag icon-large"></i></a>
      <a class="button" href="#"><i class="icon-trash icon-large"></i></a>
    </li>
    <li class="toolgroup pull-right">
      <a class="button" href="#"><i class="icon-file-alt icon-large"></i></a>
    </li>
  </ul>

  <div class="item">
    <div class="item-body">
      {{ replay.html }}
    </div>
    <div class="item-info">
      <div>{{ replay.whenHasBeenPublished() }}</div>
      <img class="gravatar" src="{{ replay.getGravatar() }}&s=32" />
      <div>
        <a href="#">{{ replay.getDisplayName() }}</a><br>
        <span><b>2345</b></span><span><i class="icon-certificate gold"></i> 12</span><span><i class="icon-certificate silver"></i> 10</span><span><i class="icon-certificate bronze"></i> 10</span>
      </div>
    </div>
  </div>

  <hr>

  {% endfor %}


  <hr>

</div> <!-- /column-left -->

<div class="column-right">

<div class="banner"><a href="#"><img src="/img/300x250cro.jpeg" /></a></div>

</div> <!-- /column-right -->