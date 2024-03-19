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

</br>

Choix pour l'architecture : une architecture simple en CRUD pour faciliter l'organisation des différentes méthodes utilisées selon les besoins, choisie aussi car c'est une architecture basique simple à mettre en place et que j'ai déjà pratiqué plusieurs fois.
</br>
</br>

Estimation des coûts réels de l'application sur AWS
</br>

Services AWS Principaux:</br>
Amazon EC2:</br>
</br>
    Type d'instance: t3.micro (2 vCPU, 1 GiB de mémoire).
    Coût approximatif: Environ 0,0116 €/heure.

Amazon RDS pour MySQL:</br>
</br>
    Type d'instance: db.t3.micro avec 20 GB de stockage SSD général.
    Coût approximatif: Environ 0,019 €/heure pour l'instance + 0,128 €/GB-mois pour le stockage SSD.

Amazon ElastiCache pour Redis:</br>
</br>
    Type d'instance: cache.t3.micro.
    Coût approximatif: Environ 0,019 €/heure.

Estimation des Coûts Mensuels:</br>
EC2:</br>
</br>
    Total mensuel approximatif: 0,0116 € * 24 heures * 30 jours ≈ 8,35 €.

RDS pour MySQL:</br>
</br>
    Total mensuel approximatif: (0,019 € * 24 * 30) pour l'instance + (20 * 0,128) pour le stockage ≈ 13,70 € + 2,56 € = 16,26 €.

ElastiCache pour Redis:</br>
</br>
    Total mensuel approximatif: 0,019 € * 24 heures * 30 jours ≈ 13,70 €.

Coût Total Estimatif:</br>
</br>
    Coût mensuel total approximatif: 8,35 € (EC2) + 16,26 € (RDS) + 13,70 € (ElastiCache) = 38,31 €.

Notes Importantes:</br>
</br>
    Les prix spécifiques à la région de Paris (eu-west-3) sont utilisés ici comme exemple. Les prix varient entre les régions.
    Ces estimations ne comprennent pas la bande passante sortante, les opérations supplémentaires, ni d'autres services potentiels.
    AWS propose un niveau gratuit pour les nouveaux clients qui peut réduire ou éliminer certains coûts pendant les 12 premiers mois.

