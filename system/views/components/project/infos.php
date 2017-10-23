<div class="component">
    <div class="head">
        <h1>Projet</h1>
    </div>
    <div class="fix-body">
        <div class="body">
            <div class="set-item">
                <a class="item" href="<?= APP_ROOT ?>project/mode-createbug">
                    <div class="left">
                        <span>+</span>
                    </div>
                    <div class="center">Reporter un bug</div>
                </a>
            </div>
            <div class="set-item">
                <a class="item" href="<?= APP_ROOT ?>project/mode-createevo">
                    <div class="left">
                        <span>+</span>
                    </div>
                    <div class="center">Proposer une évolution</div>
                </a>
            </div>
            <div class="number-box <?php echo ($nbBugs === 0) ? 'grey' : '' ?>">
                <span class="label">Bugs actifs</span>
                <span class="value"><?php echo $nbBugs ?></span>
            </div>
            <div class="number-box <?php echo ($nbProposedEvolutions === 0) ? 'grey' : '' ?>">
                <span class="label">Evolutions proposées</span>
                <span class="value"><?php echo $nbProposedEvolutions ?></span>
            </div>
            <div class="number-box <?php echo ($nbAcceptedEvolutions === 0) ? 'grey' : '' ?>">
                <span class="label">Evolutions acceptées</span>
                <span class="value"><?php echo $nbAcceptedEvolutions ?></span>
            </div>
            <div class="number-box <?php echo ($nbTasks === 0) ? 'grey' : '' ?>">
                <span class="label">Tâches en cours</span>
                <span class="value"><?php echo $nbTasks ?></span>
            </div>
            
        </div>
    </div>
</div>