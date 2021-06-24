<?php

    if(!array_key_exists('path', $_GET)){
        echo 'Erro: Parâmetro não enviado';
        exit;
    }

    $path = explode('/', $_GET['path']);

    if(count($path) == 0 || $path[0] == "") {
        echo 'Erro: Parâmetro não enviado';
        exit;
    }

    $params1 = "";
    if(count($path) > 1) {
        $params1 = $path[1];
    }

    $contents = file_get_contents('db.json');

    $json = json_decode($contents, true);

    $method = $_SERVER['REQUEST_METHOD'];

    header('Content-type: application/json');
    $body = file_get_contents('php://input');

    function findById($vector, $params1) {
        $foundItem = -1;
        foreach($vector as $key => $obj){
            if($obj['id'] == $params1) {
                $foundItem = $key;
                break;
            }
        }
        return $foundItem;
    }

    if($method === 'GET') {
        if($json[$path[0]]) {
            if($params1 == ""){
             echo json_encode($json[$path[0]]);
            } 
            else {
               $foundItem = findById($json[$path[0]], $params1);
               if($foundItem >= 0) {
                 echo json_encode($json[$path[0]][$foundItem]);
                }
                else {
                 echo 'ERROR.';
                 exit;
                }
            }
        }
        else {
            echo '[]';
        }
    }

    if($method === 'POST') {
        $jsonBody = json_decode($body, true);
        $jsonBody['id'] = time();

        if(!$json[$path[0]]) {
            $json[$path[0]] = [];
        }
            $json[$path[0]][] = $jsonBody;
            echo 'Criado: ' . json_encode($jsonBody);
            file_put_contents('db.json', json_encode($json));
    }

    if($method === 'DELETE') {
        if($json[$path[0]]) {
            if($params1 == ""){
             echo json_encode($json[$path[0]]);
            } 
            else {
               $foundItem = findById($json[$path[0]], $params1);
               if($foundItem >= 0) {
                 echo 'Deletado: ' . json_encode($json[$path[0]][$foundItem]);
                 unset($json[$path[0]][$foundItem]);
                 file_put_contents('db.json', json_encode($json));
                }
                else {
                 echo 'ERROR.';
                 exit;
                }
            }
        }
        else {
            echo 'Error.';
        }
    }
    
    if($method === 'PUT') {
        if($json[$path[0]]) {
            if($params1 == ""){
             echo json_encode($json[$path[0]]);
            } 
            else {
               $foundItem = findById($json[$path[0]], $params1);
               if($foundItem >= 0) {
                 $jsonBody = json_decode($body, true);
                 $jsonBody['id'] = $params1;
                 $json[$path[0]][$foundItem] = $jsonBody;
                 file_put_contents('db.json', json_encode($json));
                 echo 'Alterado: ' . json_encode($json[$path[0]][$foundItem]);
                }
                else {
                 echo 'ERROR.';
                 exit;
                }
            }
        }
        else {
            echo 'Error.';
        }
    }