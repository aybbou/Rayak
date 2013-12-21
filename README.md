Rayak : [![Build Status](https://travis-ci.org/aybbou/Rayak.png?branch=master)](https://travis-ci.org/aybbou/Rayak) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/aybbou/Rayak/badges/quality-score.png?s=9d526f372cb959371d1670e7b53ffe145bff8a95)](https://scrutinizer-ci.com/g/aybbou/Rayak/)
=======
Le projet SID.
Installation :
==============
- Exécuter ``git clone https://github.com/aybbou/Rayak`` puis ``cd Rayak``
- Installer les dépendences : ``php composer.phar install`` et donner les paramètres demandés
- Créer la base de données : ``php app/console doctrine:database:create``
- Spécifier le chemin du fichier ``result.xml`` dans ``Rayak/src/Db/CreatorBundle/Command/CreateCommand.php::execute(...)``.
- Exécuter la commande : ``php app/console db:create``
- Pour tester aller dans ``localhost/Rayak/web/app_dev.php``
