{% set sectionLabel = 'UTENTI' %}
{% set sectionMenu = [
  ['name': 'privileges', 'path': '/utenti/privilegi/', 'label': 'PRIVILEGI', 'title': 'Privilegi'],
  ['name': 'moderators', 'path': '/utenti/moderatori/', 'label': 'MODERATORI', 'title': 'Moderatori'],
  ['name': 'voters', 'path': '/utenti/votanti/', 'label': 'VOTANTI', 'title': 'Votanti'],
  ['name': 'byName', 'path': '/utenti/per-nome/', 'label': 'PER NOME', 'title': 'Utenti in ordine alfabetico'],
  ['name': 'newest', 'path': '/utenti/nuovi/', 'label': 'NUOVI', 'title': 'Nuovi utenti'],
  ['name': 'reputation', 'path': '/utenti/reputazione/', 'label': 'REPUTAZIONE', 'title': 'Reputazione utenti']
] %}