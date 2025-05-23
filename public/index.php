<?php
require_once '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/header.php';
require_once "/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/mobile-nav.php";
require_once "/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/backend/send.php";

?>

<head>
    <link rel="stylesheet" href="../styles/font.css">

    <link rel="stylesheet" href="../styles/covoiturage.css">


    <link rel="stylesheet" href="../styles/homecopy.css">
    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/research.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js"
        integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous">
    </script>
</head>
<link rel="stylesheet" href="styles/home.css">
<?php if (isset($success)) : ?>
    <div class="alert alert-success">
        <?= $success ?>
    </div>
<?php elseif (isset($error)) : ?>
    <div class="alert alert-danger">
        <?= $error ?>
    </div>
<?php endif; ?>


<style>
    @media screen and (max-width: 968px) {

        .grid-container {
            display: grid;
            grid-template-columns: 1fr 1fr;

        }


        .grid-item {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.2);
            -webkit-backdrop-filter: blur(10px);
            padding: 15px;
            margin: 0;
            box-sizing: border-box;
            border: solid 1px #000000;
            border-radius: 0px;
            font-size: 1.5em;
        }

        .grid-item1 {
            padding: 15px;
            margin: 0;
            box-sizing: border-box;
            border: solid 1px #000000;
            border-radius: 0px;
            font-size: 1.5em;

        }

        .mobile-nav {
            position: fixed;
            bottom: 0;
            z-index: 999;
        }




    }
</style>
<div class="row">
    <div class="column">

        <img src="../images/vision.png"></br>

        <h>VISION</h></br>
        <p>Une plateforme de co-voiturage responsable, soucieuse de l’environnement .</p>

    </div>
    <div class="column">
        <img src="../images/mission.png"></br>
        <h>MISSION</h></br>
        <p> Soutnenir la cause ecologique est notre principale mission. Réduire par trois l'impact environnemental de
            vos
            deplacements est notre objectif. Participer à la revolution verte en utilisant notre plateforme de
            co-voiturage.</p>
    </div>
    <div class="column">
        <img src="../images/valeurs.png"></br>
        <h>VALEURS</h></br>
        <p>Chez <u>ECORIDE</u>, nous prônons des valeurs écologiques pour une consommation responsable, une approche
            pragmatique
            de la résolution de problèmes actuels.</p>
    </div>
</div></br></br></br></br>


<div class="grid-container">
    <div class="grid-item">

        <p>Notre Equipe :</p>
        <p>Nous sommes fier de vous presenter notre equipe engagée dans la revolution verte de nos deplacement. Notre
            team
            se compose d'un community manager, Mateo, en charge du contenu posté sur ce site et les reseaux sociaux. Il
            est
            en charge de répondre a toute vos question concernant notre plateforme et le service qu'elle propose. Nous
            avons
            Léa, Mathilde et Corentin, en charge du developpement et de la maintenance de notre plateforme. C'est grace
            à
            eux si l'application fonctionne bien et nous permet d'offrir un des meilleurs service de covoiturage en
            France.</p>
    </div>
    <div class="grid-item1">

        <p1>Pourquoi nous choisir ?</p1>
        <p></p>
    </div>
    <div class="grid-item1"></br>

        <p1>Nos meilleurs avis</p1>
    </div>
    <div class="grid-item">

        <p>Reduisez votre impact ecologique de 75% en utilisant notre plateforme de co-voiturage. Nous vous permettons
            de
            voyager en toute securité et en respectant l'environnement.
            Nous sommes la plateforme la plus simple et facile d'accés. L'experience que nous avons decide de vous faire
            vivre est exceptionnel dans le domaine du co-voiturage, une plateforme facile à prendre en main, des
            chauffeurs
            qui remplissent nos conditions et standard de voyage pour vous permettre de voyager en toute serenité et
            securité.</p>
    </div>

    <div class="grid-item"><u>Marie, 25 ans</u></br>

        <p>"Je suis absolument ravi de mon expérience de covoiturage ! Non seulement j'ai économisé de l'argent par
            rapport
            à un trajet en train, mais j'ai aussi rencontré des gens formidables. Le conducteur était très sympathique
            et la
            voiture était confortable. L'ambiance était détendue et nous avons eu des conversations intéressantes tout
            au
            long du trajet. C'est une excellente alternative pour voyager, à la fois économique et conviviale. Je
            recommande
            vivement !" - <i>Marie, 25 ans</i></p>
    </div>
    <div class="grid-item1">
        <p1>Vivez votre plus belle experience</p1>
    </div>
</div></br></br>


</div></br></br></br></br>



<div class="card-section">
    <h2>Nos destinations les plus actives</h2>
    </br></br></br></br>
    <div class="card-container">
        <div class="card">
            <img src="/images/paris-6510643_640.jpg" alt="Image 1">
            <h3>Lyon - Paris</h3>
            <p>Prix du voyage: 2 credits</p></br>
            <a href="#">En savoir plus</a>
        </div>
        <div class="card">
            <img src="/images/nice-4625662_640.jpg" alt="Image 2">
            <h3>Montpellier - Nice</h3>
            <p>Prix du voyage: 2 credits</p></br>
            <a href="#">En savoir plus</a>
        </div>
        <div class="card">
            <img src="/images/lyon-4392678_640.jpg" alt="Image 3">
            <h3>Marseille-Lyon</h3>
            <p>Prix du voyage: 2 credits</p></br>
            <a href="#">En savoir plus</a>
        </div>
    </div>
</div></br></br></br></br>
<div>
    <h2>Les avantages</h2>
</div></br></br></br>

<div class="grid-container">

    <div class="grid-item">

        <p>Vos opportunitées :</p>
        <p> Creer du lien social en voyageant avec des personnes qui partagent les memes valeurs que vous. Vous avez la
            possibilite de rencontrer des personnes de tout horizon et de partager des moments inoubliables avec eux.
            Vous
            avez aussi la possibilite de voyager en toute securité et en respectant l'environnement.</p>
    </div>
    <div class="grid-item">

        <p>Notre engagement :</p>
        <p>Soutenir la cause ecologique est notre principale mission. Réduire par trois l'impact environnemental de
            vos
            deplacements est notre objectif. Participer à la revolution verte en utilisant notre plateforme de
            co-voiturage.</p></br>
    </div></br>
</div></br></br></br></br></br>




<div class="first-container container-bottom">
    <h2>OU ALLEZ VOUS ?</h2>
    <form action="" method="post" class="form">
        <div class="recherche-multicriteres text-black">



            <input type="text" id="depart" name="depart" placeholder="Ville de départ">

            <input type="text" id="arrivee" name="arrivee" placeholder="Ville d'arrivée">



            <input type="date" id="date" name="date">

            <button type="submit" name="search" aria-label="Rechercher" style="float: right;">
                <svg xmlns="http://www.w3.or    g/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-search" viewBox="0 0 16 16">
                    <path
                        d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                </svg>
            </button>
        </div>
    </form></br>
</div></br>


<div class="row">
    <div class="column">
        <h2>Merci de votre visite</h2></br>
        <img src="../images/communicate-2.png">
    </div>
    <div class="column">
        <form action="" method="post">
            <fieldset>
                <legend>Nous contacter</legend></br>

                <label for="nom">Nom :</label></br>
                <input type="text" id="nom" name="nom" required></br>

                <label for="email">Email :</label></br>
                <input type="email" id="email" name="email" required></br>

                <label for="objet">Objet :</label></br>
                <input type="text" id="objet" name="objet"></br>

                <label for="message">Message :</label></br>
                <textarea id="message" name="message" rows="5" required></textarea>

                <button type="submit" name="formulaire_contact" id="button"> Envoyer</button>
            </fieldset>
        </form>
    </div>
</div>