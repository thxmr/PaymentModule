const { json } = require('express');
const request = require('supertest');
const base_url = 'localhost:3002'

// First test, /invoice endpoint (METHOD POST)
describe('New invoice "/invoice" Endpoint', () => {
    it('should create a new invoice', async () => {
      const res = await request(base_url)
        .post('/invoice')
        .send({
                subscription_type : 1,
                address : "Test adresse",
                payment_method : 1,
                client_id : 0
            })
      expect(res.statusCode).toEqual(200)
    })
});

// Second test, /invoice/id endpoint (METHOD GET)
describe('Get invoice details "/invoice/:id" Endpoint', () => {
  it("Should get details from one invoice", async () => {
    const res = await request(base_url)
      .get('/invoice/1')
      .send()
    expect(res.statusCode).toEqual(200);
    expect(res.body.transaction_id).toEqual("1");
  })
})

// Third test, /invoices endpoint (METHOD GET)
describe('Get all invoices "/invoices/:client_id" Endpoint', () => {
	it('Should get all the invoices from one client', async () => {
		const res = await request(base_url)
			.get('/invoices/0')
			.send()
		expect(res.statusCode).toEqual(200);
		expect(typeof res.body).toEqual("object");
	})
})

// Fourth test (last one for now), /subscription/id endpoint (METHOD GET)
describe('Get all subscriptions "/subscription/:sub_id" Endpoint', () => {
	it('Should return all the subscriptions possibilities', async () => {
		const res = await request(base_url)
			.get("/subscription/1")
			.send()
		expect(res.statusCode).toEqual(200)
		expect(res.body.subscription_name).toEqual("Student")
	})
})