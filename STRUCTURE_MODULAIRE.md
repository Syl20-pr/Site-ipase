# Découpage des gros fichiers en modules

Les 3 fichiers les plus volumineux ont été découpés. Le contenu est
strictement identique (vérifié ligne par ligne) — rien n'a été supprimé,
seulement réorganisé en plusieurs fichiers plus courts.

## 1. Page d'accueil : `index.html` → `index.php` + `includes/`

`index.html` (1822 lignes) est remplacé par `index.php`, qui assemble
9 petits fichiers dans `includes/` :

| Fichier | Contenu |
|---|---|
| `includes/header.php` | `<head>`, en-tête, menu de navigation |
| `includes/page-accueil.php` | Section Accueil |
| `includes/page-apropos.php` | Section À propos |
| `includes/page-filieres.php` | Section Nos filières |
| `includes/page-etudiant.php` | Section Espace étudiant |
| `includes/page-vie-etudiante.php` | Section Vie étudiante |
| `includes/page-actualites.php` | Section Actualités |
| `includes/page-contact.php` | Section Contact |
| `includes/footer.php` | Pied de page, boutons flottants, modale, scripts |

Pour modifier une section (ex. les filières), ouvrez directement
`includes/page-filieres.php` au lieu de chercher dans un fichier de 1800
lignes.

⚠️ **Le site nécessite maintenant PHP pour s'afficher** (il en avait déjà
besoin pour l'inscription/connexion). Ouvrez toujours le site via
`http://localhost/...` (Apache/XAMPP ou `php -S`), jamais en double-cliquant
sur le fichier directement dans l'explorateur Windows.

## 2. JavaScript : `assets/js/main.js` → `assets/js/modules/`

`main.js` (1469 lignes) est découpé en 9 fichiers, chargés dans cet ordre
précis (l'ordre compte, chaque fichier peut dépendre du précédent) :

1. `config.js` — constantes (clés de stockage, URLs des API)
2. `utils.js` — fonctions utilitaires (dates, stockage local, navigation)
3. `excel-export.js` — export Excel des inscriptions locales
4. `admin-embedded.js` — code admin hérité, non utilisé sur cette page
5. `student-dashboard.js` — affichage du tableau de bord étudiant
6. `pdf-generator.js` — génération du PDF de fiche d'inscription
7. `app-init.js` — initialisation (scroll, navigation, recherche, modales)
8. `forms.js` — tous les formulaires (inscription, connexion, contact)
9. `ui-effects.js` — effets visuels et accessibilité

## 3. CSS : `assets/css/style.css` → `assets/css/modules/`

`style.css` (2187 lignes) est découpé en 5 fichiers (`part1-...` à
`part5-...`), chargés dans le même ordre que l'original pour préserver
l'ordre de priorité des styles (cascade CSS). Les noms sont génériques
(le fichier n'avait pas de sections clairement délimitées par des
commentaires), mais chaque fichier indique en en-tête à quelles lignes
il correspondait dans l'original.

Les pages `index.php` et `admin/index.html` chargent désormais ces 5
fichiers au lieu d'un seul.

## Fichiers repérés mais non touchés (hors périmètre de la demande)
- `api/liste_etudiants.php` et `api/contact_messages.php` ne sont appelés
  par aucun script — ce sont d'anciens endpoints devenus inutiles depuis
  que `api/admin.php` gère tout. Vous pouvez les supprimer si vous voulez,
  ils ne sont pas utilisés.
