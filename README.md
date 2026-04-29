# 🗺️ Activités Melun — OpenData

> Plateforme web interactive pour explorer les équipements sportifs, aires de jeux, événements et points d'intérêt de **Melun Val de Seine**, avec un système social (amis, participations, invitations).

🌐 **Site en production :** [activites-melun.alwaysdata.net](https://activites-melun.alwaysdata.net)

---

## Stack

| Couche | Technologie |
|---|---|
| Backend | PHP 8.x natif (MVC), PDO / MySQL 8.x |
| Frontend | Tailwind CSS (CDN), Leaflet.js (carte), Three.js (landing) |
| Mails | PHPMailer — invitations et notifications |
| Config | Fichier `.env` via `parse_ini_file` |
| Hébergement | AlwaysData |

---

## Fonctionnalités

- 🗺️ **Carte interactive** — Leaflet.js avec marqueurs filtrables par catégorie
- ⚽ **Équipements sportifs** — données synchronisées depuis l'OpenData de Melun Val de Seine
- 📅 **Événements** — agenda culturel et sportif, mise à jour via API publique
- 👥 **Système social** — amis, participations aux événements, invitations par email
- 🔐 **Authentification** — inscription, connexion, sessions sécurisées (bcrypt + PDO préparé)
- 🌐 **Landing animée** — page d'accueil avec animation Three.js

---

## Installation

### 1. Cloner le dépôt

```bash
git clone https://github.com/KilianS77/OpenData.git
cd OpenData
```

### 2. Créer la base de données

```bash
mysql -u root -p -e "CREATE DATABASE opendatav2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p opendatav2 < SQL/opendatav2.sql
```

### 3. Configurer l'environnement

```bash
cp .env.example .env          # Linux/macOS
Copy-Item .env.example .env   # Windows PowerShell
```

Éditez `.env` avec vos identifiants MySQL :

```env
DB_HOST=localhost
DB_NAME=opendatav2
DB_USER=root
DB_PASSWORD=votre_mot_de_passe
```

> ⚠️ Le fichier `.env` est ignoré par Git — ne le commitez **jamais**.

### 4. Lancer le serveur

```bash
php -S localhost:8000
```

Accédez à **http://localhost:8000**

### 5. Synchroniser les données OpenData

Au premier démarrage, lancez la synchronisation depuis la carte ou via :

```
POST index.php?ctl=activity&action=sync
POST index.php?ctl=evenements&action=sync
```

---

## Structure du projet

```
OpenData/
├── index.php              # Routeur principal (GET ?ctl=...)
├── .env                   # Config locale (ignoré par Git)
├── .env.example           # Modèle de configuration
├── API/DecodeApi.php      # Sync depuis les APIs OpenData
├── Controleur/            # Logique métier (connexion, amis, events…)
├── Model/                 # Accès BDD via PDO préparé
├── Vue/                   # Templates HTML/PHP
├── SQL/opendatav2.sql     # Schéma de la base de données
└── assets/                # CSS & JS (animations)
```

---

## Sources OpenData

Données publiques de [Melun Val de Seine](https://data.melunvaldeseine.fr) :

- Équipements sportifs
- Aires de jeux
- Agenda culturel
- Manifestations sportives
- Calendrier Le Mée-sur-Seine
- Points d'intérêt

---

## Sécurité

- Mots de passe hashés en **bcrypt**
- Requêtes SQL avec **PDO préparé** (zéro injection SQL)
- Pages protégées par vérification de session

---

## Équipe

| | Pseudo | GitHub |
|---|---|---|
| 🔴 | KilianS77 | [@KilianS77](https://github.com/KilianS77) |
| 🔵 | GermainDK | [@germaindk](https://github.com/germaindk) |
| 🟢 | AlexxisDS | [@alexxisds](https://github.com/alexxisds) |
| 🟡 | KohaiJ | [@KohaiJ](https://github.com/KohaiJ) |

---

## Note finale

Ce projet est objectivement le meilleur du lot — et non, on n'est pas neutres. On n'a pas prétendu résoudre une question éthique complexe ni sauver la biodiversité. On a fait **un beau site fonctionnel avec de vraies données publiques**, une carte interactive, un système social, et du PHP qui tient la route.

Les fashion sur le trottoir.

---

## 🏎️ Activité choisie : Karting

On veut faire du karting. C'est dit, c'est clair, c'est final.

On aurait pu proposer un escape game, une randonnée, ou un atelier poterie — mais on est pas des woke. On veut de la vitesse, du bruit, et gagner contre nos potes. Du karting, s'il vous plaît.

```
🔴 KilianS77 ───🏎️
🔵 GermainDK ──🏎️
🟢 AlexxisDS ─🏎️
🟡 KohaiJ ───🏎️
              🏁
```

---

*© 2026 — Activités Melun · OpenData · Melun Val de Seine*