# Projet student

Cette appli permet de gérer une liste d'apprenants, leurs promos ainsi que leurs projets.

## Cahier des charges

### Student

Cette classe représente un apprenant.

- id : primary key
- firstname : varchar 190
- lastname : varchar 190
- email : varchar 190, unique
- phone : varchar 190, nullable, unique
- creationDate : timestamp, default now
- modificationDate : timestamp, nullable, default now
- deletionDate : timestamp, nullable, default now

### SchoolYear

Cette classe représente une promo d'apprenants.

- id : primary key
- name : varchar 190
- description : text
- startDate : timestamp
- endDate : timestamp
- creationDate : timestamp, default now
- modificationDate : timestamp, nullable, default now
- deletionDate : timestamp, nullable, default now

### Project

Cette classe représente un projet réalisé par des apprenants.

- id : primary key
- name : varchar 190
- description : text
- deadline : timestamp
- budget : int, nullable
- creationDate : timestamp, default now
- modificationDate : timestamp, nullable, default now
- deletionDate : timestamp, nullable, default now

### Client

Cette classe représente un commanditaire d'un projet.
