const express = require('express');
const bodyParser = require('body-parser');
const pgp = require('pg-promise');
const {Pool, Client} = require('pg')
const db = new Pool({
    user: 'postgres',
    host: 'localhost',
    database: 'payment',
    password: 'password',
    port: 5432,
});
const PORT = 3002;

const app = express();
app.use(express.urlencoded({ extended: true }))
app.use(express.json());

app.get('/', async function (req, res) {
    console.log("Reçu")
    return res.send('Hello world');
});

// Return all invoices of a client
app.get('/invoices/:client_id', async function (req, res) {
    // Initialization
    let SQL = "";
    let transactions = [];
    // Prepare the request to get all client id
    SQL = `SELECT transaction_id FROM invoices WHERE client_id = ${req.params.client_id}`

    // Sending the SQL request
    let pgRes = await db.query(SQL);

    // If happens
    if(pgRes.rows.length > 0) {
        // Prepare all IDs
        result = pgRes.rows
        result.forEach(jsonTransac => {
            transactions.push(jsonTransac.transaction_id);
        })
        // Return all IDs
        return res.status(200).send({'transaction_id' : transactions})
    }
    else {
        // Return error, wrong client id
        return res.status(404).send({'error' : "Wrong client id"})
    }
});

app.get('/invoice/:id', async function (req, res) {
    // Initialization
    let data = {};
    let SQL = "";

    // Preparing SQL request
    SQL = `SELECT * FROM invoices WHERE transaction_id = ${req.params.id}`

    // Getting SQL datas
    let pgRes = await db.query(SQL);

    // If fetch data
    if(pgRes.rows.length > 0) {
        // Prepare SQL request to get the subscription details
        SQL = `SELECT * FROM subscriptions WHERE subscription_type = ${pgRes.rows[0].subscription_type}`;
        // Send the SQL request
        let subTypeRes = await db.query(SQL);

        // If fetch sub details
        if(subTypeRes.rows.length > 0) {

            // Prepare invoice details
            data = {
                "transaction_id" : req.params.id,
                "sub_name" : subTypeRes.rows[0].subname,
                "price" : subTypeRes.rows[0].price,
                "client_id" : pgRes.rows[0].client_id,
                "name" : pgRes.rows[0].name,
                "address" : pgRes.rows[0].address,
                "payment_method" : pgRes.rows[0].payment_method
            }

            // Return details of the invoice
            return res.status(200).send(data)
        }
        else {
            // Return internal error
            return res.status(500).send({'error' : "Internal server error"});
        }
    }
    else {
        // Return error
        return res.status(404).send({'error' : "Wrong transaction id"})
    }
});

app.post('/invoice', async function (req, res) {
    // Initialization
    let data = [];

    // Parsing data from the body request
    data['subType'] = req.body.subscription_type;
    data['address'] = req.body.address;
    data['payment_method'] = req.body.payment_method;
    data['client_id'] = req.body.client_id



    // Prepare the request
    let SQL = `
    INSERT INTO invoices (subscription_type, client_id, address, payment_method, date)
    VALUES ('${data['subType']}', '${data['client_id']}', '${data['address']}', '${data['payment_method']}', '${new Date().toISOString().slice(0, 10)}')
    RETURNING transaction_id;
    `
    console.log(SQL);

    // Send the request
    const pgRes = await db.query(SQL);
    if(pgRes.rows.length > 0) {
        console.log('Invoice added');
        // Fetch ID from SQL request
        let id = pgRes.rows[0].transaction_id;
            // Response to the client
        return res.status(200).send({'transaction_id' : id})
    }
    else {
        console.log(`Error in  SQL : ${pgRes}`)
        return res.status(500).send({'error' : pgRes})
    }
});

app.get('/subscription/:id', async function (req, res) {
    // Initialization
    let SQL = ""
    let data = {}

    // Prepare SQL request
    SQL = `SELECT * FROM subscriptions WHERE subscription_type = '${req.params.id}'`

    // Send the request
    let pgRes = await db.query(SQL)

    // Verif if the SQL request success
    if(pgRes.rows.length > 0) {
        data = {
            "subscription_name" : pgRes.rows[0].subname,
            "subscription_price" : pgRes.rows[0].price
        }
        return res.status(200).send(data);
    }
    else {
        return res.status(500).send({'error' : "Internal server error"});
    }
});

app.listen(PORT, () => {
    console.log(`Listening on localhost:${PORT}`)
});
