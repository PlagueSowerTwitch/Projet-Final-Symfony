
# Projet-Final-Symfony

Auteur(s) : Rat Frédérick / Bernheim Maxime

Description
-----------
Application Symfony minimale de gestion d'une bibliothèque (entités principales : Livre, Categorie, Auteur, Utilisateur, Emprunt) avec une API JSON pour créer/modifier/supprimer/consulter les ressources et gérer les emprunts.

Prérequis
---------
- PHP 8.x installé
- Composer
- Un serveur local (WAMP/XAMPP) ou le serveur Symfony
- Une base de données MySQL/MariaDB (ou autre prise en charge par Doctrine)

Installation (Windows / PowerShell)
----------------------------------
Ouvrez PowerShell dans le dossier du projet (`Symfony_test`) puis :

```powershell
# installer les dépendances
composer install

# (optionnel) si composer.lock ou vendor posent problème :
# Remove-Item composer.lock -Force
# composer install
```

Configuration de la base de données
-----------------------------------
1. Démarrez votre serveur MySQL (via WAMP/XAMPP).
2. Configurez la variable d'environnement `DATABASE_URL` (fichier `.env` ou variables système) :

```
# Exemple dans .env
DATABASE_URL="mysql://db_user:db_pass@127.0.0.1:3306/nom_de_la_bdd"
```

3. Créez la base :

```powershell
php bin/console doctrine:database:create
```

4. Créez les tables :

```powershell
# Option A (rapide, non recommandé pour prod) :
php bin/console doctrine:schema:update --force

# Option B (préféré) :
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

Utilisation
-----------
Vous pouvez lancer le serveur de développement Symfony ou utiliser votre hôte local (WAMP).

```powershell
# Lancer le serveur Symfony (si symfony CLI installé)
symfony server:start -d

# Ou via PHP built-in server (pour tests rapides)
php -S 127.0.0.1:8000 -t public
```

API (endpoints principaux)
--------------------------
Routes créées (format JSON attendu / retour JSON). Remplacez `HOST` par `http://127.0.0.1:8000` ou votre domaine local.

Livres (`src/Controller/LivreController.php`)
- POST /Livre/create
	- Body JSON : { "title": "Titre", "datePublication": "YYYY-MM-DD", "available": true }
	- Retour : 201 Created avec id et résumé
- PATCH|PUT /Livre/{id}/edit
	- Body JSON partiel : { "title": "...", "available": true, "datePublication": "YYYY-MM-DD" }
- DELETE /Livre/{id}/delete
- GET /Livre/{id}/get

Catégories (`src/Controller/CategorieController.php`)
- POST /Categorie/create
	- Body JSON : { "nom": "Nom", "description": "..." }
- PATCH|PUT /Categorie/{id}/edit
- DELETE /Categorie/{id}/delete
- GET /Categorie/{id}/get

Emprunts (`src/Controller/EmpruntController.php`)
- POST /emprunt/demander/{livreId}/{utilisateurId}
	- Crée un emprunt et marque le livre comme indisponible
- POST /emprunt/rendre/{id}
	- Enregistre la date de rendu et rend le livre disponible
- GET /emprunt/utilisateur/{id}
	- Liste les emprunts en cours (DateRendus = null) pour un utilisateur
- GET /emprunt/periode/{auteurId}/{debut}/{fin}
	- Liste les emprunts des livres d'un auteur donné entre deux dates
	- Exemple : GET /emprunt/periode/3/2025-01-01/2025-01-31

Autres contrôleurs
- `UtilisateurController.php` — gestion des utilisateurs
- `AuteurController.php` — gestion des auteurs

Debug & utilitaires
-------------------
- Voir les routes disponibles :

```powershell
php bin/console debug:router --no-interaction
```

- Voir quelle action gère une route :

```powershell
php bin/console debug:router nom_de_la_route
```

Tests et suggestions
--------------------
- Il est recommandé d'ajouter des tests fonctionnels (PHPUnit + client Symfony) pour valider les endpoints.
- Ajouter des validations (Symfony Validator) sur les entités pour garantir l'intégrité des données envoyées.

Notes / bonnes pratiques
------------------------
- En environnement de production, préférez Doctrine Migrations plutôt que `schema:update --force`.
- Protégez les endpoints sensibles avec une authentification (JWT, sessions, etc.).
- Validez et nettoyez les entrées utilisateurs côté API (validator, DTOs).

Contact & contribution
----------------------
Si tu veux que j'ajoute des exemples curl/Postman ou des tests unitaires/fonctionnels, dis-moi lesquels et je les ajoute.


