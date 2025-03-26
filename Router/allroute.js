
import Route from "./route.js";
//Définir ici vos routes
export const allRoutes = [
    new Route("/", "Accueil", "/pages/home.php"),
    new Route("/covoiturage", "co-voiturage", "/pages/covoiturage.html"),
    new Route("/login", "Connexion", "/pages/login.php"),
    new Route("/register", "Inscription", "/pages/register.php"),
    new Route("/account", "Mon compte", "/pages/account.html"),
    new Route("/utilisateur", "Espace employe", "/employe/Liste_utilisateur.php"),]
//Le titre s'affiche comme ceci : Route.titre - websitename
export const websiteName = "Eco Ride";