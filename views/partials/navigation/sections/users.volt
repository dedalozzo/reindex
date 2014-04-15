{% set sectionLabel = 'UTENTI' %}
{% set sectionMenu = [
  ['name': 'privileges', 'path': '/privilegi/', 'label': 'PRIVILEGI', 'title': 'Privilegi'],
  ['name': 'moderators', 'path': '/moderatori/', 'label': 'MODERATORI', 'title': 'Moderatori'],
  ['name': 'bloggers', 'path': '/bloggers/', 'label': 'BLOGGERS', 'title': 'Bloggers'],
  ['name': 'reporters', 'path': '/reporters/', 'label': 'REPORTERS', 'title': 'Reporters'],
  ['name': 'editors', 'path': '/editori/', 'label': 'EDITORI', 'title': 'Editori'],
  ['name': 'voters', 'path': '/votanti/', 'label': 'VOTANTI', 'title': 'Votanti'],
  ['name': 'byName', 'path': '/per-nome/', 'label': 'PER NOME', 'title': 'Utenti in ordine alfabetico'],
  ['name': 'newest', 'path': '/nuovi/', 'label': 'NUOVI', 'title': 'Nuovi utenti'],
  ['name': 'reputation', 'path': '/reputazione/', 'label': 'REPUTAZIONE', 'title': 'Reputazione utenti']
] %}