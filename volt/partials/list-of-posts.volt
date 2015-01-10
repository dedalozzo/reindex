{% if entries is defined %}
  {% set baseUrl = '//'~domainName %}
  {% for entry in entries %}
    {% set userUrl = baseUrl~'/'~entry.username %}
    <article id="{{ entry.id }}">
      <hr class="fade-short">
      <ul class="list item-info">
        <li>{{ entry.timestamp }}</li>
        <li>
          {{ entry.hitsCount }}&nbsp;&nbsp;
          <i class="icon-thumbs-up"></i>&nbsp;{{ entry.score }}&nbsp;&nbsp;
          <i class="icon-comments"></i>&nbsp;{{ entry.repliesCount }}
        </li>
      </ul>
      <section class="item-content">
        <div class="ghost half-gutter">
          <a class="item-img" href="{{ entry.url }}"><img class="img-polaroid" src="http://lorempixel.com/g/300/150/technics/{{ loop.index }}"></a>
          <a class="item-title" href="{{ entry.url }}">{{ entry.title }}</a>
          <div class="item-excerpt">{{ entry.excerpt }}</div>
        </div>
        <div class="ghost gutter">
          <ul class="list item-tags">
          </ul>
          <ul class="list item-tags">
            <li><a class="tag {{ entry.type }}" href="{{ baseUrl~'/'~types[entry.type] }}/">{{ types[entry.type] }}</a></li>
            {% set tags = entry.tags %}
            {% for tag in tags %}
            <li><a class="tag" href="{{ baseUrl }}/{{ tag['value'] }}/{% if resource is defined %}{{ types[entry.type] }}/{% endif %}">{{ tag['value'] }}</a></li>
            {% endfor %}
            <li class="space"></li>
          </ul>
          {% if showUser is defined %}
          <section class="item-user pull-right">
            <a class="avatar" href="{{ userUrl }}"><img class="img-polaroid" src="{{ entry.gravatar }}&s=32" /></a>
            <div class="reputation">
              <table>
                <tr><td>2345</td></tr>
                <tr><td><span class="badges"><i class="icon-certificate gold"></i> 12<i class="icon-certificate silver"></i> 14<i class="icon-certificate bronze"></i> 122</span></td></tr>
              </table>
            </div>
            <a class="username" href="{{ userUrl }}">{{ entry.username }}</a>
          </section>
          {% endif %}
        </div>
      </section>
    </article>
  {% elsefor %}
    <div class="alert alert-info">Siamo spiacenti, la ricerca non ha prodotto alcun risultato.</div>
  {% endfor %}
  {% include "partials/pagination.volt" %}
{% endif %}
