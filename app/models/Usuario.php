<?php

class Usuario
{
    public $id;
    public $usuario;
    public $clave;

    public function crearUsuario()
    {
        $DBAccessObj = DBAccess::GetInstance();
        $consulta = $DBAccessObj->PrepareQuery("INSERT INTO usuarios (usuario, clave) VALUES (:usuario, :clave)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->execute();

        return $DBAccessObj->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $DBAccessObj = DBAccess::GetInstance();
        $consulta = $DBAccessObj->PrepareQuery("SELECT id, usuario, clave FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuario($usuario)
    {
        $DBAccessObj = DBAccess::GetInstance();
        $consulta = $DBAccessObj->PrepareQuery("SELECT id, usuario, clave FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function modificarUsuario()
    {
        $DBAccessObj = DBAccess::GetInstance();
        $consulta = $DBAccessObj->PrepareQuery("UPDATE usuarios SET usuario = :usuario, clave = :clave WHERE id = :id");
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarUsuario($usuario)
    {
        $DBAccessObj = DBAccess::GetInstance();
        $consulta = $DBAccessObj->PrepareQuery("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }
}