{% set sectionLabel = currentUser.displayName|upper %}
{% set sectionMenu = [
  ['name': 'bounties', 'path': '/bounties/', 'label': 'BOUNTIES', 'title': 'Bounties'],
  ['name': 'activities', 'path': '/attivita/', 'label': 'ATTIVITÀ', 'title': 'Attività'],
  ['name': 'favorites', 'path': '/preferiti/', 'label': 'PREFERITI', 'title': 'Preferiti'],
  ['name': 'connections', 'path': '/connessioni/', 'label': 'CONNESSIONI', 'title': 'Connessioni'],
  ['name': 'reputation', 'path': '/reputazione/', 'label': 'REPUTAZIONE', 'title': 'Reputazione'],
  ['name': 'timeline', 'path': '/timeline/', 'label': 'TIMELINE', 'title': 'Timeline']
] %}