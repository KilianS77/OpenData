# 🗺️ Activités Melun — OpenData

Plateforme web interactive pour explorer les équipements sportifs, aires de jeux, événements et points d'intérêt de **Melun Val de Seine**, avec un système social (amis, participations, invitations).

---

## Stack

- **Backend :** PHP 8.x natif (MVC), PDO / MySQL 8.x
- **Frontend :** Tailwind CSS (CDN), Leaflet.js (carte), Three.js (landing)
- **Config :** fichier `.env` (parse_ini_file)

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
cp .env.example .env   # Linux/macOS
Copy-Item .env.example .env  # Windows PowerShell
```

Éditez `.env` avec vos identifiants MySQL :

```ini
DB_HOST=localhost
DB_NAME=opendatav2
DB_USER=root
DB_PASSWORD=votre_mot_de_passe
```

> ⚠️ Le fichier `.env` est ignoré par Git — ne le commitez jamais.

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

## Structure

```
OpenData/
├── index.php              # Routeur principal (GET ?ctl=...)
├── .env                   # Config locale (ignoré par Git)
├── .env.example           # Modèle de configuration
├── API/DecodeApi.php      # Sync APIs OpenData
├── Controleur/            # Logique métier (connexion, amis, events…)
├── Model/                 # Accès BDD via PDO
├── Vue/                   # Templates HTML/PHP
├── SQL/opendatav2.sql     # Schéma de la base de données
└── assets/                # CSS & JS (animations)
```

---

## Sources OpenData

Données publiques de [Melun Val de Seine](https://data.melunvaldeseine.fr) :
équipements sportifs, aires de jeux, agenda culturel, manifestations sportives, calendrier Le Mée-sur-Seine, points d'intérêt.

---

## Sécurité

- Mots de passe hashés en **bcrypt**
- Requêtes SQL avec **PDO préparé**
- Pages protégées par vérification de session
