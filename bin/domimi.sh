#!/bin/bash

# suppression de toutes les tables
php bin/console doctrine:schema:drop --full-database --force --no-interaction
# création du schéma de BDD
php bin/console doctrine:migrations:migrate --no-interaction
# validation du schéma de BDD
php bin/console doctrine:schema:validate
