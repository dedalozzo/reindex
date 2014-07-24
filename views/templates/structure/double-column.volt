{% block sectionMenu %}{% endblock %}

{% include "partials/navigation/main-menu.volt" %}
{% include "partials/navigation/section-menu.volt" %}
{% include "partials/navigation/section-submenu.volt" %}

<div class="column-left">
{% block columnLeft %}
  {% set showUser = TRUE %}
  {% include "partials/list-of-posts.volt" %}
{% endblock %}
</div> <!-- /column-left -->

<aside class="column-right">
{% block columnRight %}
  <div class="banner"><a href="#"><img src="/img/300x250.gif" /></a></div>
  {% include "partials/widgets/stats.volt" %}
  {% include "partials/widgets/tags.volt" %}
  <div class="banner"><a href="#"><img src="/img/300x250cro.jpeg" /></a></div>
  {% include "partials/widgets/badges.volt" %}
{% endblock %}
</aside> <!-- /column-right -->