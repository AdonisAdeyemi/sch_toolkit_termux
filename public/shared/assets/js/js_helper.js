
/**
EVENT LISTENER FOR FLASH MESSAGE
**/
document.addEventListener('DOMContentLoaded', () => {

    if (window.__FLASH_MESSAGES__ && window.__FLASH_MESSAGES__.length) {
        showFlash(window.__FLASH_MESSAGES__);
    }
});


/*****
****
****/


function showFlash(messages, delay = 4000) {
    
    const container = document.getElementById('toast-container');

    messages.forEach(msg => {
        const toast = document.createElement('div');

        toast.className = `
            toast align-items-center text-bg-${msg.type} border-0 mb-2 show
        `;

        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${msg.text}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"></button>
            </div>
        `;

        container.appendChild(toast);

        // Manual dismiss
        toast.querySelector('.btn-close').onclick = () => toast.remove();

        // Auto dismiss
        setTimeout(() => {
            toast.classList.remove('show');
            toast.classList.add('hide');
            setTimeout(() => toast.remove(), 300);
        }, delay);
    });
}

/*****
*****
******
******/

function makeFlashMsg (type,text)
{
    return {
        "type" : type,
        "text" : text
        };
}



/** old jsfor token-dashboard-price
<!--- priving script --->
const pricing = {
    starter: { tokens: 10, amount: 1200 },
    basic:   { tokens: 25, amount: 2500 },
    pro:     { tokens: 60, amount: 5400 },
    school:  { tokens: 150, amount: 12000 }
};

const tokensEl = document.getElementById('tokens');
const amountEl = document.getElementById('amount');

document.querySelectorAll('input[name="tier"]').forEach(radio => {
    radio.addEventListener('change', function () {
        const tier = pricing[this.value];
        tokensEl.textContent = tier.tokens;
        amountEl.textContent = tier.amount.toLocaleString();
    });
});

***/


function updateTier(radio)
{
let tokens = Number(radio.dataset.tokens);
let price  = Number(radio.dataset.price);

let perPrint = Math.round(price / tokens);

document.getElementById("tokens").textContent = tokens;
document.getElementById("amount").textContent = price.toLocaleString();
document.getElementById("perPrint").textContent = perPrint;
}

document.querySelectorAll(".tier").forEach(function(radio){

radio.addEventListener("change", function(){
updateTier(this);
});

});



/****
*****
****
****/



