<?php
# check le serveur s
# recup la clé
# uncrpyt a
# ajout dans le add

use Asylamba\Classes\Worker\API;

$request = $this->getContainer()->get('app.request');

$query = API::unParse($_SERVER['REQUEST_URI']);
$query = explode('/', $query);

foreach ($query as $q) {
    $args = explode('-', $q);

    if (count($args) == 2) {
        $request->query->add($args[0], $args[1]);
    }
}

# réglage de l'encodage
header('Content-type: text/html; charset=utf-8');

if ($this->getContainer()->getParameter('environment') === 'dev' || $request->query->has('password')) {
    switch ($request->query->get('a')) {
        # case 'ban': 				include API . 'apis/ban.php'; break;

        default:
        echo serialize(array(
            'statement' => 'error',
            'message' => 'API non reconnue par le système'
        ));
        break;
    }
} else {
    echo serialize(array(
        'statement' => 'error',
        'message' => 'Accès refusé'
    ));
}
