
# Project details
## Users stories
As a user when it came to checkout, i want to be able to select différent methods

As a user ask for all my invoices

As a user i want to be able to get détail about on specific invoice

## Working developpers

<ul>
  <li>thxm_r : Thomas Roy</li>
  <li>florianbrrl : Florian Barrali</li>
  <li>ceryuz : Enzo Rocamora</li>
  <li>ALonelyDuck : Clément Colin</li>
  <li> florian-verheecke : Florian Verheecke </li>
</ul>

## Softwares used

Comunication : Discord

## Development environment

For this project we used **Visual Studio Code** as an integrated development environment, but you can use another one if you'd like because we didn't use any extensions, packages, and plugins exlusive to it.

-You can download it from the Microsoft website https://aka.ms/visualstudiocode, the version is not important.

**IntelliJ IDEA** was also used for javascript.

-You can download it from this website https://www.jetbrains.com/fr-fr/idea/download/, the version is not important.

### Technologies used

<ul>
  <li>PHP : 7.4.9</li>
  <li>Composer : 2.2.4</li>
  <li>nodejs : 16.7.0</li>
  <li>PostgreSQL : 14.1</li>
</ul>

# Setup the environnent for the frontend

Taking into account the fact that the Payment Module does not have a complex UI, we choose to implement the front in HTML/CSS/JavaScript, in order to not allow more complexity than this UI needs. Using React JS for such a simple interface would have been too much. So we decided to implement it in those languages, allowing it to be easily integrated to the rest of the app.

## PHP

Donwload the [Current Stable PHP 7.4.27](https://www.php.net/downloads.php#gpg-7.4) version from the official website.
For Windows select `Windows downloads` and choose the `VC15 x64 Non Thread Safe` zip file.

Extract the zip and add the directory to your PATH environment variable.

## Composer

A Dependency Manager for _PHP_
Download the latest version [here](https://getcomposer.org/), make sure you already have php installed before this step.

Once installed and linked to php make sur that the directory is added to your PATH environment variable.

In Visual Studio Code, open the project and locate `composer.json` that is in the `frontend` folder, right click on it and open it with the integrated terminal.

Now in the Terminal you want to enter the following command :

    composer install

It will install all the necessaries packages and dependencies required for the project.

    Installing dependencies from lock file (including require-dev)
    Verifying lock file contents can be installed on current platform.
    Package operations: 6 installs, 0 updates, 0 removals
      - Downloading psr/http-message (1.0.1)
      - Downloading psr/http-client (1.0.1)
      - Downloading ralouphie/getallheaders (3.0.3)
      - Downloading guzzlehttp/psr7 (1.8.3)
      - Downloading guzzlehttp/promises (1.5.1)
      - Downloading guzzlehttp/guzzle (7.0.0)
      - Installing psr/http-message (1.0.1): Extracting archive
      - Installing psr/http-client (1.0.1): Extracting archive
      - Installing ralouphie/getallheaders (3.0.3): Extracting archive
      - Installing guzzlehttp/psr7 (1.8.3): Extracting archive
      - Installing guzzlehttp/promises (1.5.1): Extracting archive
      - Installing guzzlehttp/guzzle (7.0.0): Extracting archive
    Generating autoload files
    2 packages you are using are looking for funding.
    Use the `composer fund` command to find out more!

# Setup the environnent for the backend

## Bypass

For Front-End devs or the other groups, installing PostgreSQL and setting up the API is not the best way to do things.

To fix that, Enzo hosted on a private server a database with the API for remote access.

To connect to the remote API :

-Open the `index.php` file located in the `\frontend\Payment Form` folder.

-Then locate the `$clientPayment` variable on line 6 :

    $clientPayment = new Client([
        // Base URI, (url de l'API)
        'base_uri' => 'localhost:3002/',
        'timeout' => 2.0,
    ]);

-Replace the `base_uri` from `localhost:3002` to `http://152.228.163.113:3002/`, it should look like that:

    $clientPayment = new Client([
        // Base URI, (url de l'API)
        'base_uri' => 'http://152.228.163.113:3002/',
        'timeout' => 2.0,
    ]);
    
-Done.

You can now run the index.php page without having to install PostgresSQL or node.

To reverse the change simply change base_uri back to `localhost:3002`.

Beware that everytime you submit a form when testing the payment page it will send and receive data from Enzo's Server.
Please do not make it crash or it will impact service for everyone connecting to it.

## Setting up PostgreSQL

Install [PostgreSQL](https://www.postgresql.org/download/) from the official website and make you use the 14.X version for compatibility reasons.

-Open the **psql command-line tool**
    
-In the Windows Command Prompt, run the command:
        
    psql -U postgres
        
-Enter the password (`password`) when prompted.

-Run a `CREATE DATABASE` command to create the `payment` database.
    
    CREATE DATABASE payment WITH ENCODING 'UTF8';

-Then create the two following tables :

    CREATE TABLE subscriptions (
        subscription_type int NOT NULL,
        subName varchar(80) NOT NULL,
        price numeric NOT NULL
    );
    
    CREATE TABLE invoices (
        transaction_id SERIAL PRIMARY KEY,
        subscription_type INT NOT NULL,
        client_id INT NOT NULL,
        address TEXT NOT NULL,
        payment_method varchar(24) NOT NULL,
        date DATE NOT NULL
    );
   
-For testing purposes you can add some values to the `subscriptions` table :

    INSERT INTO subscriptions
    	VALUES (1, 'Student', 4.99),
    	(2, 'Basic', 9.99),
    	(3, 'Premium', 14.99);

## Setting up the node environnent

Install [Node.js](https://nodejs.org/en/) from the official website and make you use the 16.X version for compatibility reasons.

Then in Visual Studio Code, open the project and locate `index.js` that is in the `backend` folder, right click on it and open it with the integrated terminal.

Now in the Terminal you want to enter the following command :

    npm install
    
Test the environement by doing : 

    `npm test`
    
After the test passed, you can run the node with this command :

    node .\index.js
    
You should see the following message in the terminal :

    Listening on localhost:3002

You can now open http://localhost:3002/ in your browser, you should see the phrase "Hello world", and "Reçu" should ne written in the VSC Terminal.
This shows that everything works like it should.

## Testing

### Testing the API

#### Testing Requests with Postman

For testing the API we used [Postman](https://www.postman.com/downloads/), there are other options as well and the version is not important.

This app enables us to create and save HTTP/s requests, as well as to read their responses for debugging and testing.

#### Testing with npm

We now have some unit tests for the API located in the `backend\tests` folder.

There is a test for each method the API can do :

1. /invoice endpoint (METHOD POST)
2. /invoice/id endpoint (METHOD GET)
3. /invoices endpoint (METHOD GET)
4. /subscription/id endpoint (METHOD GET)

To run the tests, open the project and locate `index.js` that is in the `backend` folder, right click on it and open it with the integrated terminal.
    
Start the node with this command :

    node .\index.js

Then open the the `backend\tests` folder, right click on `route.test.js` it and open it with the integrated terminal.

Now in the Terminal you want to enter the following command :

    npm test
    
All 4 tests should pass.

### Testing the PHP Form

#### Testing in Visual Studio Code

There is a test for each input box the user as access to :

1. Credit Card Expiration Date
2. Credit Card CVC number
3. Credit Card number
4. Credit Card Owner name

To run the tests, first install the [PHPUnit](https://marketplace.visualstudio.com/items?itemName=emallin.phpunit) by Elon Mallin.

Then open file `FormTest.php` that is in the `frontend\Payment Form\tests` folder.
    
Open the VSC Command Palette with Ctrl + Shift + P and enter :

    PHPUnit Test Directory

All 30 assertions tests should pass.

In case PHPUnit doesn't work because it's still not installed, please the following command in the `frontend` where `composer.json` is located :

    composer require --dev phpunit/phpunit ^9.5
