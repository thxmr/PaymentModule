window.onload = function () {

    const name = document.getElementById('name');
    const cardnumber = document.getElementById('cardnumber');
    const expirationdate = document.getElementById('dateexp');
    const securitycode = document.getElementById('cvc');
    document.getElementById("paypalform").hidden = true;
    document.getElementById("appleform").hidden = true;
    
    
    let cctype = null;
    
    //Mask the Credit Card Number Input
    var cardnumber_mask = new IMask(cardnumber, {
        mask: [
            {
                mask: '0000 000000 00000',
                regex: '^3[47]\\d{0,13}',
                cardtype: 'american express'
            },
            {
                mask: '0000 0000 0000 0000',
                regex: '^(5[1-5]\\d{0,2}|22[2-9]\\d{0,1}|2[3-7]\\d{0,2})\\d{0,12}',
                cardtype: 'mastercard'
            },
            {
                mask: '0000 0000 0000 0000',
                cardtype: 'Unknown'
            }
        ],
        dispatch: function (appended, dynamicMasked) {
            var number = (dynamicMasked.value + appended).replace(/\D/g, '');
    
            for (var i = 0; i < dynamicMasked.compiledMasks.length; i++) {
                let re = new RegExp(dynamicMasked.compiledMasks[i].regex);
                if (number.match(re) != null) {
                    return dynamicMasked.compiledMasks[i];
                }
            }
        }
    });
    
    //Mask the Expiration Date
    var expirationdate_mask = new IMask(expirationdate, {
        mask: 'MM{/}YY',
        groups: {
            YY: new IMask.MaskedPattern.Group.Range([0, 99]),
            MM: new IMask.MaskedPattern.Group.Range([1, 12]),
        }
    });
    
    //Mask the security code
    var securitycode_mask = new IMask(securitycode, {
        mask: '000',
    });
    
   
    
    };