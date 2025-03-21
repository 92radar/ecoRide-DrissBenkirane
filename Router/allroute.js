
import Route from "./route.js";
//Définir ici vos routes
export const allRoutes = [
    new Route("/", "Accueil", "/pages/home.html"),
    new Route("/covoiturage", "co-voiturage", "/pages/covoiturage.html"),
    new Route("/login", "Connexion", "/pages/login.html"),
    new Route("/register", "Inscription", "/pages/register.html"),
    new Route("/account", "Mon compte", "/pages/account.html"),]
//Le titre s'affiche comme ceci : Route.titre - websitename
export const websiteName = "Eco Ride";