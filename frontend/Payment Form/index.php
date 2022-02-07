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
if(!isset($_GET['subscription_typ']))
    $subId = 1;
else
    $subId = $_GET['subscription_type'];

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $_POST['payment_method'] = 1;        
    $date = $_POST['dateexp'];

        //Check if month between 1-12 and format mm/yy
        if(!preg_match("/^(0[1-9]|1[012])\/[0-9]{2}$/", $date)) {
            $error = "Format date Invalide";
        } else {
            $min = date_format(new \DateTime(),"m/y");
            $min = DateTime::createFromFormat("m/y", $min);

            $max = date_format(new \DateTime(),"m/y");
            $max = DateTime::createFromFormat("m/y", $max);
            $max->add(new DateInterval("P3Y"));

            $date = date_create_from_format('m/y', $date);

            //Check if card is not expired
            if ($date < $min) {
                $error = "Carte expirée";
            }
            else {
                //Check if expiration date isn't greater dans 3 years
                if ($date > $max) {
                    $error = "Carte valide plus de 3 ans";
                } else {
                    print("Valide");
                }
            }
        }

        $name = $_POST['nom'];
        $namePattern = "/^([A-Z][a-z]+([-]{1}[A-Z][a-z]+||[A-Z][a-z]+)([ ]{1}||[ ]{1}de[ ]{1}||[ ]{1}d')[A-Z][a-z]+)$/";

        if (!preg_match($namePattern, $name))
        {
        $error = 'Nom invalide';
        }
        else {
        echo 'valide';
        }

        $cvc = $_POST['cvc'];
        $cvcPattern = "/^[0-9]{3}$/";

        if(!preg_match($cvcPattern, $cvc))
        {
        $error = "cvc invalide";
        }
        else{
        echo 'cvc valide';
        }

        $number = $_POST['cardnumber'];
        $reg = '/^[0-9]{4}[ -\/][0-9]{4}[ -\/][0-9]{4}[ -\/][0-9]{4}$/';

        if ((preg_match($reg, $number)) || (preg_match('/^[0-9]+$/', $number))) {
            $number=str_replace(array(' ', '-', '/'), '', $number);
            $number=intval($number);

            /* Luhn algorithm number checker - (c) 2005-2008 shaman - www.planzero.org *
            * This code has been released into the public domain */
            $number=preg_replace('/\D/', '', $number);

            $number_length=strlen($number);
            $parity=$number_length % 2;

            $total=0;
            for ($i=0; $i<$number_length; $i++) {
                $digit=$number[$i];
                if ($i % 2 == $parity) {
                    $digit*=2;
                    if ($digit > 9) {
                        $digit-=9;
                    }
                }
            $total+=$digit;
            }

            if ($total % 10 == 0) {
                print("Valide");
            } else {
                $error = "Carte non valide";
            }
        } else {
            $error = "Format carte de crédit invalide";
        }
        
        $data = ['subscription_type' => $subId, 'address' => $_POST['address'], 'payment_method' => $_POST['payment_method'], 'client_id' => $_POST['client_id'] ];

    $response = $clientPayment->request('POST', '/invoice', ['headers' => ['Content-Type' => 'application/json'], 'body' => json_encode($data)]);
    $body = get_object_vars(json_decode($response->getBody()));
    if (!isset($error)) {

         $callToClientClient = $clientClient->request
         (
            'POST',
            '/users/subscription', 
            [
                'headers' => ['Content-Type' => 'application/json'], 
                'body' => json_encode([ "id" => $_GET['id'], "subscription" => "standard"])
            ]
        );
        header('Location: ./factures.php?transaction_id=' . $body['transaction_id']);
        exit();
    }
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
        <div id="orderdetails">
            <h2>Order details</h2>
            <p>Product : <?=$tabSub['subscription_name']?> <br/> Price : <?=$tabSub['subscription_price']?> €</p>
            <p>Name : Jean DUPONT <br/> Address : Nice</p>
        </div>
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
                            <input type="text" name="dateexp" id="dateexp" placeholder="MM/AA" pattern="[0-9]{2}\/[0-9]{2}" inputmode="numeric" />
                        </div>
                        <div class="col">
                            <label for="cvc">CVC</label>
                            <input type="text" name="cvc" id="cvc" placeholder="3 numéros au dos de la carte" required pattern="[0-9]{3}" inputmode="numeric" />
                        </div>
                    </div>
                    <label for="nom">Nom du titulaire de la carte</label>
                    <input type="text" name="nom" id="nom" maxlength="20" placeholder="Pierre Dupont" required />
                    <input type="submit" value="Payer" />
                </form>

            </div>
        </div>
        <div id="paypalform">
            <label for="infos">Paiement par PayPal indisponible</label><br />
            <a href="#" id="infospaypal">
                <p></p>
                <img src="img/paypal.png" alt="paypal" id="paypalimg" width=100 height=30 />
            </a>
        </div>
        <div id="appleform">
            <label for="infos">Paiement par Apple Pay indisponible</label><br />
            <a href="#" id="infosapple">
                <p></p>
                <img src="img/applepay.png" alt="applepay" id="appleimg" width=100 height=40 />
            </a>
        </div>
    </div>
</body>
</html>