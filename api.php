<?php
$ORIGEN = __DIR__.'/photoCollection.json';
$SESSION_COLLECTION = sys_get_temp_dir().'/photoCollectionSession.json';
$SESSION_SELECTION = sys_get_temp_dir().'/photoSelectionSession.json';

function addId ($item, $n)
        {
            console.log("FE");
            $item->id = "+ $n";
            return $item;
        };

//header('Content-Type: application/json');
$action = isset($_POST['a']) ? $_POST['a'] : 'list';

switch ($action) {
    case 'reset':
        // Cargamos las fotos las añadimos un id y fijamos la derecha e izquierda
        $photoCollection = json_decode(file_get_contents($ORIGEN));
        $n = 0;
        $photoCollectionId = array_map(
            function($item, $n) {
                $item->id = $n;
                $n++;
                return $item;
            },
            $photoCollection,
            array_keys($photoCollection)
        );
        $selectionPhotos = new stdclass();
        $selectionPhotos->photoLeft = array_pop($photoCollectionId);
        $selectionPhotos->photoRight = array_pop($photoCollectionId);

        file_put_contents ($SESSION_COLLECTION, json_encode(array_values($photoCollectionId)));
        file_put_contents ($SESSION_SELECTION, json_encode($selectionPhotos));

        header('Location: .');
        //header('Content-Type: text/plain');
        //var_dump($selectionPhotos, $photoCollectionId);
        break;
    case 'pop':
        $photoCollection = json_decode(file_get_contents ($SESSION_COLLECTION));
        $selectionPhotos = json_decode(file_get_contents ($SESSION_SELECTION));
        $idRemove = isset($_POST['n']) ? intval($_POST['n']) : null;
        if ($idRemove === $selectionPhotos->photoLeft->id) {
            $selectionPhotos->photoLeft = array_pop($photoCollection);
            unset($photoCollection[$idRemove]);
        }
        elseif ($idRemove === $selectionPhotos->photoRight->id) {
            $selectionPhotos->photoRight = array_pop($photoCollection);
        }
        file_put_contents ($SESSION_COLLECTION, json_encode(array_values($photoCollection)));
        file_put_contents ($SESSION_SELECTION, json_encode($selectionPhotos));
        header('Content-Type: application/json');
        echo(json_encode($selectionPhotos));
        break;
    default:
        header('Content-Type: application/json');
        if($_GET['a'] === 'collection') {
            readfile($SESSION_COLLECTION);
        }
        else {
            readfile($SESSION_SELECTION);
        }
        break;
}
