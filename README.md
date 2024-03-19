# redis

1. Description Technique
  Projet réalisé purement en PHP, le choix de cette techno est dû à ma maîtrise du language et des différents CRUD déjà réalisés avec.
</br>

Pour lancer le projet : Sur wamp l'ajouter dans les virtualHost, le voir sur redis/src
</br>
Lancer les tests : dans \wamp\www\redis\redis, lancer la commande : ./vendor/bin/phpunit

3. Description Fonctionnelle
   Application utilisée en interne dans une entreprise (pas de gestion de compte, utilisée pour récupérer la liste des clients)
   Affichage d'une liste de clients (Nom, email, adresse officielle avec l'API gouv, genre)
   Téléchargement CSV de cette liste de client
   Formulaire d'ajout de clients
   Formulaire de modification de clients
   Suppression de client
   Système de sauvegarde de données sur Redis et MySQL

4. Diagrammes
</br>
UseCase
</br>

![image](https://github.com/alexandre1plessis/redis/assets/94174332/b8970d0a-342a-481d-b74b-feb6222c65e3)


</br>

Diagramme d'activité
</br>

![image](https://github.com/alexandre1plessis/redis/assets/94174332/1f7adbb8-5e08-4471-912b-018b36f8a50d)


</br>

Diagramme

</br>

![image](https://github.com/alexandre1plessis/redis/assets/94174332/a64bd513-11e8-4b85-8d61-ccc9273266ee)



