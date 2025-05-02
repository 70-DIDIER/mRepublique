## ✅ CE QU’ON A DÉJÀ FAIT (Backend Laravel 12 + Sanctum)

✔ Authentification avec Laravel Sanctum  
✔ Enregistrement d’un nouvel utilisateur  
✔ Gestion des rôles (client, livreur, admin) avec middleware  
✔ Connexion avec email ou téléphone  
✔ Table users complète (photo, téléphone, adresse, rôle, vérification, etc.)


✔ CRUD Plat (image, update, delete, etc.)  
✔ CRUD Boisson (copié de Plat, même structure)  
✔ Stockage d’image dans storage + accès via public  

## Processus pour démarrer le projet
- Cloner le projet
- Metrre à jour le projet avec la commande $composer update
- Création de la base de données
- Modification du contenus et renommer le fichier .en.exemple en .env
- Changement de DB_PORT = 3308 en DB_PORT = 3306
- Ensuite on lance le projet

## Processus pour démarrer le projet
- Cloner le projet
- Metrre à jour le projet avec la commande $composer update
- Création de la base de données
- Modification du contenus et renommer le fichier .en.exemple en .env
- Changement de DB_PORT = 3308 en DB_PORT = 3306
- Ensuite on lance le projet

## About Laravel
✔ Création de commande :  
   ▪ choix plats et boissons  
   ▪ lien avec client  
   ▪ enregistrement des articles  
   ▪ calcul automatique du montant total  
   ▪ géolocalisation du client (latitude, longitude)  
   ▪ calcul distance + frais livraison (125 FCFA/km)  
   ▪ champs : commentaire, adresse livraison  

✔ Réponse enrichie de la commande avec :  
   ▪ articles détaillés  
   ▪ montant formaté  
   ▪ distance en km  
   ▪ date formatée

---

## 🔧 CE QU’IL RESTE À FAIRE (Backend uniquement)

### 🔐 Auth & sécurité
☐ Vérification par SMS (vérification du code après inscription)  
☐ Blocage du login si pas vérifié  
☐ Réinitialisation de mot de passe (optionnel)  

### 🛒 Commande
☐ Ajout d’un statut "payé" ou "annulé"  
☐ Route pour annuler une commande  
☐ Route admin pour voir toutes les commandes  
☐ Route pour voir les commandes d’un client spécifique  
☐ Ajout d’un moyen de paiement (enum ou string : espèce, mobile money...)

### 🛵 Livraison (livreur)
☐ Table livraisons (livreur_id, commande_id, statut, heure_livraison...)  
☐ Assigner une commande à un livreur  
☐ Route "mes livraisons" côté livreur  
☐ Changement du statut (en cours, livré)  
☐ Suivi en temps réel (optionnel plus tard)

### 🛠️ Admin
☐ Tableau des commandes de tous les clients  
☐ CRUD utilisateurs (ou soft delete)  
☐ Statistiques (facultatif : nombre de commandes / jour...)

---

## 📌 SUGGESTIONS BONUS

✅ Créer un dashboard JSON pour les admins avec :

- commandes du jour
- top plats vendus
- chiffre d’affaires du jour

✅ Créer un champ "est_ouvert" pour le restaurant  
✅ API pour géolocaliser automatiquement les livreurs (tracking GPS mobile)

—

Tu veux que je t’aide à noter tout ça dans un fichier TODO.txt ou roadmap backend Laravel ? 😎  
Et dis-moi sur quoi tu veux passer ensuite : livraison ? statut ? admin ?