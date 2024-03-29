<?php
/**
 * This module contains the functions required to carry out various
 * admin tasks.
 * PHP Version 7.1
 * 
 * @package MedRefs
 * @author  Ken Cowles <krcowles29@gmail.com>
 * @license No license to date
 */

/**
 * This function is used in the process of exporting all tables. Note that
 * the pdo is used to gather info, but mysqli is used to create a string value
 * for writing out to the exported database file. Since this is a file write,
 * pdo is not required. For backwards compatibility, mysqli is used for this.
 * This also leaves the db as a sql-compatible file if used by the CLI.
 *
 * @param object $pdo         caller's PDO connection object
 * @param object $mysqli      caller's mysqli db connection link
 * @param string $name        As above
 * @param array  $tables      An array containg table names to export
 * @param string $dwnld       N->not a download; C->changes only; S->site dwnld
 * @param bool   $backup_name Backup name, if used
 * 
 * @return null;
 */
function exportDatabase($pdo, $mysqli, $name, $tables, $dwnld, $backup_name = false)
{
    foreach ($tables as $table) {
        $tbl_data       = $pdo->query("SELECT * FROM {$table}");
        $tbl_fields     = $tbl_data->columnCount();
        $rows_num       = $tbl_data->rowCount();
        $rows           = $tbl_data->fetchAll(PDO::FETCH_NUM);
        // the essence of the CREATE TABLE statement: (tblCreate[1])
        $tblCreate      = $pdo->query('SHOW CREATE TABLE '. $table);
        $showCreate     = $tblCreate->fetch(PDO::FETCH_NUM);
        $content        = (!isset($content) ?  '' : $content) 
            . "\n\n" . $showCreate[1].";\n\n";
        $st_counter = 0;
        foreach ($rows as $row) {
            //when started (and every after 100 command cycle):
            if ($st_counter%100 == 0 || $st_counter == 0) {
                $content .= "\nINSERT INTO " . $table . " VALUES";
            }
            $content .= "\n(";
            for ($j=0; $j<$tbl_fields; $j++) {
                if (is_null($row[$j])) {
                    $content .= "NULL";
                } else {
                    $row[$j] = $mysqli->real_escape_string($row[$j]);
                    if (isset($row[$j])) {
                        $content .= "'" . $row[$j] . "'" ;
                    }
                }
                if ($j<($tbl_fields-1)) {
                    $content.= ',';
                }
            }
            $content .=")";
            //every after 100 command cycle [or at last line] 
            //  ...p.s. but should be inserted 1 cycle eariler
            if ((($st_counter+1)%100 == 0 && $st_counter != 0) 
                || $st_counter+1==$rows_num
            ) {
                $content .= ";";
            } else {
                $content .= ",";
            }
            $st_counter = $st_counter + 1;
        }
        $content .= "\n\n\n";
    }
    // if $name is supplied, use it...
    $backup_name = $backup_name ? $backup_name : $name . ".sql";
    if ($dwnld !== 'N') {
        // save the new db to the standard data directory
        $loc = sys_get_temp_dir() . '/' . $backup_name;
        file_put_contents($loc, $content);
        if ($dwnld === 'C') {
            include 'zipArchive.php';
        } elseif ($dwnld === 'S') {
            include 'buildPhar.php';
        } elseif ($dwnld === 'A') {
            $arch = str_replace(
                "CREATE TABLE", "CREATE TABLE IF NOT EXISTS", $content
            );
            $archive_loc = "../database/" . $backup_name;
            file_put_contents($archive_loc, $arch);
            // Drop the table now that it is saved
            $dropReq = "DROP TABLE {$name};";
            $drop = $pdo->query($dropReq);
            //$drop->execute([$saved_name]);
        } else {
            throw new Exception("Unrecognized parameter in query string");
        }
    } else {
        setcookie("DownloadDisplayed", "1234", time() + 60);
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"".$backup_name."\"");
        echo $content;
        exit;
    }
}
/**
 * This function will create an array of all the tables currently
 * residing in the database. That table can then be used by the caller
 * to display the results. In the specific case where a table is
 * specified for creation (show tables precedes this), then an
 * error message is constructed noting that the tables already 
 * exists. Otherwise, this argument will be an empty string.
 * 
 * @param object $pdo   The database connection
 * @param string $table A table specified for creation
 * 
 * @return array
 */
function showTables($pdo, $table) 
{
    $tbl_list = [];
    $errmsg = '';
    $req = $pdo->query("SHOW TABLES;");
    $tables = $req->fetchALL(PDO::FETCH_NUM);
    foreach ($tables as $row) {
        if ($row[0] === $table) {
            $errmsg .= "You must first DROP {$table}";
        } else {
            array_push($tbl_list, $row[0]); 
        }
    }
    return array($tbl_list, $errmsg);
}
/**
 * This function will list the contents (fields) of the specified
 * table. An array will be constructed whose elements are each
 * an array of cells to appear in the displayed table.
 * 
 * @param object $pdo   the database connection
 * @param string $table the table to be described
 * 
 * @return array
 */
function describeTable($pdo, $table) 
{
    $rows = [];
    $cells = [];
    $desc = $pdo->query("DESCRIBE {$table};");
    $list = $desc->fetchALL(PDO::FETCH_NUM);
    foreach ($list as $row) {
        for ($i=0; $i<count($row); $i++) {
            array_push($cells, $row[$i]);
        }
        array_push($rows, $cells);
        $cells = [];
    }
    return $rows;
}
