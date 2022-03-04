# Test_Leboncoin_API
#
Disclaimer : Il s'agissait de mon premier environnement 'tout Docker', pour la configuration tout le mérite revient à K.Dunglas|'symfony-docker' :
https://github.com/dunglas/symfony-docker
#

Pour lancer l'application il suffira de monter les images docker :
  - docker-compose build --no-cache --pull
  - docker-compose up -d

mettre à jour la base de données, puis charger les fixtures :
  - doctrine:schema:update --force
  - doctrine:fixtures:load

Si tout s'est bien déroulé, l'API est en place et prête à être appelée.

Les routes disponibles sont les suivantes : 
# endpoints de la ressource petite Annonce
  - GET         /classified-ads?adtype={type}
  - POST        /classified-ads/{type}    le paramètre type est récupéré depuis la route /classified-ad-types
  - PUT|PATCH   /classified-ads/{id}
  - DELETE      /classified-ads/{id}
# endpoint de la ressource Voiture
  - GET         /car-models?term={nom de modèle}
# endpoint abstrait pour les types d'annonce
  - GET         /classified-ad-types
# exemples
  - GET    http://localhost:80/classified-ads?adtype=Automobile    retourne les annonces automobile
  - GET    http://localhost:80/classified-ads                      retourne toute les petites annonces
  - POST   http://localhost:80/classified-ads/Automobile           crée une annonce automobile
  - PUT    http://localhost:80/classified-ads/5                    modifie l'annonce ayant l'id 5
  - DELETE http://localhost:80/classified-ads/5                    supprime l'annonce ayant l'id 5
  - GET    http://localhost:80/car-models                          retourne les modèles de voiture autorisés
  - GET    http://localhost:80/car-models?term=RS4 AVANT           effectuera la recherche depuis la valeur saisie
  - GET    http://localhost:80/classified-ad-types                 retourne les types d'annonce existants

# notes
  
