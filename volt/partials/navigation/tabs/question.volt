{% if etag is defined %}
  {% set menu = [
  'activeByTag': 'attive',
  'popularByTag': 'popolari',
  'newestByTag': 'nuove',
  'openByTag': 'aperte',
  'importantByTag': 'importanti',
  'infoByTag': 'info'
  ] %}
  {% set resource = etag.name~'/domande' %}
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
{% set buttonLabel = 'nuova' %}