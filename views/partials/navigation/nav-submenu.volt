{% if subMenu is defined  %}
<ul class="list pills small">
  {% for i, item in subMenu %}
    <li{{ (i == subIndex) ? ' class="active pull-right"' : ' class="pull-right"' }}><a href="{{ basePath~item['link'] }}">{{ item['name'] }}</a></li>
  {% endfor %}
</ul>
{% endif %}