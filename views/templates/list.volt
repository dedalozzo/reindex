{% include "partials/navigation/section-menu.volt" %}

{% include "partials/navigation/subsection-menu.volt" %}

<div class="column-left">
{% block columnLeft %}
  {% set usersBaseUrl = 'http://utenti.'~serverName~'/' %}

  {% for entry in entries %}
    {% set userUrl = usersBaseUrl~entry.userId %}
    <div class="item">
      <div class="item-tools">
        <a href="#"><i class="icon-arrow-up icon-large"></i></a>{{ entry.score }}<a href="#"><i class="icon-arrow-down icon-large"></i></a>
        <a href="#"><i class="icon-comments icon-large"></i></a>{{ entry.replaysCount }}
      </div>
      <div class="item-section">
        <a href="#">{{ entry.publishingType }}</a>
      </div>
      <div class="item-container shift">
        <a class="item-title" href="{{ entry.url }}">{{ entry.title }}</a><br />
        <div class="item-body">{{ entry.excerpt }}</div>
        <ul class="list item-tags">
          {% set tags = entry.tags %}
          {% for tag in tags %}
            <li><a class="tag" href="/tag/">{{ tag['value'] }}</a></li>
          {% endfor  %}
            <li class="space"></li>
        </ul>
        <div class="item-info pull-right">
          <div>{{ entry.whenHasBeenPublished }}, <b>{{ entry.hitsCount }}</b> lettori</div>
          <a href="{{ userUrl }}"><img class="gravatar" src="{{ entry.gravatar }}&s=32" /></a>
          <div>
            <a href="{{ userUrl }}">{{ entry.displayName }}</a>
            <div class="reputation"><b>2345</b><i class="icon-certificate gold"></i> 12<i class="icon-certificate silver"></i> 10<i class="icon-certificate bronze"></i> 10</div>
          </div>
        </div>
      </div>
    </div>

    <hr>

  {% elsefor %}
    <div>Siamo spiacenti, la ricerca non ha prodotto alcun risultato.</div>
  {% endfor %}

{% endblock %}
</div> <!-- /column-left -->

<div class="column-right">
{% block columnRight %}

  <div class="banner"><a href="#"><img src="/img/300x250.gif" /></a></div>

  {% include "partials/widgets/counter.volt" %}

  {% include "partials/widgets/tags.volt" %}

  <div class="banner"><a href="#"><img src="/img/300x250cro.jpeg" /></a></div>

  {% include "partials/widgets/badges.volt" %}

{% endblock %}
</div> <!-- /column-right -->