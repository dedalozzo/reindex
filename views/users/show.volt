<div id="title">/*&nbsp;<a href="">{{ doc.title }}</a>&nbsp;*/</div>

<div class="column-left">

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