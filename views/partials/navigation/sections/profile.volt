{% set sectionLabel = doc.username|upper %}
{% set sectionMenu = [
  ['name': 'activities', 'path': '/attivita/', 'label': 'ATTIVITÀ', 'title': 'Attività'],
  ['name': 'projects', 'path': '/progetti/', 'label': 'PROGETTI', 'title': 'Progetti'],
  ['name': 'favorites', 'path': '/preferiti/', 'label': 'PREFERITI', 'title': 'Preferiti'],
  ['name': 'connections', 'path': '/connessioni/', 'label': 'CONNESSIONI', 'title': 'Connessioni'],
  ['name': 'about', 'path': '/profilo/', 'label': 'PROFILO', 'title': 'Profilo'],
  ['name': 'timeline', 'path': '/timeline/', 'label': 'TIMELINE', 'title': 'Timeline']
] %}