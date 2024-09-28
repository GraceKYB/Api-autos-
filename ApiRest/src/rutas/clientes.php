<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app = new \Slim\App();
// Ruta para obtener todos los clientes
$app->get('/api/clientes', function (Request $request, Response $response) {
    $sql = "SELECT * FROM usuario";
    try {
        $db = new db();
        $db = $db->connectDB();
        $resultado = $db->query($sql);

        error_log("Después de ejecutar la consulta SQL.");

        if ($resultado->rowCount() > 0) {
            $clientes = $resultado->fetchAll(PDO::FETCH_OBJ);
            error_log("Se encontraron " . count($clientes) . " clientes.");
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(200)
                            ->write(json_encode($clientes));
        } else {
            error_log("No se encontraron registros.");
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(404)
                            ->write(json_encode("No existen registros de clientes en la BD"));
        }
    } catch (PDOException $e) {
        error_log("Error de PDO: " . $e->getMessage());
        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(500)
                        ->write(json_encode(['error' => $e->getMessage()]));
    }
});


// Listar clientes por ID
$app->get('/api/clientes/{id}', function (Request $request, Response $response) {
    $idCliente = $request->getAttribute('id');
    $sql = "SELECT * FROM usuario WHERE id_cliente = :id";
    try {
        $db = new db();
        $db = $db->connectDB();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':id', $idCliente);
        $resultado->execute();
        if ($resultado->rowCount() > 0) {
            $cliente = $resultado->fetch(PDO::FETCH_OBJ);
            return $response->withJson($cliente, 200);
        } else {
            return $response->withJson("No existen registros de clientes en la BD por ID", 404);
        }
    } catch (PDOException $e) {
        return $response->withJson(['error' => $e->getMessage()], 500);
    }
});

// Búsqueda por cédula
$app->get('/api/clientes/cedula/{cedula}', function (Request $request, Response $response) {
    $cedula = $request->getAttribute('cedula');
    $sql = "SELECT usuario.*, vehiculo.nom_vehiculo, vehiculo.mod_vehiculo, vehiculo.mar_vehiculo, vehiculo.anio_vehiculo 
            FROM usuario 
            LEFT JOIN compra ON usuario.id_cliente = compra.id_cliente
            LEFT JOIN compra_detalle ON compra.id_comp = compra_detalle.id_comp
            LEFT JOIN vehiculo ON compra_detalle.id_vehiculo = vehiculo.id_vehiculo
            WHERE usuario.cedula = :cedula";
    try {
        $db = new db();
        $db = $db->connectDB();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':cedula', $cedula);
        $resultado->execute();
        if ($resultado->rowCount() > 0) {
            $clienteConAuto = $resultado->fetchAll(PDO::FETCH_OBJ);
            return $response->withJson($clienteConAuto, 200);
        } else {
            return $response->withJson("No existen registros de clientes con esa cédula en la BD", 404);
        }
    } catch (PDOException $e) {
        return $response->withJson(['error' => $e->getMessage()], 500);
    }
});

// Agregar un cliente
$app->post('/api/clientes/nuevo', function (Request $request, Response $response) {
    $nombre = $request->getParam('nombre');
    $apellido = $request->getParam('apellido');
    $cedula = $request->getParam('cedula');
    $correo = $request->getParam('correo');
    $edad = $request->getParam('edad');
    $direccion = $request->getParam('direccion');
    $estado = $request->getParam('estado');

    $sql = "INSERT INTO usuario (nombre, apellido, cedula, correo, edad, direccion, estado) 
            VALUES (:nombre, :apellido, :cedula, :correo, :edad, :direccion, :estado)";
    try {
        $db = new db();
        $db = $db->connectDB();
        $resultado = $db->prepare($sql);

        $resultado->bindParam(':nombre', $nombre);
        $resultado->bindParam(':apellido', $apellido);
        $resultado->bindParam(':cedula', $cedula);
        $resultado->bindParam(':correo', $correo);
        $resultado->bindParam(':edad', $edad);
        $resultado->bindParam(':direccion', $direccion);
        $resultado->bindParam(':estado', $estado);

        $resultado->execute();
        return $response->withJson("Nuevo cliente registrado con éxito", 201);

    } catch (PDOException $e) {
        return $response->withJson(['error' => $e->getMessage()], 500);
    }
});



