<link rel="stylesheet" href="../styles/register.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">


<?php




ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/header.php';



$pdo = new PDO("sqlite:/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/ecorideDatabase.db");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



$error = null;




try {
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, date_naissance, email, password, telephone, adresse, ville)
    VALUES (:nom, :prenom, :date_naissance, :email, :password, :telephone, :adresse, :ville)");

    if (isset($_POST['submit'])) {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $dateNaissance = $_POST['date_naissance'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $telephone = $_POST['telephone'];
        $adresse = $_POST['adresse'];
        $ville = $_POST['ville'];

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'date_naissance' => $dateNaissance,
            'email' => $email,
            'password' => $hashedPassword,
            'telephone' => $telephone,
            'adresse' => $adresse,
            'ville' => $ville
        ]);
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}



?>

<section class="vh-100 gradient-custom">
    <div class="container py-5 h-100">
        <div class="row justify-content-center align-items-center h-100">
            <div class="col-12 col-lg-9 col-xl-7">
                <div class="card shadow-2-strong card-registration" style="border-radius: 15px;">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="mb-4 pb-2 pb-md-0 mb-md-5">Formulaire d'inscription</h3>
                        <form action="" method="post">

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div data-mdb-input-init class="form-outline">
                                        <input type="text" name="nom" id="nom_input" placeholder="Nom"
                                            class="form-control form-control-lg" />
                                        <label class="form-label" for="nom_input">Nom</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div data-mdb-input-init class="form-outline">
                                        <input type="text" name="prenom" id="prenom_input" placeholder="Prenom"
                                            class="form-control form-control-lg" />
                                        <label class="form-label" for="prenom_input">Prenom</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4 d-flex align-items-center">
                                    <div data-mdb-input-init class="form-outline datepicker w-100">
                                        <input type="date" name="date_naissance" class="form-control form-control-lg"
                                            id="date_naissance_input" />
                                        <label for="date_naissance_input" class="form-label">Date de
                                            naissance</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4 pb-2">
                                    <div data-mdb-input-init class="form-outline">
                                        <input type="email" name="email" id="emailAddress_input" placeholder="Email"
                                            class="form-control form-control-lg" />
                                        <label class="form-label" for="emailAddress_input">Email</label>
                                    </div>
                                    <div data-mdb-input-init class="form-outline">
                                        <input type="password" name="password" id="password_input"
                                            class="form-control form-control-lg" placeholder="Mot de passe" />
                                        <label class="form-label" for="password_input">Mot de passe</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4 pb-2">
                                    <div data-mdb-input-init class="form-outline">
                                        <input type="tel" name="telephone" id="telephone_input"
                                            class="form-control form-control-lg" placeholder="Numero de tel" />
                                        <label class="form-label" for="telephone_input">Numero de telephone</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4 pb-2">
                                    <div data-mdb-input-init class="form-outline">
                                        <input type="text" name="adresse" id="adresse_input"
                                            class="form-control form-control-lg" placeholder="Adresse" />
                                        <label class="form-label" for="adresse_input">Adresse</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4 pb-2">
                                    <div data-mdb-input-init class="form-outline">
                                        <input type="text" name="ville" id="ville_input"
                                            class="form-control form-control-lg" placeholder="Ville" />
                                        <label class="form-label" for="ville_input">Ville</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 pt-2">
                                <input data-mdb-ripple-init class="btn btn-primary btn-lg" name="submit" type="submit"
                                    value="Sign up" />
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
require_once '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/footer.php';
?>