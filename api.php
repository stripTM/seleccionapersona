<?php
$ORIGEN = __DIR__.'/photoCollection.json';
$SESSION_PENDING_LIST = sys_get_temp_dir().'/photoPendingSession.json';
$SESSION_HISTORY_LIST = sys_get_temp_dir().'/photoHistorySession.json';
$SESSION_SELECTION = sys_get_temp_dir().'/photoSelectionSession.json';

$action = isset($_REQUEST['a']) ? $_REQUEST['a'] : 'list';

switch ($action) {
    case 'reset':
        // Cargamos las fotos las añadimos un id y fijamos la derecha e izquierda
        $photoCollectionData = json_decode(file_get_contents($ORIGEN));
        // Ordenar como una pila
        $photoCollection = $photoCollectionData->photos;
        $n = 0;
        $photoCollectionId = array_reverse(
            array_map(
                function($item, $n) {
                    $item->id = "$n";
                    $n++;
                    return $item;
                },
                $photoCollection,
                array_keys($photoCollection)
            )
        );
        $selectionPhotos = new stdclass();
        $selectionPhotos->photoLeft = array_pop($photoCollectionId);
        $selectionPhotos->photoRight = array_pop($photoCollectionId);
        $selectionPhotos->refreshTime = $photoCollectionData->refreshTime;

        $history = [];

        file_put_contents ($SESSION_PENDING_LIST, json_encode(array_values($photoCollectionId)));
        file_put_contents ($SESSION_SELECTION, json_encode($selectionPhotos));
        file_put_contents ($SESSION_HISTORY_LIST, json_encode($history));

        header('Content-Type: application/json');
        echo(json_encode($selectionPhotos));
        break;
    case 'delete':
        $photoCollection = json_decode(file_get_contents ($SESSION_PENDING_LIST));
        $history = json_decode(file_get_contents ($SESSION_HISTORY_LIST));
        $selectionPhotos = json_decode(file_get_contents ($SESSION_SELECTION));
        $newHistoryItem = new stdclass();

        $idDetele = isset($_POST['n']) ? $_POST['n'] : null;
        $photoPosition = isset($_POST['position']) ? $_POST['position'] : null;
        if ($idDetele === $selectionPhotos->photoLeft->id) {
            $newHistoryItem->photo = $selectionPhotos->photoLeft;
            $selectionPhotos->photoLeft = array_pop($photoCollection);
        }
        elseif ($idDetele === $selectionPhotos->photoRight->id) {
            $newHistoryItem->photo = $selectionPhotos->photoRight;
            $selectionPhotos->photoRight = array_pop($photoCollection);
        }

        if($newHistoryItem->photo) {
            $newHistoryItem->position = $photoPosition;
            $history[] = $newHistoryItem;
        }

        file_put_contents ($SESSION_PENDING_LIST, json_encode(array_values($photoCollection)));
        file_put_contents ($SESSION_SELECTION, json_encode($selectionPhotos));
        file_put_contents ($SESSION_HISTORY_LIST, json_encode($history));

        header('Content-Type: application/json');
        echo(json_encode($selectionPhotos));
        break;
    case 'undo':
        $photoCollection = json_decode(file_get_contents ($SESSION_PENDING_LIST));
        $history = json_decode(file_get_contents ($SESSION_HISTORY_LIST));
        $selectionPhotos = json_decode(file_get_contents ($SESSION_SELECTION));
        $undoPhoto = array_pop($history);

        if($undoPhoto) {
            if($undoPhoto->position === 'photoLeft') {
                $photoCollection[] = $selectionPhotos->photoLeft;
                $selectionPhotos->photoLeft = $undoPhoto->photo;
            } else {
                $photoCollection[] = $selectionPhotos->photoRight;
                $selectionPhotos->photoRight = $undoPhoto->photo;
            }
        }

        file_put_contents ($SESSION_PENDING_LIST, json_encode(array_values($photoCollection)));
        file_put_contents ($SESSION_SELECTION, json_encode($selectionPhotos));
        file_put_contents ($SESSION_HISTORY_LIST, json_encode($history));

        header('Content-Type: application/json');
        echo(json_encode($selectionPhotos));
        break;
    case 'collection':
        header('Content-Type: application/json');
        readfile($SESSION_PENDING_LIST);
        break;
    case 'history':
        header('Content-Type: application/json');
        readfile($SESSION_HISTORY_LIST);
        break;
    default:
        header('Content-Type: application/json');
        readfile($SESSION_SELECTION);
        break;
}
