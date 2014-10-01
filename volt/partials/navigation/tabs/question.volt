{% if resource is defined %}
  {% set menu = [
  'activeByTag': 'attive',
  'popularByTag': 'popolari',
  'newestByTag': 'nuove',
  'openByTag': 'aperte',
  'importantByTag': 'importanti'
  ] %}
  {% set resource = resource~'/domande' %}
{% else %}
  {% set menu = [
  'favorite': 'preferite',
  'interesting': 'interessanti',
  'active': 'attive',
  'popular': 'popolari',
  'newest': 'nuove',
  'open': 'aperte',
  'important': 'importanti'
  ] %}
  {% set resource = 'domande' %}
{% endif %}
{% set button = 'nuova' %}