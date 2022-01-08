<?php 
require '../vendor/autoload.php';

use GuzzleHttp\Client;

$clientPayment = new Client([
    // Base URI, (url de l'API)
    'base_uri' => 'localhost:3002',
    'timeout' => 2.0,
]);
$clientClient = new Client([
    'base_uri' => 'localhost:3000',
    'timeout' => 2.0
]);

// Add the ClientModule API
$subId = $_GET['subscription_type'];

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $_POST['dateexp'] = "05/22";
    $_POST['payment_method'] = 1;
    print_r($_POST);
    $data = ['subscription_type' => $subId, 'address' => $_POST['address'], 'payment_method' => $_POST['payment_method'], 'client_id' => $_POST['client_id'] ];

    $response = $clientPayment->request('POST', '/invoice', ['headers' => ['Content-Type' => 'application/json'], 'body' => json_encode($data)]);
    $body = get_object_vars(json_decode($response->getBody()));

    header('Location: ./factures.php?transaction_id=' . $body['transaction_id']);
    exit();
}

try {
    $response = $clientPayment->request('GET', "/subscription/$subId");
    if($response->getStatusCode() == 200) {
        $tabSub = json_decode($response->getBody(), true);
    }
} catch (GuzzleHttp\Exception\ServerException $e) {
    $error = "Attention, la page de paiement n'a pas pu chargé. Vous allez être redirigé dans un instant à la page de choix d'abonnement";
}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="scripts/imask.min.js"></script>
    <script src="scripts/paymentform.js"></script>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/responsive.css">
    <title>Payment Form</title>
</head>

<body>
    <div id="paymentform">
        <div id="paymentmod">
            <div id="visa" class="selected" onclick="changeForm(this);"></div>
            <div id="paypal" onclick="changeForm(this);"></div>
            <div id="applepay" onclick="changeForm(this);"></div>
        </div>
        <?php 
        if(isset($error)){
            echo $error;
            exit();
            header('Location : /');
        }
        ?>
        <h2>Order details</h2>
        <p>Product : <?=$tabSub['subscription_name']?> <br/> Price : <?=$tabSub['subscription_price']?> €</p>
        <p>Name : Jean DUPONT <br/> Address : Nice</p>
        <div id="cardform">
            <label for="infos">Paiement avec carte bancaire</label>
            <div id="infoscarte">
                <form action="" method="post">
                    <input type="hidden" name="payment_method" value="visa" />
                    <input type="hidden" name="client_id" value="1" />
                    <input type="hidden" name="address" value="Nice" />
                    <label for="cardnumber">Numéro de carte bancaire</label><br />
                    <input type="text" name="cardnumber" id="cardnumber" placeholder="1234 5678 9012 3456"  inputmode="numeric" />
                    <div class="row">
                        <div class="col">
                            <label for="dateexp">Date d'expiration</label><br />
                            <input type="text" name="dateexp" id="dateexp" placeholder="MM/AAAA" pattern="[0-9]*" inputmode="numeric" />
                        </div>
                        <div class="col">
                            <label for="cvc">CVC</label>
                            <input type="text" name="cvc" id="cvc" placeholder="3 numéros au dos de la carte" required pattern="[0-9]*" inputmode="numeric" />
                        </div>
                    </div>
                    <label for="nom">Nom du titulaire de la carte</label>
                    <input type="text" name="nom" id="nom" maxlength="20" placeholder="Pierre Dupont" required />
                    <!--maybe create a pattern to match only with letters and dash-->
                    <input type="submit" value="Payer" />
                </form>
            </div>
        </div>
        <div id="paypalform">
            <label for="infos">Paiement par PayPal</label><br />
            <a href="https://www.paypal.com/" id="infospaypal">
                <p>Payer avec</p>
                <img src="img/paypal.png" alt="paypal" id="paypalimg" width=100 height=30 />
            </a>
        </div>
        <div id="appleform">
            <label for="infos">Paiement par Apple Pay</label><br />
            <a href="https://www.apple.com/" id="infosapple">
                <p>Payer avec</p>
                <img src="img/applepay.png" alt="applepay" id="appleimg" width=100 height=40 />
            </a>
        </div>
    </div>
    <div id="thanks" style="display:none">
        <div class="col">
            <div class="row">
                <div class="col">
                    <img src="img/validated.jpg" alt="valide" width=50 height=50/>
                    <p>Achat Terminé</p>
                </div>
            </div>
            <div class="row" id="detailsrow">
                <div class="col" id="detailscol">
                    <p>Commande #"insérer n° commande"</p>
                    <hr/>
                    <p>"Insérer méthode de paiement"</p>
                    <hr/>
                    <p>"Insérer date et heure du paiement</p>
                </div>
                <div class="col" id="facturecol">
                    <div class="row">
                        <img src="img/pdf.jpg" alt="pdf" width=100 height=100>
                    </div>
                    <a href="#">Télécharger votre facture</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>