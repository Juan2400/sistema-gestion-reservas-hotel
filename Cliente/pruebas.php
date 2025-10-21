try {
    $sql = "INSERT INTO Usuario (nombres, dni, correo_electronico, contrasena, id_tipo_usuario, edad) 
            VALUES ('Juan Perez', '12345678', 'correo@ejemplo.com', 'password', 1, 25)";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute()) {
        echo 'Inserción exitosa';
    } else {
        echo 'Error al insertar';
    }
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
