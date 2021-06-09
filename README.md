# Projet student

Cette appli permet de gérer une liste d'apprenants, leurs promos ainsi que leurs projets.

## Install

    git clone https://github.com/jibundeyare/src-symfony-p8
    cd src-symfony-p8
    composer install

Après install du projet, créez le fichier `.env.local` et ajoutez-y les variables `APP_ENV` et `DATABASE_URL`.

Créez la BDD avec PhpMyAdmin.

Ensuite créez le schéma de la BDD et injectez les données de test avec la commande :

    bin/dofilo.sh

## Utilisation

    symfony serve

Ensuite visitez la page [http://localhost:8000](http://localhost:8000).

## Cahier des charges

### Student

Cette classe représente un apprenant.

Attributs :

- id : primary key
- firstname : varchar 190
- lastname : varchar 190
- phone : varchar 190, nullable, unique
- creationDate : timestamp, default now
- modificationDate : timestamp, nullable, default now
- deletionDate : timestamp, nullable, default now

Relations :

- projects : many to many
- schoolYear : many to one
- tags : many to many
- user : one to one, uni-directional

### SchoolYear

Cette classe représente une promo d'apprenants.

Attributs :

- id : primary key
- name : varchar 190
- description : text
- startDate : timestamp
- endDate : timestamp
- creationDate : timestamp, default now
- modificationDate : timestamp, nullable, default now
- deletionDate : timestamp, nullable, default now

Relations :

- students : one to many
- teachers : many to many

### Project

Cette classe représente un projet réalisé par des apprenants.

Attributs :

- id : primary key
- name : varchar 190
- description : text, nullable
- deadline : timestamp, nullable
- budget : int, nullable
- creationDate : timestamp, default now
- modificationDate : timestamp, nullable, default now
- deletionDate : timestamp, nullable, default now

Relations :

- clients : many to many
- students : many to many
- tags : many to many
- teacher : many to one, nullable

### Client

Cette classe représente un commanditaire d'un projet.

Attributs :

- id : primary key
- firstname : varchar 190
- lastname : varchar 190
- phone : varchar 190, nullable, unique
- creationDate : timestamp, default now
- modificationDate : timestamp, nullable, default now
- deletionDate : timestamp, nullable, default now

Relations :

- projects : many to many
- user : one to one, uni-directional

### Tag

Cette classe représente une étiquette que l'on pourra associer à un apprenant, un formateur ou un projet.

Attributs :

- id : primary key
- name : varchar 190, unique
- description : text, nullable
- creationDate : timestamp, default now
- modificationDate : timestamp, nullable, default now
- deletionDate : timestamp, nullable, default now

Relations :

- projects : many to many
- students : many to many
- teachers : many to many

### Teacher

Cette classe représente un formateur.

Attributs :

- id : primary key
- firstname : varchar 190
- lastname : varchar 190
- phone : varchar 190, nullable, unique
- creationDate : timestamp, default now
- modificationDate : timestamp, nullable, default now
- deletionDate : timestamp, nullable, default now

Relations :

- projects : one to many
- schoolYears : many to many
- tags : many to many
- user : one to one, uni-directional

### User

Cette classe représente un compte d'utilisateur qui peut se connecter à l'application.

Note : cette classe reprend les attributs par défaut proposés par Symfony et ne possède aucune relation.

Attributs :

- id : primary key
- email : varchar 190, unique
- password : varchar 190
- roles : text

Relations :

- aucune

## Arbre de dépendance des entités

- Client
  - User
- Project
- SchoolYear
- Student
  - SchoolYear
  - User
- Tag
- Teacher
  - User
- User
