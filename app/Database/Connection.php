<?php

namespace App\Database;

use PDO;
use PDOException;
use Exception;
use App\Helpers\Seguridad;

/**
 * Clase encargada de establecer la conexión a la base de datos mediante PDO.
 */
class Connection
{
    private string $serverName;
    private string $usuarioBd;
    private string $passwordBd;
    private string $baseDeDatos;
    private string $puertoBd;
    private ?PDO $cnn;
    private string $errorMensaje;

    /**
     * Lee la configuración, construye la cadena de conexión e intenta conectar a la BD.
     */
    public function __construct()
    {
        try {
            $configPath = $_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE2/Config/Aplicacion.ini';
            if (!file_exists($configPath)) {
                throw new Exception("No se encontró el archivo de configuración: $configPath");
            }

            $iniArray = parse_ini_file($configPath);
            if ($iniArray === false) {
                throw new Exception("Error al parsear el archivo de configuración: $configPath");
            }

            $this->serverName  = $iniArray['db_hostname'] ?? '';
            $this->usuarioBd   = $iniArray['db_username'] ?? '';

            if ($iniArray['db_password'] === '') {
                $this->passwordBd = '';
            } else {
                $this->passwordBd = trim(Seguridad::desencriptar_aes($iniArray['db_password'], $iniArray['db_semilla']));
            }

            $this->baseDeDatos = $iniArray['db_database'] ?? '';
            $this->puertoBd    = $iniArray['db_puerto'] ?? '';
            $this->cnn         = null;
            $this->errorMensaje = '';

            $cadena = sprintf(
                "pgsql:host=%s;port=%s;dbname=%s;user=%s;password=%s",
                $this->serverName,
                $this->puertoBd,
                $this->baseDeDatos,
                $this->usuarioBd,
                $this->passwordBd
            );

            $this->cnn = new PDO($cadena);
            $this->cnn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        }
        catch (PDOException $ex) {
            $this->cnn = null;
            $this->errorMensaje = "Error de conexión a la BD: " . utf8_encode($ex->getMessage());
        }
        catch (Exception $ex) {
            $this->cnn = null;
            $this->errorMensaje = "Error en la configuración: " . $ex->getMessage();
        }
    }

    /**
     * Devuelve el nombre del servidor configurado.
     */
    public function getServerName(): string
    {
        return $this->serverName;
    }

    /**
     * Devuelve el nombre de la base de datos configurada.
     */
    public function getBaseDeDatos(): string
    {
        return $this->baseDeDatos;
    }

    /**
     * Retorna la conexión PDO o null si no se estableció.
     */
    public function getConexion(): ?PDO
    {
        return $this->cnn;
    }

    /**
     * Retorna el mensaje de error si ocurrió un problema al conectar.
     */
    public function getError(): string
    {
        return $this->errorMensaje;
    }
}
