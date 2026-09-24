# 🎓 Plateforme IPASE - Institut Polyvalent d'Avenir et de Santé

> **Plateforme Web de Présentation, d'Orientation et de Gestion des Inscriptions Scolaires & Étudiantes.**

[![PHP Version](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php)](https://www.php.net/)
[![Database](https://img.shields.io/badge/Database-MySQL%20%2F%20MariaDB-4479A1?logo=mysql)](https://www.mysql.com/)
[![Frontend](https://img.shields.io/badge/Frontend-HTML5%20%2F%20CSS3%20%2F%20JS%20ES6-E34F26?logo=javascript)](https://developer.mozilla.org/fr/docs/Web/JavaScript)
[![License](https://img.shields.io/badge/License-Propriétaire-blue)]()

---

## 📌 Présentation du Projet

La plateforme **IPASE** est une solution web clé en main conçue pour l'**Institut Polyvalent d'Avenir et de Santé**. Elle combine un site vitrine moderne et responsive avec une plateforme de gestion administrative et un portail étudiant interactif.

Elle permet aux futurs étudiants de s'informer sur les cursus offerts, de s'inscrire directement en ligne, d'obtenir une attestation d'inscription au format PDF avec leurs identifiants, tout en offrant au secrétariat et à l'administration un tableau de bord complet de suivi et de gestion.

---

## ✨ Fonctionnalités Principales

### 🌐 1. Site Vitrine & Orientation
* **Présentation de l'Institut :** Mots de la direction, visions, valeurs et cadre d'apprentissage.
* **Catalogue des Filières :** Exploration interactive des pôles de formation :
  * 🩺 **Sciences de la Santé & Social** *(Infirmier, Sage-femme, Laboratoire, Pharmacie, etc.)*
  * 💼 **Gestion, Finance & Administration** *(Comptabilité, GRH, Banque, Audit)*
  * 🚢 **Transport, Logistique & Commerce** *(Transit, Douane, Commerce International)*
  * 💻 **Informatique & Numérique** *(Développement Web, Réseaux, Cybersécurité)*
  * ⚡ **Technique & Industriel** *(Électrotechnique, Froid, Maintenance, BTP)*
  * 📜 **Brevet de Technicien (BT)** et Formations Spécialisées.
* **Téléchargement de Fiches :** Téléchargement direct des fiches de cours et du catalogue officiel au format PDF.
* **Formulaire de Contact :** Messagerie intégrée reliée au panneau d'administration.

### 📝 2. Système d'Inscription en Ligne
* **Génération Automatique de Matricule :** Attribution d'un identifiant unique (ex: `IPASE-2026-0001`).
* **Transactions Sécurisées :** Gestion atomique des données d'inscription en base (PDO Transactions).
* **Génération de Fiche PDF :** Création instantanée d'un reçu/récapitulatif d'inscription personnalisé.
* **Notification par E-mail :** Envoi automatique des identifiants d'accès provisoires via **PHPMailer**.

### 👨‍🎓 3. Portail Étudiant
* Connexion sécurisée par **Matricule ou E-mail** et mot de passe.
* Consultation du statut du dossier (*En attente*, *Validée*, *Annulée*).
* Suivi des paiements et des pièces justificatives.

### ⚙️ 4. Espace Administration & Secrétariat
* **Tableau de bord en temps réel :** Statistiques sur les inscriptions, messages non lus, effectifs par filière.
* **Gestion des Inscriptions :** Validation des dossiers, mise à jour des acomptes et remarques.
* **Exportation de Données :** Export au format Excel / CSV des listes d'étudiants.
* **Messagerie interne :** Traitement et archivage des messages reçus via le formulaire de contact.

---

## 🛠️ Architecture Technique

```text
IPASE_HEBERGER/ipase_ready/
├── admin/                 # Interface et scripts du panneau d'administration
│   ├── app.js             # Logique JavaScript de l'administration
│   └── index.html         # Tableau de bord Administrateur
├── api/                   # Endpoints JSON (Backend PHP)
│   ├── admin.php          # API de gestion administrative
│   ├── auth_etudiant.php  # Authentification des étudiants
│   ├── inscrire_etudiant.php # Traitement et enregistrement des inscriptions
│   └── pdf_generator.php  # Génération des documents PDF (FPDF)
├── assets/                # Ressources statiques
│   ├── css/               # Modules CSS structurés (Hero, Cartes, Dashboard, etc.)
│   ├── img/               # Logos, bannières et visuels des formations
│   └── js/modules/        # Scripts JS modulaires (forms, utils, ui-effects, etc.)
├── config/                # Fichiers de configuration
│   ├── config.example.php # Modèle de configuration
│   └── config.local.php   # Paramètres de connexion DB (ignoré par Git)
├── includes/              # Composants PHP réutilisables (Header, Footer, Pages)
├── mail/                  # Module d'envoi d'e-mails (PHPMailer)
├── ressources/            # Fiches de filières, catalogues PDF et médias
├── sql/                   # Base de données
│   ├── ipase_schema.sql   # Schéma complet et données initiales (2026-2027)
│   └── migration_v2.sql   # Script de mise à jour / migration
└── index.php              # Point d'entrée principal de l'application
```

---

## 🚀 Installation & Déploiement

### Prérequis
* Web Server : **Apache** (avec `mod_rewrite` activé) ou **Nginx**
* Langage : **PHP 8.0** ou supérieur (extensions `pdo_mysql`, `gd`, `mbstring` activées)
* Base de données : **MySQL 5.7+** ou **MariaDB 10.3+**

### Étapes d'Installation

1. **Cloner le projet :**
   ```bash
   git clone git@github.com:Syl20-pr/Site-ipase.git
   cd Site-ipase
   ```

2. **Configurer la Base de Données :**
   * Créez une base de données MySQL nommée `ipase_db`.
   * Importez le fichier SQL principal :
     ```bash
     mysql -u votre_user -p ipase_db < sql/ipase_schema.sql
     ```

3. **Configurer l'Application :**
   * Dans le dossier `config/`, dupliquez `config.example.php` sous le nom `config.local.php` :
     ```php
     <?php
     return [
         'db_host' => 'localhost',
         'db_name' => 'ipase_db',
         'db_user' => 'votre_utilisateur',
         'db_pass' => 'votre_mot_de_passe',
         'smtp_host' => 'smtp.votre-serveur.com',
         'smtp_user' => 'contact@ipase.tg',
         'smtp_pass' => 'mot_de_passe_mail',
     ];
     ```

4. **Accéder à l'application :**
   * Site Vitrine & Inscriptions : `http://localhost/ipase_ready/`
   * Administration : `http://localhost/ipase_ready/admin/`

---

## 🛡️ Sécurité & Bonnes Pratiques

* **Mots de passe :** Hachage fort des mots de passe en base de données avec `password_hash()` (BCRYPT).
* **Requêtes SQL :** Utilisation systématique de requêtes préparées PDO contre les injections SQL.
* **Injections XSS :** Échappement et assainissement des entrées utilisateurs.
* **Données sensibles :** Exclusion stricte du fichier `config.local.php` et des dossiers de logs du contrôle de version (`.gitignore`).

---

## 📄 Licence

Projet réservé à l'**Institut Polyvalent d'Avenir et de Santé (IPASE)**. Tous droits réservés.
