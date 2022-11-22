# phpBlocNote Bundle

## pré-requis
phpBlocNote millésime 2021 est un bundle pour Symfony 5 (testé sur PHP 8.0)

## installation et utilisation
### composer
Ajouter (ou modifier)
```
    "repositories": {
        "pbn-bundle": {
            "type": "vcs",
            "url": "git@github.com:DelPlop/phpblocnote.git"
        }
    },
```
dans `composer.json` de votre projet puis exécuter `composer require delplop/pbnbundle dev-master`

### templates twig
#### templates
tous les templates utiles se trouvent dans `templates` (y compris les templates surchargés du bundle `DelPlopUserBundle`, dans le sous-dossier `bundles`)

#### layout
surcharger ou étendre le template `templates/pbn.html.twig` du bundle dans le projet principal (dans `templates/bundles/DelPlopPbnBundle`)

#### assets
`php bin/console assets:install public`

### PHP
#### ApplicationUser
copier le fichier `src/Entity/ApplicationUser.php.dist` du bundle dans le projet principal dans `src/Entity/` (et le renommer en `ApplicationUser.php`)

### configuration
#### fichiers yaml
vous aurez à configurer plusieurs fichiers du projet principal. Vous pouvez prendre exemple sur https://github.com/DelPlop/phpblocnote-demo

#### préfixes des tables
par défaut, les tables sont préfixées par `sfp_` mais vous pouvez retirer le préfixe ou le modifier à votre guise :) (voir `config/services.yaml` ligne 29)

## traductions
par défaut, phpBlocNote est en français (aucun chauvinisme là-dedans ;) ) et traduit en anglais (par moi-même, sorry ^^), libre à vous d'ajouter des langues ou modifier les traductions (ou même les textes français)

## apparence
le template est basé sur https://www.w3schools.com/w3css/tryw3css_templates_webpage.htm
