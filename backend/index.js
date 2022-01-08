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
