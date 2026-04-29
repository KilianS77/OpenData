# 🗺️ Activités Melun — OpenData

> Plateforme web interactive permettant d'explorer les équipements sportifs, aires de jeux, événements culturels et points d'intérêt de **Melun Val de Seine**, avec un système social (amis, participations, invitations) basé sur les données ouvertes de la communauté d'agglomération.

---

## 📋 Sommaire

- [Aperçu du projet](#-aperçu-du-projet)
- [Fonctionnalités](#-fonctionnalités)
- [Architecture technique](#-architecture-technique)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Structure du projet](#-structure-du-projet)
- [Sources de données OpenData](#-sources-de-données-opendata)
- [Schéma de base de données](#-schéma-de-base-de-données)
- [Routes de l'application](#-routes-de-lapplication)

---

## 🌍 Aperçu du projet

**Activités Melun** est une application web PHP/MySQL suivant le pattern **MVC** (Modèle-Vue-Contrôleur). Elle consomme les APIs OpenData de Melun Val de Seine pour afficher sur une carte interactive les lieux d'activités de la ville. Les utilisateurs connectés peuvent enregistrer leur participation à des activités, gérer une liste d'amis et s'envoyer des invitations.

---

## ✨ Fonctionnalités

| Fonctionnalité | Accès |
|---|---|
| Carte interactive des équipements, aires de jeux et points d'intérêt | Public |
| Liste des événements culturels et manifestations sportives | Public |
| Synchronisation des données via l'API OpenData | Connecté |
| Enregistrement de participations à des activités | Connecté |
| Voir les participations de ses amis | Connecté |
| Système d'amis (envoi/acceptation de demande par email) | Connecté |
| Envoi d'invitations à des activités | Connecté |
| Paramètres de confidentialité (public / amis seulement) | Connecté |
| Inscription et connexion avec mot de passe bcrypt | Public |

---

## 🏗️ Architecture technique

L'application suit une architecture **MVC classique en PHP natif** avec un point d'entrée unique :

```
index.php  ←─ Routeur principal (paramètre GET ?ctl=...)
   │
   ├── Controleur/   ← Logique métier & traitement des requêtes
   ├── Model/        ← Accès aux données (PDO / MySQL)
   ├── Vue/          ← Templates HTML/PHP (affichage)
   ├── API/          ← Consommation & synchronisation des APIs OpenData
   └── assets/       ← CSS (animations), JS (animations)
```

**Stack :**
- **Backend :** PHP 8.x (natif, pas de framework)
- **Base de données :** MySQL 8.x via PDO
- **Frontend :** HTML, Tailwind CSS (CDN), JavaScript vanilla, Three.js (animation landing)
- **Carte :** Leaflet.js (intégré dans la vue Map)
- **Emails :** PHPMailer (répertoire inclus, prêt à configurer)
- **Configuration :** fichier `.env` (parse_ini_file)

---

## ⚙️ Prérequis

Avant d'installer le projet, assurez-vous d'avoir :

| Outil | Version minimale | Vérification |
|---|---|---|
| PHP | 8.0+ | `php -v` |
| MySQL / MariaDB | 8.0+ | `mysql --version` |
| Serveur web | Apache / Nginx / PHP built-in | — |
| Composer *(optionnel)* | toute version | `composer --version` |
| Git | toute version | `git --version` |

> **Note :** L'application utilise les extensions PHP `pdo_mysql`, `json` et `filter` — toutes activées par défaut dans PHP 8.x.

---

## 🚀 Installation

### Étape 1 — Cloner le dépôt

```bash
git clone https://github.com/KilianS77/OpenData.git
cd OpenData
```

---

### Étape 2 — Créer la base de données

Connectez-vous à MySQL et créez la base de données :

```sql
CREATE DATABASE opendatav2
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

Importez ensuite le schéma complet (tables + données initiales) :

```bash
mysql -u root -p opendatav2 < SQL/opendatav2.sql
```

Ou depuis phpMyAdmin :
1. Sélectionnez la base `opendatav2`
2. Onglet **Importer**
3. Choisissez le fichier `SQL/opendatav2.sql`
4. Cliquez sur **Exécuter**

---

### Étape 3 — Configurer le fichier `.env`

Un fichier `.env.example` est fourni comme modèle. Copiez-le et remplissez vos valeurs :

```bash
# Linux / macOS
cp .env.example .env

# Windows (PowerShell)
Copy-Item .env.example .env
```

Ouvrez ensuite le fichier `.env` et adaptez les valeurs :

```ini
DB_HOST=localhost
DB_NAME=opendatav2
DB_USER=root
DB_PASSWORD=votre_mot_de_passe
```

| Variable | Description | Exemple |
|---|---|---|
| `DB_HOST` | Hôte du serveur MySQL | `localhost` |
| `DB_NAME` | Nom de la base de données | `opendatav2` |
| `DB_USER` | Utilisateur MySQL | `root` |
| `DB_PASSWORD` | Mot de passe MySQL | `motdepasse123` |

> ⚠️ Le fichier `.env` est dans le `.gitignore` — ne le commitez jamais. Seul `.env.example` doit être versionné.

---

### Étape 4 — Lancer le serveur

#### Option A — Serveur de développement PHP intégré (le plus simple)

```bash
# Depuis la racine du projet
php -S localhost:8000
```

Ouvrez votre navigateur sur **http://localhost:8000**

#### Option B — XAMPP / WAMP / MAMP

1. Copiez le dossier du projet dans `htdocs/` (XAMPP) ou `www/` (WAMP)
2. Démarrez Apache et MySQL depuis le panneau de contrôle
3. Accédez à **http://localhost/OpenData/**

#### Option C — Apache (serveur Linux/production)

Créez un VirtualHost dans `/etc/apache2/sites-available/opendata.conf` :

```apache
<VirtualHost *:80>
    ServerName opendata.local
    DocumentRoot /var/www/OpenData
    DirectoryIndex index.php

    <Directory /var/www/OpenData>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Activez le site et redémarrez :

```bash
sudo a2ensite opendata.conf
sudo systemctl restart apache2
```

#### Option D — Nginx

```nginx
server {
    listen 80;
    server_name opendata.local;
    root /var/www/OpenData;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

---

### Étape 5 — Synchroniser les données OpenData

Au premier démarrage, les tables de données (équipements, aires, événements…) sont vides. Connectez-vous à l'application, puis synchronisez les données depuis l'interface :

1. Accédez à la **carte** (`?ctl=map`) ou à la **liste des événements** (`?ctl=evenements`)
2. Cliquez sur le bouton **"Synchroniser les données"** si disponible, ou appelez directement :

```
POST http://localhost:8000/index.php?ctl=activity&action=sync
POST http://localhost:8000/index.php?ctl=evenements&action=sync
```

> Ces endpoints récupèrent les données en temps réel depuis l'API OpenData de Melun Val de Seine et les stockent en base.

---

### Étape 6 — Créer votre compte

1. Accédez à **http://localhost:8000/index.php?ctl=connexion&action=inscription**
2. Remplissez : nom, email, mot de passe (6 caractères minimum)
3. Vous êtes automatiquement connecté après l'inscription

---

## 🔧 Configuration avancée

### PHPMailer (notifications email)

Le répertoire `PHPMailer/` est inclus mais vide. Pour activer les emails :

1. Téléchargez PHPMailer via Composer :
   ```bash
   composer require phpmailer/phpmailer
   ```
2. Ou copiez les fichiers source dans `PHPMailer/`
3. Ajoutez les variables SMTP dans `.env` :
   ```ini
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USER=votre@email.com
   MAIL_PASSWORD=votre_mot_de_passe_app
   MAIL_FROM_NAME=Activités Melun
   ```

### Permissions des fichiers (Linux)

```bash
# Rendre le projet lisible par le serveur web
sudo chown -R www-data:www-data /var/www/OpenData
sudo chmod -R 755 /var/www/OpenData
```

---

## 📁 Structure du projet

```
OpenData/
│
├── index.php                        # Point d'entrée unique (routeur MVC)
├── .env                             # Variables d'environnement (à créer, ignoré par Git)
├── .env.example                     # Modèle de configuration à copier
├── .gitignore                       # Ignore le fichier .env
│
├── API/
│   └── DecodeApi.php                # Consommation & synchronisation des APIs OpenData
│
├── Controleur/
│   ├── ctlActivity.php              # Sync & récupération des activités (AJAX JSON)
│   ├── ctlAmis.php                  # Gestion des amis (liste, demandes, acceptation)
│   ├── ctlConnexion.php             # Inscription, connexion, déconnexion
│   ├── ctlEvenements.php            # Liste & synchronisation des événements
│   ├── ctlParametres.php            # Paramètres de confidentialité (AJAX)
│   └── ctlParticipation.php         # Participations & invitations
│
├── Model/
│   ├── MysqlDB.php                  # Connexion PDO (singleton), lecture du .env
│   ├── UserModel.php                # CRUD utilisateurs, login, bcrypt
│   ├── FriendModel.php              # Système d'amitié (demandes, acceptation)
│   ├── ActivityModel.php            # Requêtes sur les activités
│   ├── InvitationModel.php          # Gestion des invitations
│   ├── ParticipationModel.php       # Gestion des participations
│   └── SettingsModel.php            # Paramètres utilisateur
│
├── Vue/
│   ├── body.php                     # Landing page (animation Three.js)
│   ├── Entetes_Footers/
│   │   ├── entete.php               # Navigation (Tailwind CSS, menu adaptatif)
│   │   └── footer.php               # Pied de page
│   ├── Map/
│   │   └── map.php                  # Carte Leaflet.js interactive
│   ├── vueConnexion/
│   │   ├── v_form_connexion.php     # Formulaire de connexion
│   │   └── v_form_inscription.php   # Formulaire d'inscription
│   ├── vueEvenements/
│   │   └── v_liste_evenements.php   # Liste des événements (du jour + futurs)
│   ├── vueParticipation/
│   │   ├── v_liste_participations.php          # Mes participations
│   │   ├── v_participations_autres.php         # Participations des amis
│   │   ├── v_mes_invitations.php               # Invitations reçues
│   │   ├── v_form_participation.php            # Formulaire de participation
│   │   └── v_form_participation_manifestation.php
│   ├── vueAmis/
│   │   └── v_liste_amis.php         # Liste des amis + demandes en attente
│   └── vueParametres/
│       └── parametres.php           # Page des paramètres
│
├── SQL/
│   └── opendatav2.sql               # Schéma complet de la base de données
│
├── PHPMailer/                       # Répertoire pour PHPMailer (à remplir)
│
├── assets/
│   ├── css/
│   │   └── animations.css           # Animations CSS (fade-in, float, hover)
│   └── js/
│       └── animations.js            # Animations JS (scroll observer, ripple)
│
└── DOC/
    └── api.txt                      # Documentation des endpoints OpenData utilisés
```

---

## 🌐 Sources de données OpenData

L'application consomme les APIs publiques de [Melun Val de Seine](https://data.melunvaldeseine.fr) :

| Dataset | URL API |
|---|---|
| Équipements sportifs | `https://data.melunvaldeseine.fr/api/explore/v2.1/catalog/datasets/equipements-sportifs/records?limit=-1` |
| Aires de jeux | `https://data.melunvaldeseine.fr/api/explore/v2.1/catalog/datasets/aires-de-jeux/records?limit=-1` |
| Agenda culturel 2025-2026 | `https://data.melunvaldeseine.fr/api/explore/v2.1/catalog/datasets/agenda-culturel-communautaire-2025-2026/records?limit=-1` |
| Manifestations sportives Melun | `https://data.melunvaldeseine.fr/api/explore/v2.1/catalog/datasets/manifestations-sportives-melun/records?limit=-1` |
| Calendrier Le Mée-sur-Seine | `https://data.melunvaldeseine.fr/api/explore/v2.1/catalog/datasets/calendrier-des-manifestations-le-mee-sur-seine/records?limit=-1` |
| Points d'intérêt | `https://data.melunvaldeseine.fr/api/explore/v2.1/catalog/datasets/points-d-interets/records?limit=-1` |

---

## 🗄️ Schéma de base de données

```
users                   ← Comptes utilisateurs (bcrypt)
  └── user_settings     ← Paramètres de confidentialité (1-1)
  └── friends           ← Relations d'amitié (pending / accepted / blocked)
  └── participations    ← Participation à une activité (date, heure)
  └── invitations       ← Invitation d'un ami à une activité

aires_jeux              ← Aires de jeux (depuis API)
equipements_sportifs    ← Équipements sportifs (depuis API)
manifestations_sportives← Manifestations sportives (depuis API)
agenda_culturel         ← Agenda culturel communautaire (depuis API)
calendrier_mee_sur_seine← Calendrier Le Mée-sur-Seine (depuis API)
points_interets         ← Points d'intérêt (depuis API)
```

Les tables `participations` et `invitations` utilisent un système générique `activity_type` + `activity_id` permettant de référencer n'importe quel type d'activité sans clé étrangère rigide.

---

## 🔗 Routes de l'application

Toutes les routes passent par `index.php` avec le paramètre `?ctl=` :

| URL | Description | Auth |
|---|---|---|
| `index.php` | Landing page | Non |
| `index.php?ctl=map` | Carte interactive | Non |
| `index.php?ctl=evenements&action=liste` | Liste des événements | Non |
| `index.php?ctl=connexion&action=connexion` | Formulaire de connexion | Non |
| `index.php?ctl=connexion&action=inscription` | Formulaire d'inscription | Non |
| `index.php?ctl=connexion&action=deconnexion` | Déconnexion | Oui |
| `index.php?ctl=participation&action=mes_participations` | Mes participations | Oui |
| `index.php?ctl=participation&action=participations_autres` | Participations des amis | Oui |
| `index.php?ctl=participation&action=mes_invitations` | Mes invitations | Oui |
| `index.php?ctl=amis&action=liste` | Mes amis | Oui |
| `index.php?ctl=parametres&action=afficher_parametres` | Paramètres | Oui |
| `POST index.php?ctl=activity&action=sync` | Sync toutes les activités | Oui |
| `GET index.php?ctl=activity&action=get_activities` | JSON des activités (carte) | Non |
| `POST index.php?ctl=evenements&action=sync` | Sync événements | Oui |
| `GET index.php?ctl=amis&action=get_friends_json` | JSON des amis | Oui |
| `POST index.php?ctl=parametres&action=update_setting` | Màj paramètre (AJAX) | Oui |

---

## 🛡️ Sécurité

- Les mots de passe sont hashés en **bcrypt** (`password_hash` / `password_verify`)
- Les requêtes SQL utilisent des **requêtes préparées PDO** (protection contre les injections SQL)
- Les pages sensibles vérifient la session (`$_SESSION['connect']`)
- Le fichier `.env` est dans le `.gitignore` — ne jamais le committer
- Les sorties utilisateur sont protégées avec `htmlspecialchars()` dans les vues

---

## 🐛 Dépannage

### ❌ "Configuration de base de données manquante"
→ Le fichier `.env` est absent ou mal formaté. Vérifiez que `DB_HOST`, `DB_NAME` et `DB_USER` sont définis.

### ❌ Page blanche ou erreur 500
→ Activez les erreurs PHP en développement en ajoutant au début de `index.php` :
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

### ❌ La carte ne s'affiche pas
→ Vérifiez que les tables `aires_jeux` et `equipements_sportifs` contiennent des données (lancez la synchronisation).

### ❌ Erreur de connexion à la base de données
→ Vérifiez que MySQL est démarré et que les identifiants dans `.env` sont corrects.

---

## 📄 Licence

Ce projet est développé à des fins éducatives et exploite les données ouvertes de la Communauté d'Agglomération Melun Val de Seine, disponibles sous licence ouverte.
