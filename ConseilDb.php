<?php

namespace AcMarche\Conseil;

use DateTime;
use Exception;
use PDOException;
use PDOStatement;
use PDO;

class ConseilDb
{
    private \PDO $dbh;

    public function __construct()
    {
        $dsn      = 'mysql:host=localhost;dbname=conseil';
        $username = DB_USER;
        $password = DB_PASSWORD;
        $options  = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        );

        $this->dbh = new PDO($dsn, $username, $password, $options);
    }

    public function getAllOrdre()
    {
        $today = new DateTime();
        $sql   = 'SELECT * FROM ordre_jour WHERE `date_fin_diffusion` >= '.$today->format(
                'Y-m-d'
            ).' ORDER BY `date_ordre` DESC ';
        $query = $this->execQuery($sql);

        return $query->fetchAll();
    }

    public function getPvByYear(int $year)
    {
        $sql   = "SELECT * FROM pv WHERE `date_pv` LIKE '$year-%' ORDER BY `date_pv` DESC ";
        $query = $this->execQuery($sql);

        return $query->fetchAll();
    }

    public function getByYearPvs(int $year): array
    {
        return $this->getPvByYear($year);
    }

    /**
     *
     * @throws Exception
     */
    public function insertOrdre(string $nom, string $dateOrdre, string $dateFin, string $fileName): bool|string
    {
        $today = new DateTime();
        $today = $today->format('Y-m-d');
        $req   = "INSERT INTO `ordre_jour` (`nom`,`date_ordre`,`date_fin_diffusion`,`file_name`,`createdAt`,`updatedAt`) 
VALUES (?,?,?,?,?,?)";

        $stmt = $this->dbh->prepare($req);
        $stmt->bindParam(1, $nom);
        $stmt->bindParam(2, $dateOrdre);
        $stmt->bindParam(3, $dateFin);
        $stmt->bindParam(4, $fileName);
        $stmt->bindParam(5, $today);
        $stmt->bindParam(6, $today);

        try {
            return $stmt->execute();
        } catch (PDOException $exception) {
            return $exception->getMessage();
        }

    }

    public function insertPv(string $nom, string $datePv, string $fileName): bool|string
    {
        $today = new DateTime();
        $today = $today->format('Y-m-d');
        $req   = "INSERT INTO `pv` (`nom`,`date_pv`,`file_name`,`createdAt`,`updatedAt`) 
VALUES (?,?,?,?,?)";

        $stmt = $this->dbh->prepare($req);
        $stmt->bindParam(1, $nom);
        $stmt->bindParam(2, $datePv);
        $stmt->bindParam(3, $fileName);
        $stmt->bindParam(4, $today);
        $stmt->bindParam(5, $today);

        try {
            return $stmt->execute();
        } catch (PDOException $exception) {
            return $exception->getMessage();
        }

    }

    public function deleteOrdre(string $dateOrdre, string $fileName): bool|string
    {
        $req = 'DELETE FROM ordre_jour WHERE date_ordre = ? AND file_name = ?';

        $stmt = $this->dbh->prepare($req);
        $stmt->bindParam(1, $dateOrdre);
        $stmt->bindParam(2, $fileName);

        try {
            return $stmt->execute();
        } catch (PDOException $exception) {
            return $exception->getMessage();
        }
    }

    public function deletePv(string $datePv, string $fileName): bool|string
    {
        $req = 'DELETE FROM pv WHERE date_pv = ? AND file_name = ?';

        $stmt = $this->dbh->prepare($req);
        $stmt->bindParam(1, $datePv);
        $stmt->bindParam(2, $fileName);

        try {
            return $stmt->execute();
        } catch (PDOException $exception) {
            return $exception->getMessage();
        }
    }

    public function execQuery($sql): bool|PDOStatement
    {
        // var_dump($sql);
        $query = $this->dbh->query($sql);
        $error = $this->dbh->errorInfo();
        if ($error[0] != '0000') {
            //    var_dump($error[2]);
            // mail('jf@marche.be', 'duobac error sql', $error[2]);

            throw new Exception($error[2]);
        };

        return $query;
    }

}

?>
