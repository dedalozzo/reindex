<ul class="list tabs">
  <li><span><b>{{ sectionLabel }}</b></span></li>
  <li class="pull-right icon"><a href="http://programmazione.it/rss" class="icon-rss icon-large"></a></li>
  {% for i, item in sectionMenu %}
    <li{{ (i == sectionIndex) ? ' class="active pull-right"' : ' class="pull-right"' }}><a href="{{ basePath~item['link'] }}">{{ item['name'] }}</a></li>
  {% endfor %}
</ul>