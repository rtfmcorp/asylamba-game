<?php
use Asylamba\Classes\Library\Format;
?>
<div class="component profil">
	<div class="head skin-1">
		<h1>Financement</h1>
	</div>
	<div class="fix-body">
		<div class="body">
            <h4>Statistiques</h4>
            <div class="number-box <?= (array_sum($treasury) <= 0) ? 'grey' : '' ?>">
                <span class="label">Trésorerie</span>
                <span class="value"><?= Format::numberFormat(end($treasury), 2); ?> €</span>
            </div>
            <div class="number-box">
                <span class="label">Coût des serveurs</span>
                <span class="value">2,99 € / mois</span>
            </div>
            <div class="number-box">
                <span class="label">Commission de la passerelle de paiement</span>
                <span class="value">1.4% + 0,25 €</span>
            </div>
        </div>
	</div>
</div>
<div class="component size3">
    <div class="head skin-1">
        
    </div>
    <div class="fix-body">
        <div class="body">
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js"></script>
            
            <h4>Évolution de la trésorerie</h4>
            <canvas id="treasury" width="100%" height="40"></canvas>
            <div id="budget-ratios">
                <div>
                    <h4>Budget du mois</h4>
                    <canvas id="monthly-budget" width="300" height="300"></canvas>
                </div>
                <div>
                    <h4>Budget global</h4>
                    <canvas id="global-budget" width="300" height="300"></canvas>
                </div>
            </div>
            <script type="text/javascript">
                var treasury = [<?= implode(',', $treasury) ?>];
                
                var monthlyIncome = <?= $monthlyIncome ?>;
                var monthlyExpenses = [<?= implode(',', array_values($monthlyExpenses)); ?>];
                
                var globalIncome = <?= $globalIncome ?>;
                var globalExpenses = [<?= implode(',', array_values($globalExpenses)); ?>];
            </script>
            <script type="text/javascript" src="<?= JS ?>budget/statistics.js"></script>
        </div>
    </div>
</div>