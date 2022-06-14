<?php
/**
 * Part of galenus-verbatim https://github.com/galenus-verbatim/galenus
 * Copyright (c) 2021 Nathalie Rousseau
 * MIT License https://opensource.org/licenses/mit-license.php
 */

/**
 * This class requires verbatim, https://github.com/galenus-verbatim/varbatim
 * Specific code for Galenus https://galenus-verbatim.huma-num.fr/
 */

declare(strict_types=1);

require_once(dirname(__DIR__) . '/verbatim/Verbatim.php');

use Oeuvres\Kit\{Xml};
use Psr\Log\{LoggerInterface, NullLogger};

Galenus::init();
class Galenus
{
    /** Logger */
    private static $logger;
    /** The sqlite file, hard coded */
    private static $db_file = __DIR__ . '/galenus.db';
    /**
     * Set logger
     */
    public static function setLogger(LoggerInterface $logger)
    {
        // default logger
        self::$logger = $logger;
    }

    /**
     * Init static things
     */
    static public function init()
    {
        self::$logger = new NullLogger();
    }

    /**
     * Path of sqlite file
     */
    static public function db_file()
    {
        return self::$db_file;
    }
    /**
     * 
     */
    static function zotero($rdf_file = __DIR__ . "/galenus-verbatim.rdf")
    {
        Xml::setLogger(self::$logger);
        $dom = Xml::load($rdf_file);
        /* editiones */
        $editiones = Xml::transformToXml(
            __DIR__ . "/build/galenzot_editiones.xsl",
            $dom
        );
        file_put_contents(__DIR__ . "/pages/editiones.html", $editiones);


        Verbatim::$pdo->beginTransaction();
        $re = '@<section class="verbatim" id="([^"]+)">.*?</section>@s';
        preg_match_all($re, $editiones, $matches);

        $clavis = $matches[1];
        $bibl = $matches[0];
        $sql = "UPDATE editio SET bibl = ? WHERE clavis = ?;";
        $ins = Verbatim::$pdo->prepare($sql);
        $sel = Verbatim::$pdo->prepare("SELECT COUNT(*) FROM editio WHERE clavis = ?;");

        for ($i = 0, $max = count($bibl); $i < $max; $i++) {
            $sel->execute(array($clavis[$i]));
            list($num) = $sel->fetch();
            if (!$num) continue;
            $ins->execute(array($bibl[$i], $clavis[$i]));
        }
        Verbatim::$pdo->commit();

        /* opera */
        $opera = Xml::transformToXml(
            __DIR__ . "/build/galenzot_opera.xsl",
            $dom
        );
        file_put_contents(__DIR__ . "/pages/opera.html", $opera);
        // load opus records

        Verbatim::$pdo->exec("DELETE FROM opus;");
        Verbatim::$pdo->beginTransaction();
        $re = '@<section class="opus" id="([^"]+)">.*?</section>@s';
        preg_match_all($re, $opera, $matches);
        $clavis = $matches[1];
        $bibl = $matches[0];
        $sql = "INSERT INTO opus (clavis, bibl) VALUES (?, ?);";
        $insOpus = Verbatim::$pdo->prepare($sql);
        for ($i = 0, $max = count($bibl); $i < $max; $i++) {
            $insOpus->execute(array($clavis[$i], $bibl[$i]));
        }
        Verbatim::$pdo->commit();

    }
}

