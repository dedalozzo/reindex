{% include "partials/navigation/section-menu.volt" %}

{% include "partials/navigation/subsection-menu.volt" %}

<div class="column-left">
{% block columnLeft %}

  {% if entries is empty %}
    <div>Siamo spiacenti, la ricerca non ha prodotto alcun risultato.</div>
  {% endif %}

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
        <a class="item-title" href="{{ entry.url }}">{{ entry.title }}</a><br />
        <div class="item-body">{{ entry.excerpt }}</div>
        <ul class="list item-tags">
          {% set tags = entry.tags %}
          {% for tag in tags %}
            <li><a class="tag" href="/tag/">{{ tag['value'] }}</a></li>
          {% endfor  %}
        </ul>
        <div class="item-info">
          <div>{{ entry.whenHasBeenPublished }}, <b>{{ entry.hitsCount }}</b> lettori</div>
          <img class="gravatar" src="{{ entry.gravatar }}&s=32" />
          <div>
            <a href="#">{{ entry.displayName }}</a><br>
            <span><b>2345</b></span><span><i class="icon-certificate gold"></i> 12</span><span><i class="icon-certificate silver"></i> 10</span><span><i class="icon-certificate bronze"></i> 10</span>
          </div>
        </div>
      </div>
    </div>

    <hr>

  {% endfor %}

{% endblock %}
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