{% if resource is defined %}
  {% set menu = [
  'activeByTag': 'attivi',
  'popularByTag': 'popolari',
  'newestByTag': 'nuovi'
  ] %}
{% else %}
  {% set menu = [
  'favorite': 'preferiti',
  'interesting': 'interessanti',
  'active': 'attivi',
  'popular': 'popolari',
  'newest': 'nuovi'
  ] %}
{% endif %}
{% block vars %}{% endblock %}