{% include "partials/navigation/nav-section.volt" %}

{% include "partials/navigation/nav-submenu.volt" %}

<div class="column-left">

  {% for entry in entries %}
    <div class="item">
      <div class="item-tools">
        <a href="#"><i class="icon-arrow-up icon-large"></i></a>{{ entry.score }}<a href="#"><i class="icon-arrow-down icon-large"></i></a>
        <a href="#"><i class="icon-star-empty icon-large"></i></a>{{ entry.starsCount }}
      </div>
      <div class="item-section">
        <a href="#">{{ entry.publishingType }}</a>
      </div>
      <div class="item-container">
        <a class="item-title" href="#">{{ entry.title }}</a><br />
        <ul class="list item-info">
          <li><img class="gravatar" src="http://www.gravatar.com/avatar/b6799a3261ca303c0b39f991fd9250b4.png" />&nbsp;<a href="#">{{ entry.displayName }}</a><span><b>2345</b></span><span><i class="icon-certificate gold"></i> 12</span><span><i class="icon-certificate silver"></i> 10</span><span><i class="icon-certificate bronze"></i> 10</span></li>
          <li class="space"></li>
          <li>{{ entry.whenHasBeenPublished }}, <b>{{ entry.hitsCount }}</b> lettori</li>
        </ul>
        <div class="item-body">{{ entry.excerpt }}</div>
        <ul class="list item-tags">
          {% set tags = entry.tags %}
          {% for tag in tags %}
            <li><a class="tag" href="/tag/">{{ tag['value'] }}</a></li>
          {% endfor  %}
          <li class="space"></li>
          <li class="icon"><a class="link" href="#">12 commenti</a></li>
        </ul>
      </div>
    </div>

    <hr>

  {% endfor %}

</div> <!-- /column-left -->

<div class="column-right">
{% block columnRight %}

  <div class="banner"><a href="#"><img src="/img/300x250.gif" /></a></div>

  {% include "partials/widgets/counter.volt" %}

  {% include "partials/widgets/updates.volt" %}

  <div class="banner"><a href="#"><img src="/img/300x250cro.jpeg" /></a></div>

  {% include "partials/widgets/tags.volt" %}

  <div class="banner"><a href="#"><img src="/img/300x250cro.jpeg" /></a></div>

  {% include "partials/widgets/badges.volt" %}

{% endblock %}
</div> <!-- /column-right -->