# phpBlocNote Bundle

## pré-requis
phpBlocNote millésime 2021 est un bundle pour Symfony 5 (testé sur PHP 8.0)

## installation et utilisation
### composer
`composer require delplop/pbnbundle dev-master`

### templates twig
#### layout
copier le fichier `src/Resources/views/base.html.twig.dist` du bundle dans le projet principal dans `templates/` (et le renommer en `base.html.twig`)

#### assets
`php bin/console assets:install public`

#### templates
tous les templates utiles se trouvent dans `src/Resources/views` (y compris les templates surchargés du bundle `DelPlopUserBundle`)

### PHP
#### ApplicationUser
copier le fichier `src/Entity/ApplicationUser.php.dist` du bundle dans le projet principal dans `src/Entity/` (et le renommer en `ApplicationUser.php`)

#### fixtures
des fictures existent pour tester ou initialiser un projet rapidement

### configuration
#### fichiers yaml
vous aurez à configurer plusieurs fichiers du projet principal. Vous pouvez prendre exemple sur https://github.com/DelPlop/phpblocnote-demo

## traductions
par défaut, phpBlocNote est en français (aucun chauvinisme là-dedans ;) ) et traduit en anglais (par moi-même, sorry ^^), libre à vous d'ajouter des langues ou modifier les traductions (ou même les textes français)

## apparence
le template est basé sur https://www.w3schools.com/w3css/tryw3css_templates_webpage.htm
