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
print_r($body);
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
                    <p>Abonnement n°XXXXXX - Informations à rajouter - 29.99€</p>
                </div>
            </div>
            <div class="row" id="detailsrow">
                <div class="col" id="detailclient">
                    <p>#idclient Nom Prénom</p>
                    <hr/>
                    <p>Adresse de facturation</p>
                </div>
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
