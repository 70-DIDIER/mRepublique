

# 🍽️ M'République – Plateforme de Commande, Livraison et Paiement

Ce projet est une application complète de gestion de restauration incluant :

* ✅ Commande de plats et boissons
* ✅ Paiement mobile (Flooz / TMoney)
* ✅ Livraison avec géolocalisation et validation par code
* ✅ Gestion des utilisateurs (clients, livreurs, admins)
* ✅ Interface web admin (Blade + Tailwind CSS)
* ✅ API RESTful bien structurée et documentée

Développé en Laravel 10/12 et conteneurisé avec Docker.

---

## 🚀 Technologies utilisées

* PHP 8.2 / Laravel 12
* Laravel Sanctum (authentification par token)
* MySQL 8
* Blade + Bootstrap (interface admin)
* Docker & docker-compose
* PayGate API (paiement mobile)
* Scribe (documentation API)
* Postman (tests)
* React Native (frontend mobile)

---

## 🧩 Fonctionnalités principales

* 🔐 Authentification avec rôles : admin, client, livreur
* 🍽️ Gestion des plats et boissons (CRUD)
* 🛒 Système de commande dynamique (panier d’articles)
* 🚚 Livraison avec calcul automatique des frais selon la distance (125 F/km)
* 📍 Géolocalisation du client (lat/lon)
* 🧾 Paiement mobile (intégré via API PayGate)
* ✅ Validation de livraison via code secret
* 📦 Interface admin web moderne
* 📘 Documentation API automatique (via Scribe)

---

## 📦 Installation (local ou Docker)

1. Cloner le projet

```bash
git clone https://github.com/70-DIDIER/mRepublique.git
cd mrepublique
```

2. Copier le fichier d’environnement

```bash
cp .env.example .env
```

ou si tu utilises Docker :

```bash
cp .env.docker .env
```

3. Installer les dépendances

```bash
composer install
```

4. Générer la clé

```bash
php artisan key:generate
```

5. Lancer les migrations

```bash
php artisan migrate
php artisan storage:link
```

6. Lancer en local :

```bash
php artisan serve
```

ou avec Docker :

```bash
docker-compose up -d --build
```

Accès :

* Backend API : [http://localhost:8000/api](http://localhost:8000/api)
* Admin Web : [http://localhost:8000/login](http://localhost:8000/login)
* phpMyAdmin : [http://localhost:8080](http://localhost:8080)

---

## 📘 Documentation API

Générée automatiquement avec Laravel Scribe.

Accessible à :

[http://localhost:8000/docs](http://localhost:8000/docs)

---

## 👤 Rôles & Accès

* Admin : accès à l’interface de gestion via le web
* Client : accès mobile pour commander
* Livreur : accès mobile pour suivre et livrer

Création d’un compte admin via artisan :

```bash
php artisan make:admin

User::create([
  'name' => 'Admin',
  'email' => 'admin@example.com',
  'password' => Hash::make('motdepasse'),
  'role' => 'admin'
]);
```

---

## ✅ Routes principales

| Méthode | Route                        | Fonction                       |
| ------- | ---------------------------- | ------------------------------ |
| POST    | /api/register                | Enregistrement client avec SMS |
| POST    | /api/login                   | Connexion                      |
| GET     | /api/plats                   | Tous les plats                 |
| GET     | /api/plats/categorie/{cat}   | Plats par catégorie            |
| POST    | /api/commandes               | Passer une commande            |
| POST    | /api/paiement                | Lancer un paiement mobile      |
| POST    | /api/livraisons              | Prise en charge par livreur    |
| POST    | /api/livraisons/{id}/valider | Valider la livraison avec code |

Toutes les routes sont protégées par Laravel Sanctum et middleware de rôle.

---

## 🧪 Tests manuels

* Postman utilisé pour tester chaque route.
* Paiements testés avec l’API PayGate (clés API valides).
* Interface admin testée avec utilisateurs réels.
* Authentification, reset password, et notifications fonctionnels.

---

## 📦 Licence

Projet privé développé pour un restaurant réel.