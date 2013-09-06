<ul class="list tabs">
  <li><span><b>{{ sectionLabel }}</b></span></li>
  <li class="pull-right icon"><a href="http://programmazione.it/rss" class="icon-rss icon-large"></a></li>
  {% for item in sectionMenu %}
    <li{{ (item['name'] == actionName) ? ' class="active pull-right"' : ' class="pull-right"' }}><a href="{{ controllerPath~item['link'] }}">{{ item['label'] }}</a></li>
  {% endfor %}
</ul>