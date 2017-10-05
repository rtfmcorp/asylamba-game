var getMonths = function(dataLength) {
    var date = new Date();
    var currentMonth = date.getMonth();
    var months = [];
    
    // get the 4 previous months
    for (let i = currentMonth - (dataLength - 1); i < currentMonth; i++) {
        date.setMonth(i);
        months.push(date.toLocaleDateString('fr-FR', {month: 'long', year: 'numeric'}));
    }
    date.setMonth(currentMonth);
    months.push(date.toLocaleDateString('fr-FR', {month: 'long', year: 'numeric'}));
    // get the five next months
    var max = (currentMonth + (7 - dataLength))
    for (let i = currentMonth + 1; i <= max; i++) {
        date.setMonth(i);
        months.push(date.toLocaleDateString('fr-FR', {month: 'long', year: 'numeric'}));
    }
    return months;
}

var myLineChart = new Chart('treasury', {
    type: 'line',
    data: {
        labels: getMonths(treasury.length),
        datasets: [
            {
                label: "Trésorerie",
                data: treasury,
                borderColor: "rgba(255,255,255,0.6)",
                backgroundColor: "rgba(0,0,255,0.4)"
            },
        ]
    },
    options: {
        legend: {
            display: false
        }
    }
});

var donutEl = document.getElementById("monthly-budget").getContext("2d");
var donut = new Chart(donutEl, {
    type: 'doughnut',
    data: {
        datasets: [{
            data: (new Array()).concat(monthlyIncome, monthlyExpenses),
            backgroundColor: [
                "rgba(0,0,255,0.4)",
                "rgba(255,0,0,0.6)",
                "rgba(255,0,0,0.4)"
            ],
        }],
        labels: [
            "Donations",
            "Coût de la passerelle de paiement",
            "Coût des serveurs"
        ]
    },
    options: {
        responsive: true,
        legend: {
            display: false
        }
    }
});

var donutEl2 = document.getElementById("global-budget").getContext("2d");
var donut2 = new Chart(donutEl2, {
    type: 'doughnut',
    data: {
        datasets: [{
            data: (new Array()).concat(globalIncome, globalExpenses),
            backgroundColor: [
                "rgba(0,0,255,0.4)",
                "rgba(255,0,0,0.6)",
                "rgba(255,0,0,0.4)"
            ],
        }],
        labels: [
            "Donations",
            "Coût de la passerelle de paiement",
            "Coût des serveurs"
        ]
    },
    options: {
        responsive: true,
        legend: {
            display: false
        }
    }
});