<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app = new \Slim\App();

//listar autos
$app->get('/api/autos', function (Request $request, Response $response) {
    $sql = "SELECT * FROM vehiculo";
    try {
        $db = new db();
        $db = $db->connectDB();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $autos = $resultado->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($autos);
        } else {
            echo json_encode("No existen registros de autos en la BD");
        }
    } catch (PDOException $e) {
        echo '{"text": ' . $e->getMessage() . '}';
    }
});

// listar autos por id
$app->get('/api/autos/{id}', function (Request $request, Response $response) {
    $idVehiculo = $request->getAttribute('id');
    $sql = "SELECT * FROM vehiculo WHERE id_vehiculo = :id_vehiculo";
    try {
        $db = new db();
        $db = $db->connectDB();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':id_vehiculo', $idVehiculo);
        $resultado->execute();
        if ($resultado->rowCount() > 0) {
            $auto = $resultado->fetch(PDO::FETCH_OBJ);
            echo json_encode($auto);
        } else {
            echo json_encode("No existe un vehículo con ese ID en la BD");
        }
    } catch (PDOException $e) {
        echo '{"text": ' . $e->getMessage() . '}';
    }
});


//agregar autos
$app->post('/api/autos/nuevo', function (Request $request, Response $response) {
    $nomVehiculo = $request->getParam('nom_vehiculo');
    $modVehiculo = $request->getParam('mod_vehiculo');
    $marVehiculo = $request->getParam('mar_vehiculo');
    $colVehiculo = $request->getParam('col_vehiculo');
    $anioVehiculo = $request->getParam('anio_vehiculo');
    $preVehiculo = $request->getParam('pre_vehiculo');
    $stock = $request->getParam('stock');

    $sql = "INSERT INTO vehiculo (nom_vehiculo, mod_vehiculo, mar_vehiculo, col_vehiculo, anio_vehiculo, pre_vehiculo, stock) 
            VALUES (:nom_vehiculo, :mod_vehiculo, :mar_vehiculo, :col_vehiculo, :anio_vehiculo, :pre_vehiculo, :stock)";
    try {
        $db = new db();
        $db = $db->connectDB();
        $resultado = $db->prepare($sql);

        $resultado->bindParam(':nom_vehiculo', $nomVehiculo);
        $resultado->bindParam(':mod_vehiculo', $modVehiculo);
        $resultado->bindParam(':mar_vehiculo', $marVehiculo);
        $resultado->bindParam(':col_vehiculo', $colVehiculo);
        $resultado->bindParam(':anio_vehiculo', $anioVehiculo);
        $resultado->bindParam(':pre_vehiculo', $preVehiculo);
        $resultado->bindParam(':stock', $stock);

        $resultado->execute();
        echo json_encode("Nuevo vehículo registrado con éxito");

    } catch (PDOException $e) {
        echo '{"text": ' . $e->getMessage() . '}';
    }
});
