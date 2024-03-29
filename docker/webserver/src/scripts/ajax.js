function request() {
    "use strict";

    //Global xmlhttp object
    let xmlhttp = new XMLHttpRequest();

    //Global AJAX response handler
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            process(this.responseText);
        }
    }

    xmlhttp.open("GET", "KundenStatus.php", true);
    xmlhttp.send();
}

window.onload=function() {
    this.request();
    this.setInterval(window.request, 2000);
}

function process(responseText) {
    "use strict";

    let json = JSON.parse(responseText);

    let sectionNode = document.getElementById("cust");

    // Remove all child nodes from <section>.
    while (sectionNode.firstChild) {
        sectionNode.removeChild(sectionNode.firstChild);
    }

    //Add h2-element bevor p-elements
    let h2Node = document.createElement("h2");
    h2Node.textContent = "Kundenbestellungen:";
    sectionNode.appendChild(h2Node);

    json.forEach(function(pizza) {  

        let divNode = document.createElement("div");
        divNode.className = "orderCustomer";
        //divNode.setAttribute("p", "Bestellnummer: " + pizza.orderID);
        //divNode.setAttribute("p", pizza.pizzaName + " - " + pizza.status);

        let pNode1 = document.createElement("p");
        pNode1.textContent = "Bestellnummer: " + pizza.orderID;
        divNode.appendChild(pNode1);

        let pNode2 = document.createElement("p");
        pNode2.textContent = pizza.pizzaName + " - " + pizza.status;
        divNode.appendChild(pNode2);

        sectionNode.appendChild(divNode);
    });
}