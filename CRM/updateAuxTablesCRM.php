<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

// $sql = "SELECT * FROM AREAS";
// $rs = $db1->getRS($sql);
// var_dump($rs);

// $sql = "SELECT * FROM AREAS";
// $rs = $db2->getRS($sql);
// var_dump($rs);

// die("Temporarily out of service");


/*function updateTable($table, $db1, $dbEpag) {
    echo "Updating $table...<br/>";
    $sql = "DELETE FROM $table";
    $res = $db1->execSQL($sql);
    if ($res===FALSE) { $db1->execSQL("ROLLBACK"); die("DELETE FROM $table FAILED");} 
    $sql = "INSERT INTO $table
    SELECT * FROM epagelma_epagelmatias_crm.$table;";
    $res = $db1->execSQL($sql);
    if ($res===FALSE) { $db1->execSQL("ROLLBACK"); die("INSERT INTO $table FAILED");}
}*/




function insertDataFromSourceToTarget($sourceDBConnection, $targetDBConnection, $sourceTable, $targetTable, $idColumn) {
    echo "Table $sourceTable ... <br/> ";
    
    // Fetch column names from the source and target tables using the getCols helper function
    $sourceColumns = getCols($sourceDBConnection, $sourceTable);
    $targetColumns = getCols($targetDBConnection, $targetTable);
  
    // Find common columns between source and target
    $commonColumns = array_intersect($sourceColumns, $targetColumns);
  
    if (empty($commonColumns)) {
        echo "No common columns found between source and target tables.\n";
        return;
    }
  
    // Fetch data from the source table for the common columns
    $columnsString = implode(', ', $commonColumns);
    $selectQuery = "SELECT $columnsString FROM $sourceTable";
    $sourceData = $sourceDBConnection->getRS($selectQuery);
  
    if (empty($sourceData)) {
        echo "No data found in the source table.\n";
        return;
    }
  
    // Loop through the source data and insert or update each row
    foreach ($sourceData as $row) {
        // Extract the value of the index column (assumed to be 'id')
        $idValue = $row[$idColumn];
  
        // Prepare the columns and values for the INSERT/UPDATE query
        $columnsWithoutId = array_diff($commonColumns, [$idColumn]);
        $columnsStringForInsert = implode(', ', $commonColumns);  // Include the 'id' column
        $placeholders = implode(', ', array_fill(0, count($commonColumns), '?'));  // Include placeholder for 'id'
  
        // Check if the record already exists in the target table based on the id
        $selectIdQuery = "SELECT 1 FROM $targetTable WHERE $idColumn = ?";
        $existingRecord = $targetDBConnection->getRS($selectIdQuery, [$idValue]);
  
        if (empty($existingRecord)) {
            // If no existing record, perform an INSERT (including the id column)
            $insertQuery = "INSERT INTO $targetTable ($columnsStringForInsert) VALUES ($placeholders)";
            $params = [];
            foreach ($commonColumns as $column) {
                $params[] = $row[$column];
            }
            $targetDBConnection->execSQL($insertQuery, $params, true);
            echo "Inserted record with $idColumn = $idValue into $targetTable.<br/>";
        } else {
            // If record exists, perform an UPDATE
            $updateQuery = "UPDATE $targetTable SET ";
            $setClause = [];
            foreach ($columnsWithoutId as $column) {
                $setClause[] = "$column = ?";
            }
            $updateQuery .= implode(', ', $setClause) . " WHERE $idColumn = ?";
            $params = [];
            foreach ($columnsWithoutId as $column) {
                $params[] = $row[$column];
            }
            // Add the ID value to the parameters for the WHERE clause
            $params[] = $idValue;
            $targetDBConnection->execSQL($updateQuery, $params, true);
            // echo "Updated record with $idColumn = $idValue in $targetTable.\n";
        }
    }
  }




// Helper function to get column names from a table
function getCols($dbConnection, $table) {
    // Query to show columns of the table
    $sql = "SHOW COLUMNS FROM " . $table;
    
    // Execute the query using getRS method from DB class
    $columns = $dbConnection->getRS($sql);

    // Initialize an array to store column names
    $cols = [];
    
    // Loop through the result to extract column names
    foreach ($columns as $column) {
        $cols[] = $column['Field'];  // 'Field' is the column name in the result
    }
    
    return $cols;
}





$err = FALSE;
// $db1->execSQL("START TRANSACTION");

echo "<h1>Updating tables from EPAGELMATIAS CRM DB</h1>";

insertDataFromSourceToTarget($db2, $db1, "AREAS", "AREAS", "id");

insertDataFromSourceToTarget($db2, $db1, "CATEGORIES", "CATEGORIES", "id");

insertDataFromSourceToTarget($db2, $db1, "EP_AREAS", "EP_AREAS", "id");

insertDataFromSourceToTarget($db2, $db1, "EP_CATEGORIES", "EP_CATEGORIES", "id");

insertDataFromSourceToTarget($db2, $db1, "EP_CITIES", "EP_CITIES", "id");

insertDataFromSourceToTarget($db2, $db1, "PROFESSIONS", "PROFESSIONS", "id");

insertDataFromSourceToTarget($db2, $db1, "VAT", "VAT", "id");

// updateTable("CATEGORIES", $db1);
// updateTable("EP_AREAS", $db1);
// updateTable("EP_CATEGORIES", $db1);
// updateTable("EP_CITIES", $db1);
// updateTable("PROFESSIONS", $db1);
// updateTable("VAT", $db1);


// $db1->execSQL("COMMIT");

echo "ALL TABLES UPDATED SUCCESSFULLY";

