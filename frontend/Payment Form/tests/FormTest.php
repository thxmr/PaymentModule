<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class FormTest extends TestCase
{
    /**
     * @dataProvider DateDataProvider
     */
    public function testDate($date, $response): void
    {
        $this->expectOutputString($response);

        if(!preg_match("/^(0[1-9]|1[012])\/[0-9]{2}$/", $date)) {
            $error = "Format date Invalide";
            echo $error;
        } else {
            $min = date_format(new \DateTime(),"m/y");
            $min = DateTime::createFromFormat("m/y", $min);
            $max = date_format(new \DateTime(),"m/y");
            $max = DateTime::createFromFormat("m/y", $max);
            $max->add(new DateInterval("P3Y"));
            $date = date_create_from_format("m/y", $date);
            //Check if card is not expired
            if ($date < $min) {
                $error = "Carte expirée";
                echo $error;
            }
            else {
                //Check if expiration date isn"t greater dans 3 years
                if ($date > $max) {
                    $error = "Carte valide plus de 3 ans";
                    echo $error;
                } else {
                    echo "valide";
                }
            }
        }
    }

    public function DateDataProvider(): array
    {
        return [
            ["02/22", "valide"],
            ["02/25", "valide"],
            ["12/22", "valide"],
            ["02/21", "Carte expirée"],
            ["03/25", "Carte valide plus de 3 ans"],
            ["aa/bb", "Format date Invalide"],
            ["3/25", "Format date Invalide"],
        ];
    }

    /**
     * @dataProvider CVCDataProvider
     */
    public function testCVC($cvc, $response): void
    {
        $this->expectOutputString($response);

        if(!preg_match("/^[0-9]{3}$/", $cvc))
        {
          $error = "invalide";
          echo $error;
        }
        else{
          echo "valide";
        }
    }

    public function CVCDataProvider(): array
    {
        return [
            ["000", "valide"],
            ["999", "valide"],
            ["365", "valide"],
            ["", "invalide"],
            ["0000", "invalide"],
            ["abc", "invalide"]
        ];
    }

    /**
     * @dataProvider CreditCardProvider
     */
    public function testCreditCard($number, $response): void
    {
        $this->expectOutputString($response);

        $reg = "/^[0-9]{4}[ -\/][0-9]{4}[ -\/][0-9]{4}[ -\/][0-9]{4}$/";

        if ((preg_match($reg, $number)) || (preg_match("/^[0-9]+$/", $number))) {
            $number=str_replace(array(" ", "-", "/"), "", $number);
            $number=intval($number);
            $number=strval($number);

            /* Luhn algorithm number checker - (c) 2005-2008 shaman - www.planzero.org *
            * This code has been released into the public domain */
            $number=preg_replace("/\D/", "", $number);

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
                echo "valide";
            } else {
                $error = "Carte non valide";
                echo $error;
            }
        } else {
            $error = "Format carte de crédit invalide";
            echo $error;
        }
    }

    public function CreditCardProvider(): array
    {
        return [
            ["1738 2929 2828 4637", "valide"],
            ["1738 F929 2828 4637", "Format carte de crédit invalide"],
            ["1738292928284637", "valide"],
            ["FREN", "Format carte de crédit invalide"],
            ["0000 0000 0000 0000", "valide"],
            ["1738-2929-2828-4637", "valide"],
            ["1738/2929/2828/4637", "valide"]
        ];
    }

    /**
     * @dataProvider NameProvider()
     */
    public function testName($name, $response): void
    {
        $this->expectOutputString($response);

        $namePattern = "/^([A-Z][a-z]+([-]{1}[A-Z][a-z]+||[A-Z][a-z]+)([ ]{1}||[ ]{1}de[ ]{1}||[ ]{1}d')[A-Z][a-z]+)$/";
    
        if (!preg_match($namePattern, $name))
        {
          $error = "Nom invalide";
          echo $error;
        }
        else {
          echo "valide";
        }
    }

    public function NameProvider(): array
    {
        return [
            ["Clement Colin","valide"],
            ["Clement","Nom invalide"],
            ["Colin","Nom invalide"],
            ["Clement  Colin","Nom invalide"],
            ["Clement de Colin","valide"],
            ["Clement deColin","Nom invalide"],
            ["Clementde Colin","valide"],
            ["Clementde 2 Colin","Nom invalide"],
            ["Clementde 2 ","Nom invalide"],
            ["666","Nom invalide"]
        ];
    }
}