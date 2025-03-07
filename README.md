EcoRide est une startup française qui a pour objectif de réduire l'impact environnemental des déplacements en encourageant le covoiturage. EcoRide décide de s'implémenter sur le web pour faire bénéficier de nombreux utilisateurs de son service, c'est-à-dire une plateforme qui permet de rassembler des utilisateurs, chauffeurs ou simples voyageurs, et de leur permettre de voyager en réduisant leur empreinte carbone. Projet initié par José, directeur technique de l'entreprise.

L'objectif principal de la plateforme web est de permettre à des utilisateurs de trouver une solution économique et écologique pour leurs déplacements. Leur ambition est de devenir la principale plateforme de covoiturage pour les voyageurs écolos.

La plateforme est simple d'utilisation, avec une page d'accueil, un menu et une barre de recherche pour choisir l'itinéraire ainsi que les dates de départ.

EcoRide est gérée comme une entreprise et souhaite pouvoir intégrer, à l'utilisation de la plateforme, plusieurs rôles aux privilèges différents. Ainsi, quatre rôles aux privilèges différents ont été identifiés :

Le premier rôle, administrateur, sera en mesure, depuis son espace, de créer et d'administrer les différents comptes employés. Il sera aussi en mesure de visionner deux graphiques reflétant l'activité économique de la plateforme.
Le deuxième rôle, employé, aura les privilèges d'un community manager. Sur la plateforme, il pourra valider, supprimer et publier les avis des voyageurs. L'employé disposera d'informations clés pour visionner les voyages passés, telles que le numéro du covoiturage, les adresses e-mail ou encore le pseudo des intéressés.
Le troisième rôle est celui de l'utilisateur. Après son inscription ou son authentification, depuis son espace, il pourra choisir s'il veut être chauffeur, simple voyageur, ou les deux. L'utilisateur a seulement accès aux fonctionnalités essentielles de la plateforme. Après inscription, il peut réserver un voyage, organiser un voyage, ou publier un avis sur un voyage qu'il a déjà effectué.
Le quatrième rôle est celui du visiteur. Le visiteur peut avoir accès à la page d'accueil et à tous les détails d'un voyage (comme les avis, la marque du véhicule, le type d'énergie utilisé). S'il veut participer, une inscription ou une authentification sera requise.
Le site devra être construit en suivant les bonnes pratiques en matière de développement et de sécurité. Il se veut être un produit « secure by design ».


Le projet se fait sur MacOs 15.3.1, avec manager OS-X (XAMP) pour travailler en local avec VS code et phpMyAdmin.
l'utilisation de github desktop et de VScode comme moyen d'organiser les differente branches, commit et merge du projet.

Le projet comporte une branche principale ainsi qu'une branche de développement.
- Chaque fonctionnalité sera une branche issue de la branche
développement, après test, le merge sera effectué vers la
branche développement
- Une fois que la branche développement est correctement
testée, il faudra effectuer un merge vers la branche principale


Technologies utilisées :

Front-end : HTML5, CSS/SCSS, Bootstrap et JavaScript.
Back-end : PHP, le framework Symfony sera utilisé.
Base de données : MySQL MariaDB 10.4.28.
Outils et déploiement : Git, Composer, VS Code et Heroku.


