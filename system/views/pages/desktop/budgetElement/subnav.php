<?php

$request = $this->getContainer()->get('app.request');

?>
<div id="subnav">
    <button class="move-side-bar top" data-dir="up"> </button>
    <div class="overflow">
        <a href="/budget/view-donation" class="item <?= (!$request->query->has('view') || $request->query->get('view') === 'donation') ? 'active' : '' ?>">
            <span class="picto">
                <img src="/public/media/financial/invest.png" alt="" />
            </span>
            <span class="content skin-1">
                <span>Donations</span>
            </span>
        </a>
        <a href="/budget/view-statistics" class="item <?= ($request->query->get('view') === 'statistics') ? 'active' : '' ?>">
            <span class="picto">
                <img src="/public/media/orbitalbase/commercialplateforme.png" alt="" />
            </span>
            <span class="content skin-1">
                <span>Statistiques</span>
            </span>
        </a>
    </div>
    <button class="move-side-bar bottom" data-dir="down"> </button>
</div>
