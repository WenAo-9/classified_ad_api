# Test_API_and_phpSearchEngine
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
  - GET    http://localhost:80/classified-ads (?page=2)            retourne toute les petites annonces (indiquer la page pour obtenir les résultats suivants)
  - POST   http://localhost:80/classified-ads/Automobile           crée une annonce automobile
  - PUT    http://localhost:80/classified-ads/5                    modifie l'annonce ayant l'id 5
  - DELETE http://localhost:80/classified-ads/5                    supprime l'annonce ayant l'id 5
  - GET    http://localhost:80/car-models (?page=2)                retourne les modèles de voiture autorisés (indiquer la page pour obtenir les résultats suivants)
  - GET    http://localhost:80/car-models?term=RS4 AVANT           effectuera la recherche depuis la valeur saisie
  - GET    http://localhost:80/classified-ad-types                 retourne les types d'annonce existants

# notes
  Parmi les options possibles pour implémenter les types de petite annonce, j'ai longtemps hésité entre une classe abstraite/interface à implémenter sur des classes distinctes     et le CTI mappping doctrine. Le choix s'est finalement porté vers ce dernier, bien que cela implique une jointure supplémentaire, sémantiquement et fonctionnellement il         s'agit véritablement de petites annonces pouvant être traitées et considérées de la même manière indistinctement du type. Le CTI me paraissait plus proche de la réalité         "métier".
