# phpBlocNote

## pré-requis
phpBlocNote millésime 2021 est basé sur Symfony 5.3 et testé sur PHP 8.0 et MySQL 5.7

## installation

### environnement
créer un fichier `.env` à la racine à partir du fichier `.env.dist` et renseigner les paramètres

### créer la base de données (si nécessaire)
`bin/console doctrine:database:create`

### créer la structure de données
`bin/console doctrine:migrations:migrate`

#### préfixes de tables
par défaut, les tables sont préfixées par `sfp_` mais vous pouvez retirer le préfixe ou le modifier à votre guise :) (voir config/services.yaml ligne 29)

### insérer des fixtures (pour tester)
`bin/console doctrine:fixtures:load`

## traductions
par défaut, phpBlocNote est en français (aucun chauvinisme là-dedans ;) ) et traduit en anglais (par moi-même, sorry ^^), libre à vous d'ajouter des langues ou modifier les traductions (ou même les textes français)

## template
le template est basé sur https://www.w3schools.com/w3css/tryw3css_templates_webpage.htm
