<?php 
require '../vendor/autoload.php';

use GuzzleHttp\Client;

$clientPayment = new Client([
    // Base URI, (url de l'API)
    'base_uri' => 'http://152.228.163.113:3002/',
    'timeout' => 2.0,
]);
$clientClient = new Client([
    'base_uri' => 'localhost:3000',
    'timeout' => 2.0
]);

$res = $clientPayment->request('GET', 'invoice/' . $_GET['transaction_id']);
$body = get_object_vars(json_decode($res->getBody()));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Facture de Bobby</title>
</head>
<body>
    <div id="thanks">
        <div class="col">
            <div class="row">
                <div class="col">
                    <img src="img/validated.jpg" alt="valide" width=100 height=100/>
                    <p>Achat Terminé</p>
                    <hr/>
                    <p>Abonnement n°XXX- <?php print_r($body[price]);?></p>
                </div>
            </div>
            <div class="row" id="detailsrow">
                <div class="col" id="detailclient">
                    <p><?php print_r($body[client_id]);?> <?php print_r($body[sub_name]);?></p>
                    <hr/>
                    <p><?php print_r($body[address]);?></p>
                </div>
                <div class="col" id="detailscol">
                    <p>Commande #<?php print_r($body[transaction_id]);?></p>
                    <hr/>
                    <p><?php switch ($body[transaction_id]){
                                case 1:
                                    echo "Carte Bleue";
                                case 2 :
                                    echo "Paypal";
                                case 3 :
                                    echo "Apple Pay";
                                default:
                                    echo "Méthode de paiement inconnue";
                            }
                        ?></p>
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
