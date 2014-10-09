{# Menu #}
<ul class="list tabs">
  {% if resource is defined %}
    <li><span>{% if etag is defined %}<a class="btn-star{% if etag.isStarred(currentUser) %} active{% endif %}" title="aggiungi ai preferiti"><i class="icon-star icon-large"></i></a>{% endif %}&nbsp;<b>{{ resource }}</b></span></li>
    {% set controllerPath = resource~'/' %}
  {% else %}
    {% set controllerPath = '' %}
  {% endif %}
  {% if button is defined %}
  <li class="pull-right icon"><a href="http://programmazione.it" class="icon-plus icon-large"> {{ button }}</a></li>
  {% endif %}
  {% for name, actionPath in menu %}
  <li{{ (name == actionName) ? ' class="active pull-right"' : ' class="pull-right"' }}><a href="//{{ domainName~'/'~controllerPath~actionPath }}/">{{ actionPath|minustospace }}</a></li>
  {% endfor %}
</ul>