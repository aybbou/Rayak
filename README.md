Rayak :
=======
Le projet SID.
Installation :
==============
- Exécuter ``git clone https://github.com/aybbou/Rayak`` puis ``cd Rayak``
- Installer les dépendences : ``php composer.phar install``
- Copier le fichier ``parameters.yml`` à partir de ``parameters.yml.dist`` : ``cp app/config/parameters.yml.dist app/config/parameters.yml``
- Modifier ``parameters.yml`` suivant vos paramètres de base de données.
- Créer la base de données : ``php app/console doctrine:database:create``
- Spécifier le chemin du fichier ``result.xml`` dans ``Rayak/src/Db/CreatorBundle/Commande/CreateCommand.php::execute(...)``.
- Exécuter la commande : ``php app/console db:create``
- Pour tester aller dans ``localhost/Rayak/web/app_dev.php``
