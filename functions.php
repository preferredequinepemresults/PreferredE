<?php

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

function fetchRecords($horseName)
{
    global $mysqli;
    $sql = 'SELECT 
    Horse,
    b.sire,
    b.dam,
    Color,
    Sex,
    Gait,
    a.Type,
    ET,
    Datefoal,
    Salecode,
    Price,
    Currency,
    Hip, 
    Day,
    Consno,
    Pemcode,
    Rating,
    Bredto,
    Lastbred,    
    Purlname,
    Purfname,
    Sbcity,
    Sbstate,
    Sbcountry
    FROM sales a
    JOIN damsire b ON a.damsire_Id=b.damsire_ID 
        WHERE a.chorse = "' . $horseName . '" order by a.datefoal DESC,a.saledate DESC';


    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }

    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $json;
}

function fetchRecords_tb($horseName)
{
    global $mysqli;
    $sql = 'SELECT
    Horse,
    b.sire,
    b.dam,
    Color,
    Sex,
    a.Type,
    Datefoal,
    Salecode,
    Price,
    Currency,
    Hip,
    Day,
    Consno,
    Pemcode,
    Rating,
    Bredto,
    Lastbred,
    Purlname,
    Purfname,
    Sbcity,
    Sbstate,
    Sbcountry
    FROM tsales a
    JOIN tdamsire b ON a.damsire_Id=b.damsire_ID
        WHERE a.chorse = "' . $horseName . '" order by a.datefoal DESC,a.saledate DESC';


    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }

    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $json;
}

function fetchOffsprings($damName)
{
    global $mysqli;
    $sql = 'SELECT
    Horse,
    b.sire,
    b.dam,
    Color,
    Sex,
    Gait,
    a.Type,
    ET,
    Datefoal,
    Salecode,
    Price,
    Currency,
    Hip,
    Day,
    Consno,
    Pemcode,
    Rating,
    Bredto,
    Lastbred,    
    Purlname,
    Purfname,
    Sbcity,
    Sbstate,
    Sbcountry
    FROM sales a
    JOIN damsire b ON a.damsire_Id=b.damsire_ID
    WHERE b.dam = "' . $damName . '" order by a.datefoal DESC,a.saledate DESC;';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if ($damName == "") {
        return "";
    }
    return $json;
}

function fetchOffsprings_broodmare($damName, $saleYear)
{
    global $mysqli;
    $sql = 'SELECT
    a.Horse,
    a.Hip,
    a.Sex,
    a.Salecode,
    a.Price,
    a.Rating
FROM sales a
JOIN damsire b ON a.damsire_Id = b.damsire_ID
JOIN sales c ON c.Horse = b.dam
WHERE b.dam = "' . $damName . '" AND c.type = "B"
AND c.lastbred <> "1900-01-01" 
AND a.datefoal >= DATE_ADD(c.lastbred, INTERVAL 11 MONTH)
AND a.datefoal <= DATE_ADD(c.lastbred, INTERVAL 13 MONTH)
AND YEAR(c.lastbred) >= ' . $saleYear . '
AND a.type IN ("Y", "W")
ORDER BY a.datefoal DESC, a.saledate DESC
LIMIT 1;';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if ($damName == "") {
        return "";
    }
    return $json;
}

function fetchOffsprings_broodmare_tb($damName, $saleYear)
{
    global $mysqli;
    $sql = 'SELECT
    a.Horse,
    a.Hip,
    a.Sex,
    a.Salecode,
    a.Price,
    a.Rating
FROM tsales a
JOIN tdamsire b ON a.damsire_Id = b.damsire_ID
JOIN tsales c ON c.Horse = b.dam
WHERE b.dam = "' . $damName . '" AND c.type = "B"
AND c.lastbred <> "1900-01-01" 
AND a.datefoal >= DATE_ADD(c.lastbred, INTERVAL 11 MONTH)
AND a.datefoal <= DATE_ADD(c.lastbred, INTERVAL 13 MONTH)
AND YEAR(c.lastbred) >= ' . $saleYear . '
AND a.type IN ("Y", "W")
ORDER BY a.datefoal DESC, a.saledate DESC
LIMIT 1;';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if ($damName == "") {
        return "";
    }
    return $json;
}

function fetchOffsprings_weanling_tb($damName, $salecode)
{
    global $mysqli;

    // Validate input parameters
    if (empty($damName)) {
        return "";
    }

    // Validate input parameters
    if (empty($salecode)) {
        return "";
    }

    // Prepare the SQL query with the required conditions
    $sql = '
    SELECT
    b.Horse,
    b.Hip,
    b.Sex,
    b.Datefoal,
    b.Salecode,
    b.Price,
    b.Rating,
    b.type AS b_type
    FROM tsales a
    JOIN tsales b ON a.TDAM = b.TDAM
    WHERE LOWER(a.TDAM) = LOWER("' . $damName . '")  -- Case-insensitive comparison
    AND a.Salecode = "' . $salecode . '"
    AND DATEDIFF(b.Saledate, a.Saledate) >= 90
    AND DATEDIFF(b.Saledate, a.Saledate) <= 390
    AND b.type = "Y"
    LIMIT 1;';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $json;
}

function fetchOffsprings_breeze_tb($damName, $salecode)
{
    global $mysqli;

    // Validate input parameters
    if (empty($damName)) {
        return "";
    }

    // Prepare the SQL query with the required conditions
    $sql = '
    SELECT
    b.Horse,
    b.Hip,
    b.Sex,
    b.Datefoal,
    b.Salecode,
    b.Price,
    b.Rating,
    b.type AS b_type
    FROM tsales a
    JOIN tsales b ON a.TDAM = b.TDAM
    WHERE LOWER(a.TDAM) = LOWER("' . $damName . '")  -- Case-insensitive comparison
    AND a.Salecode = "' . $salecode . '"
    AND DATEDIFF(b.Saledate, a.Saledate) >= 1
    AND DATEDIFF(b.Saledate, a.Saledate) <= 365  -- Sale must be within 12 months (365 days) of the dam
    AND b.type = "R"
    LIMIT 1;';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $json;
}

function breezeFromYearlingReport_tb($year, $salecode, $type, $sex, $sire, $sort1, $sort2, $sort3, $sort4, $sort5)
{
    global $mysqli;

    // Validate input parameters
    if (empty($year)) {
        return "";
    }

    $searchParam = "WHERE Price > 0"; // Start with the basic WHERE clause

    // Build the query conditionally based on the provided parameters
    if (!empty($year)) {
        $searchParam .= " AND YEAR(Saledate) = ?";
    }
    if (!empty($salecode)) {
        $searchParam .= " AND Salecode = ?";
    }
    if (!empty($type)) {
        $searchParam .= " AND `Type` = ?";
    }
    if (!empty($sex)) {
        $searchParam .= " AND Sex = ?";
    }
    if (!empty($sire)) {
        $searchParam .= " AND tSire = ?";
    }

    // Define columns that can be sorted
    $sortableColumns = [
        'sire' => 'tSire',
        'dam' => 'TDAM',
        'hip' => 'Hip',
        'sex' => 'Sex',
        'utt' => 'utt',
        'price' => 'Price'
    ];

    // Build ORDER BY clause
    $orderBy = '';
    $sortOrder = [];
    $sortColumns = [$sort1, $sort2, $sort3, $sort4, $sort5];

    // Loop through sort columns and build the ORDER BY part
    $sortIndex = 1;
    // Default sort direction for all columns is ASC unless otherwise specified
    foreach ($sortColumns as $sortColumn) {
        // Set default direction for sorting columns if not provided by the user
        if (empty($sortColumn)) {
            continue;
        }

        // Check if the direction is set for this sort column in GET request, default to 'ASC'
        $direction = isset($_GET["sort{$sortIndex}_order"]) && $_GET["sort{$sortIndex}_order"] == 'DESC' ? 'DESC' : 'ASC';

        // If column exists in the sortable columns, add to order
        if (isset($sortableColumns[strtolower($sortColumn)])) {
            $sortOrder[] = $sortableColumns[strtolower($sortColumn)] . ' ' . $direction;
        }
        $sortIndex++;
    }

    $sql = "
        SELECT
            b.Horse,
            b.Hip,
            b.Sex,
            b.Datefoal,
            b.Salecode,
            b.Price,
            b.Rating,
            b.type AS b_type,
            b.TDAM,
            b.utt,
            b.tSire
        FROM tsales b
        $searchParam
        " . (count($sortOrder) > 0 ? "ORDER BY " . implode(", ", $sortOrder) : "") . " ";

    // Prepare the statement
    if ($stmt = $mysqli->prepare($sql)) {
        // Bind parameters dynamically
        $types = '';
        $params = [];

        // Add parameters based on provided input
        if (!empty($year)) {
            $types .= 's';
            $params[] = $year;
        }
        if (!empty($salecode)) {
            $types .= 's';
            $params[] = $salecode;
        }
        if (!empty($type)) {
            $types .= 's';
            $params[] = $type;
        }
        if (!empty($sex)) {
            $types .= 's';
            $params[] = $sex;
        }
        if (!empty($sire)) {
            $types .= 's';
            $params[] = $sire;
        }

        // Bind the parameters to the statement
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }

        // Execute the query
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all rows as an associative array
        $json = $result->fetch_all(MYSQLI_ASSOC);

        // Close the statement
        $stmt->close();

        return $json;
    } else {
        // Handle error if prepare failed
        printf("Error in preparing statement: %s\n", $mysqli->error);
        return [];
    }
}

function fetchOffsprings_tb($damName)
{
    global $mysqli;
    $sql = 'SELECT
    Horse,
    b.sire,
    b.dam,
    Color,
    Sex,
    a.Type,
    Datefoal,
    Salecode,
    Price,
    Currency,
    Hip,
    Day,
    Consno,
    Pemcode,
    Rating,
    Bredto,
    Lastbred,
    Purlname,
    Purfname,
    Sbcity,
    Sbstate,
    Sbcountry
    FROM tsales a
    JOIN tdamsire b ON a.damsire_Id=b.damsire_ID
    WHERE b.dam = "' . $damName . '" order by a.datefoal DESC,a.saledate DESC;';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if ($damName == "") {
        return "";
    }
    return $json;
}

function getHorseList()
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Horse FROM Sales';  // Select ONLY one, instead of all
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $json;
}

function getDamname($horseName)
{
    global $mysqli;
    $sql = 'SELECT
    b.dam
    FROM sales a
    JOIN damsire b ON a.damsire_Id=b.damsire_ID
    WHERE a.horse = "' . $horseName . '"';  // Select ONLY one, instead of all
    $result = $mysqli->query($sql);
    try {
        $row = $result->fetch_assoc();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    if ($row['dam'] == "") {
        $sql = 'SELECT
        b.damofdam as dam
        FROM sales a
        JOIN damsire b ON a.damsire_Id=b.damsire_ID
        WHERE b.dam = "' . $horseName . '"';  // Select ONLY one, instead of all
        $result = $mysqli->query($sql);
        try {
            $row = $result->fetch_assoc();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        if (!$result) {
            printf("Errormessage: %s\n", $mysqli->error);
        }
    }
    return ($row['dam']);
}

function getDamname_tb($horseName)
{
    global $mysqli;
    $sql = 'SELECT
    b.dam
    FROM tsales a
    JOIN tdamsire b ON a.damsire_Id=b.damsire_ID
    WHERE a.horse = "' . $horseName . '"';  // Select ONLY one, instead of all
    $result = $mysqli->query($sql);
    try {
        $row = $result->fetch_assoc();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    if ($row['dam'] == "") {
        $sql = 'SELECT
        b.damofdam as dam
        FROM tsales a
        JOIN tdamsire b ON a.damsire_Id=b.damsire_ID
        WHERE b.dam = "' . $horseName . '"';  // Select ONLY one, instead of all
        $result = $mysqli->query($sql);
        try {
            $row = $result->fetch_assoc();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        if (!$result) {
            printf("Errormessage: %s\n", $mysqli->error);
        }
    }
    return ($row['dam']);
}

function getTitleData($horseName)
{
    global $mysqli;
    $sql = 'SELECT
    b.sire,
    b.dam,
    b.damofdam
    FROM sales a
    JOIN damsire b ON a.damsire_Id=b.damsire_ID
    WHERE a.horse = "' . $horseName . '"';  // Select ONLY one, instead of all
    $result = $mysqli->query($sql);
    try {
        $row = $result->fetch_assoc();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    //return ($row[sire]." - ".$row[dam]." - ".$row[damofdam]);
    if ($row['dam'] == "") {
        $sql = 'SELECT distinct
        b.sireofdam,
        b.damofdam
        FROM sales a
        JOIN damsire b ON a.damsire_Id=b.damsire_ID
        WHERE b.dam = "' . $horseName . '"';  // Select ONLY one, instead of all
        $result = $mysqli->query($sql);
        try {
            $row = $result->fetch_assoc();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        if (!$result) {
            printf("Errormessage: %s\n", $mysqli->error);
        }
    }
    return $row;
}

function getTitleData_tb($horseName)
{
    global $mysqli;
    $sql = 'SELECT
    b.sire,
    b.dam,
    b.damofdam
    FROM tsales a
    JOIN tdamsire b ON a.damsire_Id=b.damsire_ID
    WHERE a.horse = "' . $horseName . '"';  // Select ONLY one, instead of all
    $result = $mysqli->query($sql);
    try {
        $row = $result->fetch_assoc();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    //return ($row[sire]." - ".$row[dam]." - ".$row[damofdam]);
    if ($row['dam'] == "") {
        $sql = 'SELECT distinct
        b.sireofdam,
        b.damofdam
        FROM tsales a
        JOIN tdamsire b ON a.damsire_Id=b.damsire_ID
        WHERE b.dam = "' . $horseName . '"';  // Select ONLY one, instead of all
        $result = $mysqli->query($sql);
        try {
            $row = $result->fetch_assoc();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        if (!$result) {
            printf("Errormessage: %s\n", $mysqli->error);
        }
    }
    return $row;
}

function getdamofdam($horseName)
{
    global $mysqli;
    $sql = 'SELECT
    b.dam
    FROM sales a
    JOIN damsire b ON a.damsire_Id=b.damsire_ID
    WHERE a.horse = "' . $horseName . '"';  // Select ONLY one, instead of all
    $result = $mysqli->query($sql);
    try {
        $row = $result->fetch_assoc();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }

    return $row['dam'];
}

function getdamofdam_tb($horseName)
{
    global $mysqli;
    $sql = 'SELECT
    b.dam
    FROM tsales a
    JOIN tdamsire b ON a.damsire_Id=b.damsire_ID
    WHERE a.horse = "' . $horseName . '"';  // Select ONLY one, instead of all
    $result = $mysqli->query($sql);
    try {
        $row = $result->fetch_assoc();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }

    return $row['dam'];
}


function fetchConsnoData($consno, $year, $elig, $gait, $sort1, $sort2, $sort3, $sort4, $sort5)
{
    global $mysqli;

    $searchParam = ' AND left(Consno,4)= IF("' . $consno . '"  = "", left(Consno,4), "' . $consno . '")
                     AND YEAR(Saledate)= IF("' . $year . '" = "", YEAR(Saledate), "' . $year . '")
                     AND Elig= IF("' . $elig . '"  = "", Elig, "' . $elig . '")
                     AND Gait= IF("' . $gait . '"  = "", Gait, "' . $gait . '")';

    $sql = 'SELECT
            HIP,
            Horse,
            Sex,
            Color,
            Gait,
            a.Type,
            ET,
            Datefoal,
            Elig,
            b.Dam,
            b.Sireofdam,
            Salecode,
            Consno,
            Saledate,
            a.Day,
            Price,
            Currency,
            Purlname,
            Purfname,
            Rating
            FROM sales a
            JOIN damsire b ON a.damsire_Id=b.damsire_ID
            WHERE PRICE>0 ' . $searchParam;

    //     $join = ') a LEFT JOIN
    //     (SELECT Price AS Rankprice ,(@curRank := @curRank + 1) AS Rank from (
    //     SELECT Price FROM sales a
    //     JOIN damsire b ON a.damsire_Id=b.damsire_ID WHERE TYPE= "Y" AND PRICE>0 ';
    //     $join1 = 'LEFT JOIN
    //     (select price  AS P1,sex AS S1,(@curRank1 := @curRank1 + 1) AS FRank from (
    //              SELECT price, sex FROM sales a
    //              JOIN damsire b ON a.damsire_Id=b.damsire_ID WHERE TYPE= "Y" AND Sex IN ("F","M") AND PRICE>0 ';
    //     $join2 = 'LEFT JOIN
    //     (select price  AS P2,sex AS S2,(@curRank2 := @curRank2 + 1) AS CRank from (
    //              SELECT price, sex FROM sales a
    //              JOIN damsire b ON a.damsire_Id=b.damsire_ID WHERE TYPE= "Y" AND Sex IN ("C","H","G") AND PRICE>0 ';
    //     $searchConsno = ' AND Cosnno="'.$consno.'"';
    //     $searchYear = ' AND YEAR(`SALEDATE`)="'.$year.'"';
    //     $searchElig = ' AND Elig= "'.$elig.'" ';
    //     $searchGait = ' AND Gait= "'.$gait.'" ';

    $orderby1 = ' ORDER BY ' . $sort1;
    $orderby2 = ', ' . $sort2;
    $orderby3 = ', ' . $sort3;
    $orderby4 = ', ' . $sort4;
    $orderby5 = ', ' . $sort5;


    //     $join11 = ' group by Price ORDER BY Price desc) as a,(SELECT @curRank := 0) r) b
    //     on a.price=b.Rankprice '; //in order to do ranking becauserank function doesn't work on server.
    //     $join21 = ' group by price,sex ORDER BY price desc) as a,(SELECT @curRank1 := 0) r) c
    //              on a.price=c.P1 and a.Sex=c.S1 ';
    //     $join31 = ' group by price,sex ORDER BY price desc) as a,(SELECT @curRank2 := 0) r) d
    //              on a.price=d.P2 and a.Sex=d.S2 ';

    //     if ($year != "" && $consno != "" && $elig != "" && $gait != "") {
    //         $sql = $sql.$searchConsno.$searchElig.$searchYear.$searchGait.
    //         $join.$searchConsno.$searchElig.$searchYear.$searchGait.$join11.
    //         $join1.$searchConsno.$searchElig.$searchYear.$searchGait.$join21.
    //         $join2.$searchConsno.$searchElig.$searchYear.$searchGait.$join31;
    //     }elseif ($year != "" && $consno != "" && $elig != "") {
    //         $sql = $sql.$searchConsno.$searchElig.$searchYear.
    //         $join.$searchConsno.$searchElig.$searchYear.$join11.
    //         $join1.$searchConsno.$searchElig.$searchYear.$join21.
    //         $join2.$searchConsno.$searchElig.$searchYear.$join31;
    //     }elseif ($year != "" && $consno != "" && $gait != "") {
    //         $sql = $sql.$searchConsno.$searchGait.$searchYear.
    //         $join.$searchConsno.$searchGait.$searchYear.$join11.
    //         $join1.$searchConsno.$searchGait.$searchYear.$join21.
    //         $join2.$searchConsno.$searchGait.$searchYear.$join31;
    //     }elseif ($year != "" && $elig != "" && $gait != "") {
    //         $sql = $sql.$searchElig.$searchGait.$searchYear.
    //         $join.$searchElig.$searchGait.$searchYear.$join11.
    //         $join1.$searchElig.$searchGait.$searchYear.$join21.
    //         $join2.$searchElig.$searchGait.$searchYear.$join31;
    //     }elseif ($consno != "" && $elig != "" && $gait != "") {
    //         $sql = $sql.$searchConsno.$searchGait.$searchElig.
    //         $join.$searchConsno.$searchGait.$searchElig.$join11.
    //         $join1.$searchConsno.$searchGait.$searchElig.$join21.
    //         $join2.$searchConsno.$searchGait.$searchElig.$join31;
    //     }elseif ($year != "" && $consno != "") {
    //         $sql = $sql.$searchConsno.$searchYear.$join.$searchConsno.$searchYear.$join11.
    //         $join1.$searchConsno.$searchYear.$join21.$join2.$searchConsno.$searchYear.$join31;
    //     }elseif ($consno != "" && $elig != "") {
    //         $sql = $sql.$searchConsno.$searchElig.$join.$searchConsno.$searchElig.$join11.
    //         $join1.$searchConsno.$searchElig.$join21.$join2.$searchConsno.$searchElig.$join31;
    //     }elseif ($year != "" && $elig != "") {
    //         $sql = $sql.$searchElig.$searchYear.$join.$searchElig.$searchYear.$join11.
    //         $join1.$searchElig.$searchYear.$join21.$join2.$searchElig.$searchYear.$join31;
    //     }elseif ($year != "" && $gait != "") {
    //         $sql = $sql.$searchGait.$searchYear.$join.$searchGait.$searchYear.$join11.
    //         $join1.$searchGait.$searchYear.$join21.$join2.$searchGait.$searchYear.$join31;
    //     }elseif ($consno != "" && $gait != "") {
    //         $sql = $sql.$searchGait.$searchConsno.$join.$searchGait.$searchConsno.$join11.
    //         $join1.$searchGait.$searchConsno.$join21.$join2.$searchGait.$searchConsno.$join31;
    //     }elseif ($elig != "" && $gait != "") {
    //         $sql = $sql.$searchGait.$searchElig.$join.$searchGait.$searchElig.$join11.
    //         $join1.$searchGait.$searchElig.$join21.$join2.$searchGait.$searchElig.$join31;
    //     }elseif ($consno != "") {
    //         $sql = $sql.$searchConsno.$join.$searchConsno.$join11.
    //         $join1.$searchConsno.$join21.$join2.$searchConsno.$join31;
    //     }elseif ($year != "") {
    //         $sql = $sql.$searchYear.$join.$searchYear.$join11.$join1.$searchYear.$join21.$join2.$searchYear.$join31;
    //     }elseif ($elig != "") {
    //         $sql = $sql.$searchElig.$join.$searchElig.$join11.$join1.$searchElig.$join21.$join2.$searchElig.$join31;
    //     }


    if ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "" && $sort5 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4 . $orderby5;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3;
    } elseif ($sort1 != "" && $sort2 != "") {
        $sql = $sql . $orderby1 . $orderby2;
    } elseif ($sort1 != "") {
        $sql = $sql . $orderby1;
    }

    //echo $sql;
    $result = mysqli_query($mysqli, $sql);

    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
        echo $sql;
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;

    //Sample query above
    //     'SELECT * FROM (
    //         SELECT HIP, Horse, Sex, Color, Gait, A.Type, ET, Elig, B.Dam, Sireofdam, Salecode, Consno, Saledate, A.Day, Price, CONCAT (Purlname," " ,Purfname) As Buyer, Rating FROM Sales A JOIN Damsire B ON A.damsire_Id=B.damsire_ID WHERE TYPE= "Y" AND PRICE>0 AND B.Sire="A GO GO LAUXMONT"
    //         ) a left join
    //         (select price ,(@curRank := @curRank + 1) AS Ranking from (
    //             SELECT price FROM Sales A
    //             JOIN Damsire B ON A.damsire_Id=B.damsire_ID WHERE TYPE= "Y" AND PRICE>0
    //             AND B.Sire="A GO GO LAUXMONT" group by price ORDER BY price desc) as a,(SELECT @curRank := 0) r) b
    //             on a.price=b.price  ORDER BY A.SaleCode;'
}

function fetchSireData1($sire, $year, $elig, $gait, $sort1, $sort2, $sort3, $sort4, $sort5)
{
    global $mysqli;

    // Escape the input parameters
    $sire = $mysqli->real_escape_string($sire);
    $year = $mysqli->real_escape_string($year);
    $elig = $mysqli->real_escape_string($elig);
    $gait = $mysqli->real_escape_string($gait);
    $sort1 = $mysqli->real_escape_string($sort1);
    $sort2 = $mysqli->real_escape_string($sort2);
    $sort3 = $mysqli->real_escape_string($sort3);
    $sort4 = $mysqli->real_escape_string($sort4);
    $sort5 = $mysqli->real_escape_string($sort5);

    // Construct SQL query
    $sql = "
    WITH RankedSales AS (
        SELECT
            HIP,
            Horse,
            Sex,
            Color,
            Gait,
            Type,
            Datefoal,
            Elig,
            DAM AS Dam,
            Sireofdam AS Sireofdam,
            Salecode,
            Consno,
            Saledate,
            Day,
            Price,
            Currency,
            Purlname,
            Purfname,
            Rating,
            Sire,
            ROW_NUMBER() OVER (PARTITION BY Sire ORDER BY Price DESC) AS RankNum,
            ROW_NUMBER() OVER (PARTITION BY Sire, CASE WHEN Sex IN ('F', 'M') THEN 1 ELSE 0 END ORDER BY Price DESC) AS FRankNum,
            ROW_NUMBER() OVER (PARTITION BY Sire, CASE WHEN Sex IN ('C', 'H', 'G') THEN 1 ELSE 0 END ORDER BY Price DESC) AS CRankNum
        FROM sales
        WHERE TYPE = 'Y'
          AND PRICE > 0
          AND (Sire = COALESCE(NULLIF('$sire', ''), Sire))
          AND YEAR(Saledate) = COALESCE(NULLIF('$year', ''), YEAR(Saledate))
          AND (Elig = COALESCE(NULLIF('$elig', ''), Elig))
          AND (Gait = COALESCE(NULLIF('$gait', ''), Gait))
    )
    SELECT 
        RankNum AS `Rank`,
        FRankNum AS `FRank`,
        CRankNum AS `CRank`,
        HIP,
        Horse,
        Sex,
        Color,
        Gait,
        Type,
        Datefoal,
        Elig,
        Dam,
        Sireofdam,
        Salecode,
        Consno,
        Saledate,
        Day,
        Price,
        Currency,
        Purlname,
        Purfname,
        Rating
    FROM RankedSales
    ";

    // Construct ORDER BY clause
    $orderBy = [];
    if ($sort1) $orderBy[] = $sort1;
    if ($sort2) $orderBy[] = $sort2;
    if ($sort3) $orderBy[] = $sort3;
    if ($sort4) $orderBy[] = $sort4;
    if ($sort5) $orderBy[] = $sort5;

    if (!empty($orderBy)) {
        $sql .= ' ORDER BY ' . implode(', ', $orderBy);
    } else {
        $sql .= ' ORDER BY Sire, Price DESC';
    }

    // Execute query
    $result = mysqli_query($mysqli, $sql);

    if (!$result) {
        printf("Error message: %s\n", $mysqli->error);
        echo $sql;
    }

    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function sanitizeSort(?string $sort, array $allowedSortColumns): ?string
{
    if (empty($sort)) {
        return null;
    }

    // Parse the sort parameter to extract column and direction
    $parts = explode(' ', $sort);
    $column = trim($parts[0]);
    $direction = 'ASC'; // Default direction
    
    // Check for direction in the second part
    if (count($parts) > 1) {
        $dir = strtoupper(trim($parts[1]));
        if ($dir === 'DESC' || $dir === 'ASC') {
            $direction = $dir;
        }
    }

    // Validate column name
    if (in_array($column, $allowedSortColumns, true)) {
        // Special handling for HIP column (needs casting)
        if ($column === 'HIP') {
            return "CAST(HIP AS UNSIGNED) $direction";
        }
        
        // For all other columns, use backticks and include direction
        return "`$column` $direction";
    }
    
    // If column is not in allowed list, return null
    return null;
}

function fetchSireData($sire, $year, $elig, $gait, $sort1, $sort2, $sort3, $sort4, $sort5, $salecode)
{
    global $mysqli;

    // Define allowed sort columns to prevent SQL injection
    $allowedSortColumns = [
        'Rank',
        'FRank',
        'CRank',
        'HIP',
        'Horse',
        'Sex',
        'Color',
        'Gait',
        'Type',
        'Datefoal',
        'Elig',
        'Dam',
        'Sireofdam',
        'Salecode',
        'Consno',
        'Saledate',
        'Day',
        'Price',
        'Currency',
        'Purlname',
        'Purfname',
        'Rating'
    ];

    // Sanitize sort fields
    $sorts = [];
    foreach ([$sort1, $sort2, $sort3, $sort4, $sort5] as $sort) {
        $cleanSort = sanitizeSort($sort, $allowedSortColumns);
        if ($cleanSort) {
            $sorts[] = $cleanSort;
        }
    }

    // Construct ORDER BY clause
    $orderBy = !empty($sorts) ? ' ORDER BY ' . implode(', ', $sorts) : ' ORDER BY Sire, Price DESC';

    // Construct SQL query with placeholders
    $sql = "
    WITH RankedSales AS (
        SELECT
            HIP,
            Horse,
            Sex,
            Color,
            Gait,
            Type,
            Datefoal,
            Elig,
            DAM AS Dam,
            Sireofdam AS Sireofdam,
            Salecode,
            Consno,
            Saledate,
            Day,
            Price,
            Currency,
            Purlname,
            Purfname,
            Rating,
            Sire,
            ROW_NUMBER() OVER (PARTITION BY Sire ORDER BY Price DESC) AS RankNum,
            ROW_NUMBER() OVER (PARTITION BY Sire, CASE WHEN Sex IN ('F', 'M') THEN 1 ELSE 0 END ORDER BY Price DESC) AS FRankNum,
            ROW_NUMBER() OVER (PARTITION BY Sire, CASE WHEN Sex IN ('C', 'H', 'G') THEN 1 ELSE 0 END ORDER BY Price DESC) AS CRankNum
        FROM sales
        WHERE TYPE = 'Y'
          AND PRICE > 0
          AND (Sire = COALESCE(NULLIF(?, ''), Sire))
          AND YEAR(Saledate) = COALESCE(NULLIF(?, ''), YEAR(Saledate))
          AND (Elig = COALESCE(NULLIF(?, ''), Elig))
          AND (Gait = COALESCE(NULLIF(?, ''), Gait))
          AND (Salecode = COALESCE(NULLIF(?, ''), Salecode))
    )
    SELECT 
        RankNum AS `Rank`,
        FRankNum AS `FRank`,
        CRankNum AS `CRank`,
        HIP,
        Horse,
        Sex,
        Color,
        Gait,
        Type,
        Datefoal,
        Elig,
        Dam,
        Sireofdam,
        Salecode,
        Consno,
        Saledate,
        Day,
        Price,
        Currency,
        Purlname,
        Purfname,
        Rating
    FROM RankedSales
    $orderBy
    ";

    // Prepare the statement
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
        return [];
    }

    // Bind parameters
    if (!$stmt->bind_param('sisss', $sire, $year, $elig, $gait, $salecode)) {
        error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        $stmt->close();
        return [];
    }

    // Execute the statement
    if (!$stmt->execute()) {
        error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        echo "Error message: " . $stmt->error;
        echo "<br>SQL Query: " . htmlspecialchars($sql);
        $stmt->close();
        return [];
    }

    // Get the result
    $result = $stmt->get_result();
    if (!$result) {
        error_log("Getting result failed: (" . $stmt->errno . ") " . $stmt->error);
        echo "Error message: " . $stmt->error;
        echo "<br>SQL Query: " . htmlspecialchars($sql);
        $stmt->close();
        return [];
    }

    // Fetch all rows as associative array
    $json = $result->fetch_all(MYSQLI_ASSOC);

    // Free result and close statement
    $result->free();
    $stmt->close();

    return $json;
}

function fetchSireData_tb($sire, $year, $elig, $gait, $sort1, $sort2, $sort3, $sort4, $sort5, $salecode)
{
    global $mysqli;

    // Define allowed sort columns to prevent SQL injection
    $allowedSortColumns = [
        'Rank',
        'FRank',
        'CRank',
        'HIP',
        'Horse',
        'Sex',
        'Color',
        'Gait',
        'Type',
        'Datefoal',
        'Elig',
        'Dam',
        'Sireofdam',
        'Salecode',
        'Consno',
        'Saledate',
        'Day',
        'Price',
        'Currency',
        'Purlname',
        'Purfname',
        'Rating'
    ];

    /**
     * Sanitize sort fields by ensuring they are within the allowed list.
     *
     * @param string|null $sort The sort parameter to sanitize.
     * @param array $allowedSortColumns List of allowed sort columns.
     * @return string|null Returns the sanitized sort column or null if invalid.
     */

    // Sanitize sort fields
    $sorts = [];
    foreach ([$sort1, $sort2, $sort3, $sort4, $sort5] as $sort) {
        $cleanSort = sanitizeSort($sort, $allowedSortColumns);
        if ($cleanSort) {
            $sorts[] = $cleanSort;
        }
    }

    // Construct ORDER BY clause
    $orderBy = !empty($sorts) ? ' ORDER BY ' . implode(', ', $sorts) : ' ORDER BY HIP';

    // Construct SQL query with placeholders
    $sql = "
    WITH RankedSales AS (
        SELECT
            HIP,
            Horse,
            Sex,
            Color,
            Gait,
            Type,
            Datefoal,
            Elig,
            TDAM AS Dam,
            tSireofdam AS Sireofdam,
            Salecode,
            Consno,
            Saledate,
            Day,
            Price,
            Currency,
            Purlname,
            Purfname,
            Rating,
            tSire,
            ROW_NUMBER() OVER (PARTITION BY tSire ORDER BY Price DESC) AS RankNum,
            ROW_NUMBER() OVER (PARTITION BY tSire, CASE WHEN Sex IN ('F', 'M') THEN 1 ELSE 0 END ORDER BY Price DESC) AS FRankNum,
            ROW_NUMBER() OVER (PARTITION BY tSire, CASE WHEN Sex IN ('C', 'H', 'G') THEN 1 ELSE 0 END ORDER BY Price DESC) AS CRankNum
        FROM tsales
        WHERE TYPE = 'Y'
          AND PRICE > 0
          AND (tSire = COALESCE(NULLIF(?, ''), tSire))
          AND YEAR(Saledate) = COALESCE(NULLIF(?, ''), YEAR(Saledate))
          AND (Elig = COALESCE(NULLIF(?, ''), Elig))
          AND (Gait = COALESCE(NULLIF(?, ''), Gait))
          AND (Salecode = COALESCE(NULLIF(?, ''), Salecode))
    )
    SELECT 
        RankNum AS `Rank`,
        FRankNum AS `FRank`,
        CRankNum AS `CRank`,
        HIP,
        Horse,
        Sex,
        Color,
        Gait,
        Type,
        Datefoal,
        Elig,
        Dam,
        Sireofdam,
        Salecode,
        Consno,
        Saledate,
        Day,
        Price,
        Currency,
        Purlname,
        Purfname,
        Rating
    FROM RankedSales
    $orderBy
    ";

    // Prepare the statement
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
        return [];
    }

    // Bind parameters
    // 'ssss' denotes four string parameters; adjust types if necessary (e.g., 'i' for integers)
    if (!$stmt->bind_param('sssss', $sire, $year, $elig, $gait, $salecode)) {
        error_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        $stmt->close();
        return [];
    }

    // Execute the statement
    if (!$stmt->execute()) {
        error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        echo "Error message: " . $stmt->error;
        echo "<br>SQL Query: " . htmlspecialchars($sql);
        $stmt->close();
        return [];
    }

    // Get the result
    $result = $stmt->get_result();
    if (!$result) {
        error_log("Getting result failed: (" . $stmt->errno . ") " . $stmt->error);
        echo "Error message: " . $stmt->error;
        echo "<br>SQL Query: " . htmlspecialchars($sql);
        $stmt->close();
        return [];
    }

    // Fetch all rows as associative array
    $json = $result->fetch_all(MYSQLI_ASSOC);

    // Free result and close statement
    $result->free();
    $stmt->close();

    return $json;
}


function fetchConsAnalysis($consno, $year, $elig, $gait)
{
    global $mysqli;
    $sql = 'SELECT * FROM cons_sales_allyear';


    if ($year != "" && $consno != "" && $elig != "" && $gait != "") {
        $sql = 'SELECT * FROM cons_sales_elig WHERE Consno ="' . $consno . '" AND Year = ' . $year . ' AND
                Elig ="' . $elig . '" AND Gait="' . $gait . '"';
    } elseif ($year != "" && $consno != "" && $gait != "") {
        $sql = 'SELECT * FROM cons_sales_elig WHERE Consno ="' . $consno . '" AND Year = ' . $year . ' AND Gait="' . $gait . '"';
    } elseif ($year != "" && $elig != "" && $gait != "") {
        $sql = 'SELECT * FROM cons_sales_elig WHERE Elig ="' . $elig . '" AND Year = ' . $year . ' AND Gait="' . $gait . '"';
    } elseif ($consno != "" && $elig != "" && $gait != "") {
        $sql = 'SELECT * FROM cons_sales_elig_allyear WHERE Consno ="' . $consno . '" AND Elig = "' . $elig . '" AND Gait="' . $gait . '"';
    } elseif ($year != "" && $consno != "" && $elig != "") {
        $sql = 'SELECT * FROM cons_sales_elig WHERE Consno ="' . $consno . '" AND Year = ' . $year . ' AND Elig ="' . $elig . '"';
    } elseif ($year != "" && $consno != "") {
        $sql = 'SELECT * FROM cons_sales WHERE Consno ="' . $consno . '" AND Year = ' . $year;
    } elseif ($consno != "" && $elig != "") {
        $sql = 'SELECT * FROM cons_sales_elig_allyear WHERE Consno ="' . $consno . '" AND Elig ="' . $elig . '"';
    } elseif ($year != "" && $elig != "") {
        $sql = 'SELECT * FROM cons_sales_elig WHERE Elig ="' . $elig . '" AND Year = ' . $year;
    } elseif ($year != "" && $gait != "") {
        $sql = 'SELECT * FROM cons_sales_elig WHERE Gait ="' . $gait . '" AND Year = ' . $year;
    } elseif ($consno != "" && $gait != "") {
        $sql = 'SELECT * FROM cons_sales_elig_allyear WHERE Gait ="' . $gait . '" AND Consno = ' . $consno;
    } elseif ($elig != "" && $gait != "") {
        $sql = 'SELECT * FROM cons_sales_elig_allyear WHERE Gait ="' . $gait . '" AND Elig = ' . $elig;
    } elseif ($consno != "") {
        $sql = 'SELECT * FROM cons_sales_allyear WHERE Consno ="' . $consno . '"';
    } elseif ($year != "") {
        $sql = 'SELECT * FROM cons_sales WHERE Year = ' . $year;
    } elseif ($elig != "") {
        $sql = 'SELECT * FROM cons_sales_elig_allyear WHERE Elig ="' . $elig . '"';
    }

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
        echo $sql;
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSireAnalysis($sire, $year, $elig, $gait, $salecode)
{
    global $mysqli;
    
    // Escape all inputs for security
    $sire_escaped = mysqli_real_escape_string($mysqli, $sire);
    $year_escaped = mysqli_real_escape_string($mysqli, $year);
    $elig_escaped = mysqli_real_escape_string($mysqli, $elig);
    $gait_escaped = mysqli_real_escape_string($mysqli, $gait);
    $salecode_escaped = mysqli_real_escape_string($mysqli, $salecode);
    
    $sql = 'SELECT * FROM sire_sales_elig_allyear WHERE 1=1';

    // Rules for view selection:
    // - Use sire_sales_elig for queries WITH year filter
    // - Use sire_sales_elig_allyear for queries WITHOUT year filter
    // - Use sire_sales for basic sire/year queries without elig/gait/salecode

    // All 5 filters - WITH year
    if ($year != "" && $sire != "" && $elig != "" && $gait != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig WHERE Sire ="' . $sire_escaped . '" AND Year = ' . $year_escaped . ' AND Elig ="' . $elig_escaped . '" AND Gait="' . $gait_escaped . '" AND Salecode="' . $salecode_escaped . '"';
    }
    // 4 filters WITH year
    elseif ($year != "" && $sire != "" && $elig != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig WHERE Sire ="' . $sire_escaped . '" AND Year = ' . $year_escaped . ' AND Elig ="' . $elig_escaped . '" AND Gait="' . $gait_escaped . '"';
    }
    elseif ($year != "" && $sire != "" && $elig != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig WHERE Sire ="' . $sire_escaped . '" AND Year = ' . $year_escaped . ' AND Elig ="' . $elig_escaped . '" AND Salecode="' . $salecode_escaped . '"';
    }
    elseif ($year != "" && $sire != "" && $gait != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig WHERE Sire ="' . $sire_escaped . '" AND Year = ' . $year_escaped . ' AND Gait="' . $gait_escaped . '" AND Salecode="' . $salecode_escaped . '"';
    }
    elseif ($year != "" && $elig != "" && $gait != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig WHERE Elig ="' . $elig_escaped . '" AND Year = ' . $year_escaped . ' AND Gait="' . $gait_escaped . '" AND Salecode="' . $salecode_escaped . '"';
    }
    // 4 filters WITHOUT year
    elseif ($sire != "" && $elig != "" && $gait != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear WHERE Sire ="' . $sire_escaped . '" AND Elig ="' . $elig_escaped . '" AND Gait="' . $gait_escaped . '" AND Salecode="' . $salecode_escaped . '"';
    }
    // 3 filters WITH year
    elseif ($year != "" && $sire != "" && $elig != "") {
        $sql = 'SELECT * FROM sire_sales_elig WHERE Sire ="' . $sire_escaped . '" AND Year = ' . $year_escaped . ' AND Elig ="' . $elig_escaped . '"';
    }
    elseif ($year != "" && $sire != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig WHERE Sire ="' . $sire_escaped . '" AND Year = ' . $year_escaped . ' AND Gait="' . $gait_escaped . '"';
    }
    elseif ($year != "" && $sire != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig WHERE Sire ="' . $sire_escaped . '" AND Year = ' . $year_escaped . ' AND Salecode="' . $salecode_escaped . '"';
    }
    elseif ($year != "" && $elig != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig WHERE Elig ="' . $elig_escaped . '" AND Year = ' . $year_escaped . ' AND Gait="' . $gait_escaped . '"';
    }
    elseif ($year != "" && $elig != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig WHERE Elig ="' . $elig_escaped . '" AND Year = ' . $year_escaped . ' AND Salecode="' . $salecode_escaped . '"';
    }
    elseif ($year != "" && $gait != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig WHERE Gait ="' . $gait_escaped . '" AND Year = ' . $year_escaped . ' AND Salecode="' . $salecode_escaped . '"';
    }
    // 3 filters WITHOUT year
    elseif ($sire != "" && $elig != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear WHERE Sire ="' . $sire_escaped . '" AND Elig ="' . $elig_escaped . '" AND Gait="' . $gait_escaped . '"';
    }
    elseif ($sire != "" && $elig != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear WHERE Sire ="' . $sire_escaped . '" AND Elig ="' . $elig_escaped . '" AND Salecode="' . $salecode_escaped . '"';
    }
    elseif ($sire != "" && $gait != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear WHERE Sire ="' . $sire_escaped . '" AND Gait="' . $gait_escaped . '" AND Salecode="' . $salecode_escaped . '"';
    }
    elseif ($elig != "" && $gait != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear WHERE Elig ="' . $elig_escaped . '" AND Gait="' . $gait_escaped . '" AND Salecode="' . $salecode_escaped . '"';
    }
    // 2 filters WITH year
    elseif ($year != "" && $sire != "") {
        $sql = 'SELECT * FROM sire_sales WHERE Sire ="' . $sire_escaped . '" AND Year = ' . $year_escaped;
    }
    elseif ($year != "" && $elig != "") {
        $sql = 'SELECT * FROM sire_sales_elig WHERE Elig ="' . $elig_escaped . '" AND Year = ' . $year_escaped;
    }
    elseif ($year != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig WHERE Gait ="' . $gait_escaped . '" AND Year = ' . $year_escaped;
    }
    elseif ($year != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig WHERE Salecode ="' . $salecode_escaped . '" AND Year = ' . $year_escaped;
    }
    // 2 filters WITHOUT year
    elseif ($sire != "" && $elig != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear WHERE Sire ="' . $sire_escaped . '" AND Elig ="' . $elig_escaped . '"';
    }
    elseif ($sire != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear WHERE Sire ="' . $sire_escaped . '" AND Gait="' . $gait_escaped . '"';
    }
    elseif ($sire != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear WHERE Sire ="' . $sire_escaped . '" AND Salecode="' . $salecode_escaped . '"';
    }
    elseif ($elig != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear WHERE Elig ="' . $elig_escaped . '" AND Gait="' . $gait_escaped . '"';
    }
    elseif ($elig != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear WHERE Elig ="' . $elig_escaped . '" AND Salecode="' . $salecode_escaped . '"';
    }
    elseif ($gait != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear WHERE Gait="' . $gait_escaped . '" AND Salecode="' . $salecode_escaped . '"';
    }
    // Single filter
    elseif ($sire != "") {
        $sql = 'SELECT * FROM sire_sales_allyear WHERE Sire ="' . $sire_escaped . '"';
    }
    elseif ($year != "") {
        $sql = 'SELECT * FROM sire_sales WHERE Year = ' . $year_escaped;
    }
    elseif ($elig != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear WHERE Elig ="' . $elig_escaped . '"';
    }
    elseif ($gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear WHERE Gait="' . $gait_escaped . '"';
    }
    elseif ($salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear WHERE Salecode="' . $salecode_escaped . '"';
    }

    // Add ORDER BY for consistent results
    if (strpos($sql, 'sire_sales_elig') !== false && strpos($sql, 'Year') !== false) {
        $sql .= ' ORDER BY Sire, Year, Elig, Gait, Salecode';
    } elseif (strpos($sql, 'sire_sales_elig_allyear') !== false) {
        $sql .= ' ORDER BY Sire, Elig, Gait, Salecode';
    } elseif (strpos($sql, 'sire_sales') !== false) {
        $sql .= ' ORDER BY Sire, Year';
    }

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
        echo "SQL: " . $sql . "<br>";
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSireAnalysis_tb($sire, $year, $elig, $gait, $salecode)
{
    global $mysqli;
    $sql = 'SELECT * FROM sire_sales_allyear_tb';

    if ($year != "" && $sire != "" && $elig != "" && $gait != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig_tb WHERE Sire ="' . $sire . '" AND Year = ' . $year . ' AND Elig ="' . $elig . '" AND Gait="' . $gait . '" AND Salecode="' . $salecode . '"';
    } elseif ($year != "" && $sire != "" && $gait != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig_tb WHERE Sire ="' . $sire . '" AND Year = ' . $year . ' AND Gait="' . $gait . '" AND Salecode="' . $salecode . '"';
    } elseif ($year != "" && $sire != "" && $elig != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig_tb WHERE Sire ="' . $sire . '" AND Year = ' . $year . ' AND Elig ="' . $elig . '" AND Salecode="' . $salecode . '"';
    } elseif ($sire != "" && $elig != "" && $gait != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear_tb WHERE Sire ="' . $sire . '" AND Elig ="' . $elig . '" AND Gait="' . $gait . '" AND Salecode="' . $salecode . '"';
    } elseif ($year != "" && $elig != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig_tb WHERE Elig ="' . $elig . '" AND Year = ' . $year . ' AND Gait="' . $gait . '"';
    } elseif ($year != "" && $sire != "" && $elig != "") {
        $sql = 'SELECT * FROM sire_sales_elig_tb WHERE Sire ="' . $sire . '" AND Year = ' . $year . ' AND Elig ="' . $elig . '"';
    } elseif ($year != "" && $sire != "") {
        $sql = 'SELECT * FROM sire_sales_tb WHERE Sire ="' . $sire . '" AND Year = ' . $year;
    } elseif ($year != "" && $elig != "") {
        $sql = 'SELECT * FROM sire_sales_elig_tb WHERE Elig ="' . $elig . '" AND Year = ' . $year;
    } elseif ($year != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig_tb WHERE Gait ="' . $gait . '" AND Year = ' . $year;
    } elseif ($sire != "" && $elig != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear_tb WHERE Sire ="' . $sire . '" AND Elig ="' . $elig . '"';
    } elseif ($sire != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear_tb WHERE Sire ="' . $sire . '" AND Gait="' . $gait . '"';
    } elseif ($elig != "" && $gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear_tb WHERE Elig ="' . $elig . '" AND Gait="' . $gait . '"';
    } elseif ($sire != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear_tb WHERE Sire ="' . $sire . '" AND Salecode="' . $salecode . '"';
    } elseif ($year != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear_tb WHERE `Year` = "' . $year . '" AND Salecode="' . $salecode . '"';
    } elseif ($elig != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear_tb WHERE Elig ="' . $elig . '" AND Salecode="' . $salecode . '"';
    } elseif ($gait != "" && $salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear_tb WHERE Gait="' . $gait . '" AND Salecode="' . $salecode . '"';
    } elseif ($sire != "") {
        $sql = 'SELECT * FROM sire_sales_allyear_tb WHERE Sire ="' . $sire . '"';
    } elseif ($year != "") {
        $sql = 'SELECT * FROM sire_sales_tb WHERE `Year` = "' . $year . '"';
    } elseif ($elig != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear_tb WHERE Elig ="' . $elig . '"';
    } elseif ($gait != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear_tb WHERE Gait="' . $gait . '"';
    } elseif ($salecode != "") {
        $sql = 'SELECT * FROM sire_sales_elig_allyear_tb WHERE Salecode="' . $salecode . '"';
    }

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
        echo $sql;
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSireAnalysisSummary($year, $elig, $gait, $sort1, $sort2, $sort3, $sort4, $sort5)
{
    global $mysqli;
    $select = 'SELECT 
    Sire,
    Gait,
    Elig,
    Count,
    A.Total,
    A.Avg,
    Top,
    CCount,
    CTotal,
    CAvg,
    CTop,
    FCount,
    FTotal,
    FAvg,
    FTop,
    SireAvgRank,
    SireGrossRank,
    PacerAvgRank,
    PacerGrossRank,
    TrotterAvgRank,
    TrotterGrossRank FROM';

    $sql_elig = $select . ' (
        (SELECT * FROM sire_sales_elig) A
        LEFT JOIN
        (SELECT Avg ,(@CurRank := @CurRank + 1) AS SireAvgRank From (SELECT Avg
            FROM sire_sales_elig WHERE Year=' . $year . ' GROUP BY Avg ORDER BY Avg DESC) as a,(SELECT @curRank := 0) r) B
            ON A.Avg=B.Avg
        LEFT JOIN
        (SELECT Total ,(@CurRank1 := @CurRank1 + 1) AS SireGrossRank From (SELECT Total
            FROM sire_sales_elig WHERE Year=' . $year . ' GROUP BY Total ORDER BY Total DESC) as a,(SELECT @curRank1 := 0) r) C
            ON A.Total=C.Total
        LEFT JOIN
        (SELECT Avg ,(@curRank2 := @curRank2 + 1) AS PacerAvgRank From (SELECT Avg
    		FROM sire_sales_elig WHERE Gait="P" AND Year=' . $year . ' GROUP BY Avg ORDER BY Avg DESC) as a,(SELECT @curRank2 := 0) r) D
            ON A.Avg=D.Avg and A.Gait="P"
        LEFT JOIN
        (SELECT Total ,(@curRank3 := @curRank3 + 1) AS PacerGrossRank From (SELECT Total
    		FROM sire_sales_elig WHERE Gait="P" AND Year=' . $year . ' GROUP BY Total ORDER BY Total DESC) as a,(SELECT @curRank3 := 0) r) E
            ON A.Total=E.Total and A.Gait="P"
        LEFT JOIN
        (SELECT Avg ,(@curRank4 := @curRank4 + 1) AS TrotterAvgRank From (SELECT Avg
    		FROM sire_sales_elig WHERE Gait="T" AND Year=' . $year . ' GROUP BY Avg ORDER BY Avg DESC) as a,(SELECT @curRank4 := 0) r) F
            ON A.Avg=F.Avg and A.Gait="T"
        LEFT JOIN
        (SELECT Total ,(@curRank5 := @curRank5 + 1) AS TrotterGrossRank From (SELECT Total
    		FROM sire_sales_elig WHERE Gait="T" AND Year=' . $year . ' GROUP BY Total ORDER BY Total DESC) as a,(SELECT @curRank5 := 0) r) G
            ON A.Total=G.Total and A.Gait="T")';


    $sql_elig_allyear = $select . ' (
        (SELECT * FROM sire_sales_elig_allyear) A
        LEFT JOIN
        (SELECT Avg ,(@CurRank := @CurRank + 1) AS SireAvgRank From (SELECT Avg
            FROM sire_sales_elig_allyear GROUP BY Avg ORDER BY Avg DESC) as a,(SELECT @curRank := 0) r) B
            ON A.Avg=B.Avg
        LEFT JOIN
        (SELECT Total ,(@CurRank1 := @CurRank1 + 1) AS SireGrossRank From (SELECT Total
            FROM sire_sales_elig_allyear GROUP BY Total ORDER BY Total DESC) as a,(SELECT @curRank1 := 0) r) C
            ON A.Total=C.Total
        LEFT JOIN 
        (SELECT Avg ,(@curRank2 := @curRank2 + 1) AS PacerAvgRank From (SELECT Avg
    		FROM sire_sales_elig_allyear WHERE Gait="P" GROUP BY Avg ORDER BY Avg DESC) as a,(SELECT @curRank2 := 0) r) D
            ON A.Avg=D.Avg and A.Gait="P"
        LEFT JOIN 
        (SELECT Total ,(@curRank3 := @curRank3 + 1) AS PacerGrossRank From (SELECT Total
    		FROM sire_sales_elig_allyear WHERE Gait="P" GROUP BY Total ORDER BY Total DESC) as a,(SELECT @curRank3 := 0) r) E
            ON A.Total=E.Total and A.Gait="P"
        LEFT JOIN 
        (SELECT Avg ,(@curRank4 := @curRank4 + 1) AS TrotterAvgRank From (SELECT Avg
    		FROM sire_sales_elig_allyear WHERE Gait="T" GROUP BY Avg ORDER BY Avg DESC) as a,(SELECT @curRank4 := 0) r) F
            ON A.Avg=F.Avg and A.Gait="T"
        LEFT JOIN 
        (SELECT Total ,(@curRank5 := @curRank5 + 1) AS TrotterGrossRank From (SELECT Total
    		FROM sire_sales_elig_allyear WHERE Gait="T" GROUP BY Total ORDER BY Total DESC) as a,(SELECT @curRank5 := 0) r) G
            ON A.Total=G.Total and A.Gait="T")';
    $sql = $sql_elig_allyear;
    if ($year != "" && $elig != "" && $gait != "") {
        $sql = $sql_elig . ' WHERE Elig ="' . $elig . '" AND Year = ' . $year . ' AND Gait = "' . $gait . '"';
    } elseif ($year != "" && $elig) {
        $sql = $sql_elig . ' WHERE Elig ="' . $elig . '" AND Year = ' . $year;
    } elseif ($elig != "" && $gait != "") {
        $sql = $sql_elig_allyear . ' WHERE Elig ="' . $elig . '" AND Gait = "' . $gait . '"';
    } elseif ($year != "" && $gait != "") {
        $sql = $sql_elig . ' WHERE Gait ="' . $gait . '" AND Year = ' . $year;
    } elseif ($elig != "") {
        $sql = $sql_elig_allyear . ' WHERE Elig ="' . $elig . '"';
    } elseif ($year != "") {
        $sql = $sql_elig . ' WHERE Year = ' . $year;
    } elseif ($gait != "") {
        $sql = $sql_elig_allyear . ' WHERE Gait ="' . $gait . '"';
    }

    $orderby1 = ' ORDER BY ' . $sort1;
    $orderby2 = ', ' . $sort2;
    $orderby3 = ', ' . $sort3;
    $orderby4 = ', ' . $sort4;
    $orderby5 = ', ' . $sort5;

    if ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "" && $sort5 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4 . $orderby5;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3;
    } elseif ($sort1 != "" && $sort2 != "") {
        $sql = $sql . $orderby1 . $orderby2;
    } elseif ($sort1 != "") {
        $sql = $sql . $orderby1;
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}



function fetchSireAnalysisSummary_tb($year, $elig, $sort1, $sort2, $sort3, $sort4, $sort5)
{
    global $mysqli;

    $select = 'SELECT
        Sire,
        Elig,
        Count,
        A.Total,
        A.Avg,
        Top,
        CCount,
        CTotal,
        CAvg,
        CTop,
        FCount,
        FTotal,
        FAvg,
        FTop,
        SireAvgRank,
        SireGrossRank FROM';

    // Updated SQL queries without Gait
    $sql_elig = $select . ' (
        (SELECT * FROM sire_sales_elig_tb) A
        LEFT JOIN
        (SELECT Avg, (@CurRank := @CurRank + 1) AS SireAvgRank FROM (SELECT Avg
            FROM sire_sales_elig_tb WHERE Year=' . $year . ' GROUP BY Avg ORDER BY Avg DESC) AS a, (SELECT @curRank := 0) r) B
            ON A.Avg=B.Avg
        LEFT JOIN
        (SELECT Total, (@CurRank1 := @CurRank1 + 1) AS SireGrossRank FROM (SELECT Total
            FROM sire_sales_elig_tb WHERE Year=' . $year . ' GROUP BY Total ORDER BY Total DESC) AS a, (SELECT @curRank1 := 0) r) C
            ON A.Total=C.Total)';

    $sql_elig_allyear = $select . ' (
        (SELECT * FROM sire_sales_elig_allyear_tb) A
        LEFT JOIN
        (SELECT Avg, (@CurRank := @CurRank + 1) AS SireAvgRank FROM (SELECT Avg
            FROM sire_sales_elig_allyear_tb GROUP BY Avg ORDER BY Avg DESC) AS a, (SELECT @curRank := 0) r) B
            ON A.Avg=B.Avg
        LEFT JOIN
        (SELECT Total, (@CurRank1 := @CurRank1 + 1) AS SireGrossRank FROM (SELECT Total
            FROM sire_sales_elig_allyear_tb GROUP BY Total ORDER BY Total DESC) AS a, (SELECT @curRank1 := 0) r) C
            ON A.Total=C.Total)';

    // Conditional SQL selection without Gait
    $sql = $sql_elig_allyear;

    if ($year != "" && $elig != "") {
        $sql = $sql_elig . ' WHERE Elig ="' . $elig . '" AND Year = ' . $year;
    } elseif ($year != "" && $elig) {
        $sql = $sql_elig . ' WHERE Elig ="' . $elig . '" AND Year = ' . $year;
    } elseif ($elig != "") {
        $sql = $sql_elig_allyear . ' WHERE Elig ="' . $elig . '"';
    } elseif ($year != "") {
        $sql = $sql_elig . ' WHERE Year = ' . $year;
    }

    $sql .= ' GROUP BY Sire, Elig, Count, A.Total, A.Avg, Top, CCount, CTotal, CAvg, CTop, FCount, FTotal, FAvg, FTop, SireAvgRank, SireGrossRank';

    $orderBy = [];
    if ($sort1 != "") $orderBy[] = $sort1;
    if ($sort2 != "") $orderBy[] = $sort2;
    if ($sort3 != "") $orderBy[] = $sort3;
    if ($sort4 != "") $orderBy[] = $sort4;
    if ($sort5 != "") $orderBy[] = $sort5;

    if (!empty($orderBy)) {
        $sql .= ' ORDER BY ' . implode(', ', $orderBy);
    }

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }

    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSireList($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Sire FROM sire_sales';
    if ($year != "") {
        $sql = 'SELECT DISTINCT Sire FROM sire_sales WHERE `Year` = "' . $year . '"';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchConsnoList($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Consno FROM cons_sales';
    if ($year != "") {
        $sql = 'SELECT DISTINCT Consno FROM cons_sales WHERE `Year` = "' . $year . '"';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSireList_tb($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Sire FROM sire_sales_tb';
    if ($year != "") {
        $sql = 'SELECT DISTINCT Sire FROM sire_sales_tb WHERE `Year` = "' . $year . '"';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSireListAll($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Sire FROM damsire';
    //     if ($year != "") {
    //         $sql = 'SELECT DISTINCT Sire FROM sales WHERE Year(saledate) = "'.$year.'"';
    //     }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSireListAll_tb($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Sire FROM tdamsire';
    //     if ($year != "") {
    //         $sql = 'SELECT DISTINCT Sire FROM sales_tb WHERE Year(saledate) = "'.$year.'"';
    //     }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function getSexList()
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Sex FROM sales';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function getSexList_tb()
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Sex FROM tsales';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function getBredtoList($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Bredto FROM sales order by Bredto';
    if ($year != "") {
        $sql = 'SELECT DISTINCT Bredto FROM sales WHERE Year(saledate) = "' . $year . '" order by Bredto';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function getBredtoList_tb($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Bredto FROM tsales';
    if ($year != "") {
        $sql = 'SELECT DISTINCT Bredto FROM tsales WHERE Year(saledate) = "' . $year . '"';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}


function getYearsList()
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Year(saledate) AS `Year` FROM sales ORDER BY Year(saledate) DESC;';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}


function getYearsList_tb()
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Year(saledate) AS `Year` FROM tsales ORDER BY Year(saledate) DESC;';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function getYearsList_tb1()
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Year(saledate) AS `Year` FROM tsales WHERE Type = "W" ORDER BY Year(saledate) DESC;';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function getYearsList_tb_breeze()
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Year(saledate) AS `Year` FROM tsales WHERE Type = "R" ORDER BY Year(saledate) DESC;';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function getEligList()
{
    global $mysqli;
    $sql = 'select distinct Elig FROM sales WHERE PRICE>0 ORDER BY Elig';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function getEligList_tb()
{
    global $mysqli;
    $sql = 'select distinct Elig FROM tsales WHERE PRICE>0 ORDER BY Elig';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function getGaitList()
{
    global $mysqli;
    $sql = 'select distinct Gait FROM sales WHERE PRICE>0 ORDER BY Gait';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function getGaitList_tb()
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Gait FROM tsales WHERE PRICE>0 ORDER BY Gait';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchBuyersReport($salecode, $year, $type, $sort1, $sort2, $sort3, $sort4, $sort5)
{
    global $mysqli;
    $sql = 'SELECT 
    HIP,
    Horse,
    Type,
    Price,
    Currency,
    Salecode,
    Day,
    Purlname,
    Purfname,
    Sbcity,
    Sbstate,
    Sbcountry
    FROM sales WHERE Price>0 ';

    $searchSalecode = ' AND Salecode="' . $salecode . '"';
    $searchYear = ' AND YEAR(`SALEDATE`)=' . $year;
    $searchType = ' AND Type="' . $type . '"';

    $orderby1 = ' ORDER BY ' . $sort1;
    $orderby2 = ', ' . $sort2;
    $orderby3 = ', ' . $sort3;
    $orderby4 = ', ' . $sort4;
    $orderby5 = ', ' . $sort5;

    if ($year != "" && $salecode != "" && $type != "") {
        $sql = $sql . $searchSalecode . $searchType . $searchYear;
    } elseif ($year != "" && $salecode) {
        $sql = $sql . $searchSalecode . $searchYear;
    } elseif ($salecode != "" && $type != "") {
        $sql = $sql . $searchSalecode . $searchType;
    } elseif ($year != "" && $type != "") {
        $sql = $sql . $searchType . $searchYear;
    } elseif ($salecode != "") {
        $sql = $sql . $searchSalecode;
    } elseif ($year != "") {
        $sql = $sql . $searchYear;
    } elseif ($type != "") {
        $sql = $sql . $searchType;
    } else
        return "";

    if ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "" && $sort5 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4 . $orderby5;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3;
    } elseif ($sort1 != "" && $sort2 != "") {
        $sql = $sql . $orderby1 . $orderby2;
    } elseif ($sort1 != "") {
        $sql = $sql . $orderby1;
    }
    $result = mysqli_query($mysqli, $sql);
    //echo $sql;
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error . '--SQL--' . $sql);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchBuyersReport_tb($salecode, $year, $type, $sort1, $sort2, $sort3, $sort4, $sort5)
{
    global $mysqli;
    $sql = 'SELECT
    HIP,
    Horse,
    `Type`,
    tSire,
    tDam,
    Sex,
    Datefoal,
    Price,
    Currency,
    Salecode,
    `Day`,
    Purlname,
    Purfname
    FROM tsales WHERE Price>0 ';

    $searchSalecode = ' AND Salecode="' . $salecode . '"';
    $searchYear = ' AND YEAR(`SALEDATE`)=' . $year;
    $searchType = ' AND Type="' . $type . '"';

    $orderby1 = ' ORDER BY ' . $sort1;
    $orderby2 = ', ' . $sort2;
    $orderby3 = ', ' . $sort3;
    $orderby4 = ', ' . $sort4;
    $orderby5 = ', ' . $sort5;

    if ($year != "" && $salecode != "" && $type != "") {
        $sql = $sql . $searchSalecode . $searchType . $searchYear;
    } elseif ($year != "" && $salecode) {
        $sql = $sql . $searchSalecode . $searchYear;
    } elseif ($salecode != "" && $type != "") {
        $sql = $sql . $searchSalecode . $searchType;
    } elseif ($year != "" && $type != "") {
        $sql = $sql . $searchType . $searchYear;
    } elseif ($salecode != "") {
        $sql = $sql . $searchSalecode;
    } elseif ($year != "") {
        $sql = $sql . $searchYear;
    } elseif ($type != "") {
        $sql = $sql . $searchType;
    }

    if ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "" && $sort5 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4 . $orderby5;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3;
    } elseif ($sort1 != "" && $sort2 != "") {
        $sql = $sql . $orderby1 . $orderby2;
    } elseif ($sort1 != "") {
        $sql = $sql . $orderby1;
    }
    $result = mysqli_query($mysqli, $sql);
    //echo $sql;
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error . '--SQL--' . $sql);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchFarmnameList($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT FARMNAME FROM sales 
            WHERE FARMNAME <> "" 
            ORDER BY FARMNAME';
    if ($year != "") {
        $sql = 'SELECT DISTINCT FARMNAME FROM sales WHERE YEAR(`SALEDATE`) = "' . $year . '"
                AND FARMNAME <> "" 
                ORDER BY FARMNAME';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchFarmnameList_tb($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT FARMNAME FROM tsales 
            WHERE FARMNAME <> "" 
            ORDER BY FARMNAME';
    if ($year != "") {
        $sql = 'SELECT DISTINCT FARMNAME FROM tsales WHERE YEAR(`SALEDATE`) = "' . $year . '"
                AND FARMNAME <> "" 
                ORDER BY FARMNAME';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchFarmcodeList($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT FARMCODE FROM sales 
            WHERE FARMCODE <> "" 
            ORDER BY FARMCODE';
    if ($year != "") {
        $sql = 'SELECT DISTINCT FARMCODE FROM sales WHERE YEAR(`SALEDATE`) = "' . $year . '"
                AND FARMCODE <> "" 
                ORDER BY FARMCODE';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchFarmcodeList_tb($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT FARMCODE FROM tsales 
            WHERE FARMCODE <> "" 
            ORDER BY FARMCODE';
    if ($year != "") {
        $sql = 'SELECT DISTINCT FARMCODE FROM tsales WHERE YEAR(`SALEDATE`) = "' . $year . '"
                AND FARMCODE <> "" 
                ORDER BY FARMCODE';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSalecodeList($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Salecode FROM sales 
            WHERE Salecode<> "" 
            ORDER BY Salecode';
    if ($year != "") {
        $sql = 'SELECT DISTINCT Salecode FROM sales WHERE YEAR(`SALEDATE`) = "' . $year . '"
                AND Salecode<> "" 
                ORDER BY Salecode';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSalecodeList_tb($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Salecode FROM tsales
            WHERE Salecode<> ""
            ORDER BY Salecode';
    if ($year != "") {
        $sql = 'SELECT DISTINCT Salecode FROM tsales WHERE YEAR(`SALEDATE`) = "' . $year . '"
                AND Salecode<> ""
                ORDER BY Salecode';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSalecodeList_tb1($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Salecode FROM tsales
            WHERE Salecode<> ""
            ORDER BY Salecode';
    if ($year != "") {
        $sql = 'SELECT DISTINCT Salecode FROM tsales WHERE YEAR(`SALEDATE`) = "' . $year . '"
                AND Salecode<> ""
                ORDER BY Salecode';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}


function fetchSalecodeWithoutYear($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT left(Salecode,4) as Salecode FROM sales
            WHERE Salecode<> ""
            Group By left(salecode,4)
            ORDER BY Salecode ;';
    if ($year != "") {
        $sql = 'SELECT DISTINCT left(Salecode,4) as Salecode FROM sales WHERE YEAR(`SALEDATE`) = "' . $year . '"
                AND Salecode<> ""
                Group by left(Salecode,4)
                ORDER BY Salecode ';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}


function fetchSalecodeWithoutYear_tb($year)
{
    global $mysqli;
    $sql = 'SELECT DISTINCT left(Salecode,4) as Salecode FROM tsales
            WHERE Salecode<> ""
            Group By left(salecode,4)
            ORDER BY Salecode ;';
    if ($year != "") {
        $sql = 'SELECT DISTINCT left(Salecode,4) as Salecode FROM tsales WHERE YEAR(`SALEDATE`) = "' . $year . '"
                AND Salecode<> ""
                Group by left(Salecode,4)
                ORDER BY Salecode ';
    }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchTypeList()
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Type FROM sales
            WHERE Type<> ""
            ORDER BY Type';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchTypeList_tb()
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Type FROM tsales
            WHERE Type<> ""
            ORDER BY Type';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchBuyerList_tb()
{
    global $mysqli;
    $sql = 'SELECT DISTINCT Purlname FROM tsales
            WHERE Purlname<> ""
            ORDER BY Purlname';
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSalesReport($salecode, $year, $type, $gait, $sex, $sire, $bredto, $sort1, $sort2, $sort3, $sort4, $sort5)
{
    global $mysqli;

    if ($year == "" && $salecode == "" && $type == "" && $gait == "" && $sex == "" && $sire == "" && $bredto == "") {
        return "";
    }
    $searchParam = ' AND YEAR(Saledate)= IF("' . $year . '" = "", YEAR(Saledate), "' . $year . '")
                     AND Salecode= IF("' . $salecode . '"  = "", Salecode, "' . $salecode . '")
                     AND Type= IF("' . $type . '"  = "", Type, "' . $type . '")
                     AND Gait= IF("' . $gait . '"  = "", Gait, "' . $gait . '")
                     AND Sex= IF("' . $sex . '"  = "", Sex, "' . $sex . '")
                     AND b.Sire= IF("' . $sire . '"  = "", b.Sire, "' . $sire . '")
                     AND Bredto= IF("' . $bredto . '"  = "", Bredto, "' . $bredto . '") ';

    $sql = 'SELECT
    HIP,
    Horse,
    Sex,
    Type,
    Gait,
    Price,
    Currency,
    Salecode,
    Day,
    Consno,
    b.Sire,
    b.Dam,
    Bredto,
    LastBred,
    Age,
    Rating
    FROM sales a
    LEFT JOIN damsire b
    ON a.damsire_Id=b.damsire_ID WHERE Price>0 ' . $searchParam;


    //     $searchSalecode = ' AND Salecode="'.$salecode.'"';
    //     $searchYear = ' AND YEAR(`SALEDATE`)='.$year;
    //     $searchType = ' AND Type="'.$type.'"';
    //     $searchGait = ' AND Gait="'.$Gait.'"';

    $orderby1 = ' ORDER BY ' . $sort1;
    $orderby2 = ', ' . $sort2;
    $orderby3 = ', ' . $sort3;
    $orderby4 = ', ' . $sort4;
    $orderby5 = ', ' . $sort5;

    //     if ($year != "" && $salecode != "" && $type != "") {
    //         $sql = $sql.$searchSalecode.$searchType.$searchYear;
    //     }elseif ($year != "" && $salecode) {
    //         $sql = $sql.$searchSalecode.$searchYear;
    //     }elseif ($salecode != "" && $type != "") {
    //         $sql = $sql.$searchSalecode.$searchType;
    //     }elseif ($year != "" && $type != "") {
    //         $sql = $sql.$searchType.$searchYear;
    //     }elseif ($salecode != "") {
    //         $sql = $sql.$searchSalecode;
    //     }elseif ($year != "") {
    //         $sql = $sql.$searchYear;
    //     }elseif ($type != "") {
    //         $sql = $sql.$searchType;
    //     }else
    //         return "";

    if ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "" && $sort5 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4 . $orderby5;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3;
    } elseif ($sort1 != "" && $sort2 != "") {
        $sql = $sql . $orderby1 . $orderby2;
    } elseif ($sort1 != "") {
        $sql = $sql . $orderby1;
    }
    $result = mysqli_query($mysqli, $sql);
    //echo $sql;
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error . '--SQL--' . $sql);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchBroodmaresReport($salecode, $year, $type, $gait, $sex, $sire, $bredto, $sort1, $sort2, $sort3, $sort4, $sort5)
{
    global $mysqli;

    if ($year == "" && $salecode == "" && $type == "" && $gait == "" && $sex == "" && $sire == "" && $bredto == "") {
        return "";
    }
    $searchParam = ' AND YEAR(Saledate)= IF("' . $year . '" = "", YEAR(Saledate), "' . $year . '")
                     AND Salecode= IF("' . $salecode . '"  = "", Salecode, "' . $salecode . '")
                     AND Type= IF("' . $type . '"  = "", Type, "' . $type . '")
                     AND Gait= IF("' . $gait . '"  = "", Gait, "' . $gait . '")
                     AND Sex= IF("' . $sex . '"  = "", Sex, "' . $sex . '")
                     AND b.Sire= IF("' . $sire . '"  = "", b.Sire, "' . $sire . '")
                     AND Bredto= IF("' . $bredto . '"  = "", Bredto, "' . $bredto . '") ';

    $sql = 'SELECT
    HIP,
    Horse,
    Sex,
    Type,
    Gait,
    Price,
    Currency,
    Salecode,
    Day,
    Consno,
    Bredto,
    LastBred,
    Age,
    Rating
    FROM sales a
    LEFT JOIN damsire b
    ON a.damsire_Id=b.damsire_ID WHERE Price>0 ' . $searchParam;


    $orderby1 = ' ORDER BY ' . $sort1;
    $orderby2 = ', ' . $sort2;
    $orderby3 = ', ' . $sort3;
    $orderby4 = ', ' . $sort4;
    $orderby5 = ', ' . $sort5;


    if ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "" && $sort5 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4 . $orderby5;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3;
    } elseif ($sort1 != "" && $sort2 != "") {
        $sql = $sql . $orderby1 . $orderby2;
    } elseif ($sort1 != "") {
        $sql = $sql . $orderby1;
    }
    $result = mysqli_query($mysqli, $sql);
    //echo $sql;
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error . '--SQL--' . $sql);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchWeanlingReport($salecode, $year, $type, $sex, $sire, $sort1, $sort2, $sort3, $sort4, $sort5)
{
    global $mysqli;

    if (empty($year) && empty($salecode) && empty($type) && empty($sex) && empty($sire)) {
        return [];
    }

    // Start building WHERE conditions
    $conditions = ['Price > 0'];
    $params = [];
    $types = '';

    if (!empty($year)) {
        $conditions[] = 'YEAR(Saledate) = ?';
        $params[] = $year;
        $types .= 's';
    }
    if (!empty($salecode)) {
        $conditions[] = 'Salecode = ?';
        $params[] = $salecode;
        $types .= 's';
    }
    if (!empty($type)) {
        $conditions[] = '`Type` = ?';
        $params[] = $type;
        $types .= 's';
    }
    if (!empty($sex)) {
        $conditions[] = 'Sex = ?';
        $params[] = $sex;
        $types .= 's';
    }
    if (!empty($sire)) {
        $conditions[] = 'tSire = ?';
        $params[] = $sire;
        $types .= 's';
    }

    // Define sortable columns
    $sortableColumns = [
        'hip' => 'HIP',
        'horse' => 'Horse',
        'sire' => 'tSire',
        'datefoal' => 'Datefoal',
        'dam' => 'TDAM',
        'sex' => 'Sex',
        'type' => 'Type',
        'price' => 'Price',
        'currency' => 'Currency',
        'salecode' => 'Salecode',
        'day' => 'Day',
        'consno' => 'Consno',
        'saletype' => 'saletype',
        'age' => 'Age',
        'rating' => 'Rating'
    ];

    // Build the SQL query base
    $sql = "
        SELECT
            HIP,
            Horse,
            tSire,
            Datefoal,
            TDAM AS Dam,
            Sex,
            Type,
            Price,
            Salecode,
            Day,
            Consno,
            saletype,
            Age,
            Rating
        FROM tsales
        WHERE " . implode(" AND ", $conditions);

    // Sorting columns from the GET parameters
    $orderConditions = [];
    $sortParams = ['sort1', 'sort2', 'sort3', 'sort4', 'sort5'];
    $sortIndex = 1; // To track sort column

    foreach ($sortParams as $sortParam) {
        if (!empty($$sortParam)) {
            // Check if sort order is specified for each sort column
            $sortOrder = isset($_GET["{$sortParam}_order"]) ? $_GET["{$sortParam}_order"] : 'ASC';

            // Check if this sort column exists in the sortable columns
            $column = strtolower($$sortParam);
            if (isset($sortableColumns[$column])) {
                $orderConditions[] = $sortableColumns[$column] . ' ' . $sortOrder;
            }
        }
        $sortIndex++;
    }

    // If sorting conditions exist, append ORDER BY
    if (!empty($orderConditions)) {
        $sql .= ' ORDER BY ' . implode(', ', $orderConditions);
    }

    // Prepare, bind, and execute the query
    if ($stmt = $mysqli->prepare($sql)) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $json = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $json;
    } else {
        error_log("MySQL Error: " . $mysqli->error . " | SQL: " . $sql);
        return [];
    }
}


function fetchBreezeReport($salecode, $year, $type, $sex, $sire, $sort1, $sort2, $sort3, $sort4, $sort5)
{
    global $mysqli;

    if ($year == "" && $salecode == "" && $type == "" && $sex == "" && $sire == "") {
        return "";
    }

    $searchParam = ' AND YEAR(Saledate)= IF("' . $year . '" = "", YEAR(Saledate), "' . $year . '")
                     AND Salecode= IF("' . $salecode . '"  = "", Salecode, "' . $salecode . '")
                     AND Type= IF("' . $type . '"  = "", Type, "' . $type . '")
                     AND Sex= IF("' . $sex . '"  = "", Sex, "' . $sex . '")
                     AND tSire= IF("' . $sire . '"  = "", tSire, "' . $sire . '") ';

    $sql = 'SELECT
    HIP,
    Horse,
    tSire,
    Datefoal,
    TDAM AS Dam,
    Sex,
    Type,
    Price,
    Salecode,
    Day,
    Consno,
    saletype,
    Age,
    Rating,
    Purlname,
    Purfname
    FROM tsales a
    WHERE Price>0' . $searchParam;


    $orderby1 = ' ORDER BY ' . $sort1 . ' ASC';
    $orderby2 = ', ' . $sort2 . ' ASC';
    $orderby3 = ', ' . $sort3 . ' ASC';
    $orderby4 = ', ' . $sort4 . ' ASC';
    $orderby5 = ', ' . $sort5 . ' ASC';


    if ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "" && $sort5 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4 . $orderby5;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3;
    } elseif ($sort1 != "" && $sort2 != "") {
        $sql = $sql . $orderby1 . $orderby2;
    } elseif ($sort1 != "") {
        $sql = $sql . $orderby1;
    }
    $result = mysqli_query($mysqli, $sql);
    //echo $sql;
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error . '--SQL--' . $sql);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchBreezeSoldAsYearling($salecode, $dam)
{
    global $mysqli;

    // Check if the parameters are empty
    if ($dam == "" && $salecode == "") {
        return [];  // Return an empty array if no parameters are provided
    }

    // SQL query with placeholders
    $sql = '
        SELECT
            b.HIP,
            b.Datefoal,
            b.TDAM AS Dam,
            b.Sex,
            b.type,
            b.Price,
            b.Salecode,
            b.Day,
            b.Consno,
            b.saletype,
            b.Age,
            b.Rating,
            b.Purlname,
            b.Purfname
        FROM tsales a
        JOIN tsales b ON a.TDAM = b.TDAM
        WHERE LOWER(a.TDAM) = LOWER(?)  -- Case-insensitive comparison
        AND a.Salecode = ?  -- Sale code of the dam
        AND b.Saledate < a.Saledate  -- Find sales before the latest sale date
        AND b.Saledate > STR_TO_DATE(a.Datefoal, "%Y-%m-%d")  -- Ensure the sale date is after the foals birth date
        AND DATEDIFF(a.Saledate, b.Saledate) <= 365        AND b.type = "Y"  -- Ensure the sale type is "Y" for yearling
        AND MONTH(b.Saledate) BETWEEN 7 AND 12  -- July to December
        ORDER BY b.Saledate DESC  -- Order the results to get the most recent sales first
        LIMIT 1;';

    // Prepare the statement
    if ($stmt = mysqli_prepare($mysqli, $sql)) {

        // Bind the input parameters
        mysqli_stmt_bind_param($stmt, "ss", $dam, $salecode);  // "ss" indicates two string parameters

        // Execute the prepared statement
        mysqli_stmt_execute($stmt);

        // Store the result
        $result = mysqli_stmt_get_result($stmt);

        // Check if the result contains any rows
        if ($result && mysqli_num_rows($result) > 0) {
            // Fetch all the rows and return as an associative array
            $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
            return $json;
        } else {
            // Handle the case where no results were found
            return [];  // Return an empty array if no data is found
        }
    } else {
        // If there's an error in preparing the statement, print the error
        printf("Error preparing query: %s\n", $mysqli->error);
        return [];  // Return an empty array on failure
    }
}


function fetchBroodmaresReport_tb($salecode, $year, $type, $gait, $sex, $sire, $bredto, $sort1, $sort2, $sort3, $sort4, $sort5)
{
    global $mysqli;

    if ($year == "" && $salecode == "" && $type == "" && $gait == "" && $sex == "" && $sire == "" && $bredto == "") {
        return "";
    }
    $searchParam = ' AND YEAR(Saledate)= IF("' . $year . '" = "", YEAR(Saledate), "' . $year . '")
                     AND Salecode= IF("' . $salecode . '"  = "", Salecode, "' . $salecode . '")
                     AND Type= IF("' . $type . '"  = "", Type, "' . $type . '")
                     AND Gait= IF("' . $gait . '"  = "", Gait, "' . $gait . '")
                     AND Sex= IF("' . $sex . '"  = "", Sex, "' . $sex . '")
                     AND b.Sire= IF("' . $sire . '"  = "", b.Sire, "' . $sire . '")
                     AND Bredto= IF("' . $bredto . '"  = "", Bredto, "' . $bredto . '") ';

    $sql = 'SELECT
    HIP,
    Horse,
    Sex,
    Type,
    Gait,
    Price,
    Currency,
    Salecode,
    Day,
    Consno,


    Bredto,
    LastBred,
    Age,
    Rating
    FROM tsales a
    LEFT JOIN tdamsire b
    ON a.damsire_Id=b.damsire_ID WHERE Price>0 ' . $searchParam;

    $orderby1 = ' ORDER BY ' . $sort1;
    $orderby2 = ', ' . $sort2;
    $orderby3 = ', ' . $sort3;
    $orderby4 = ', ' . $sort4;
    $orderby5 = ', ' . $sort5;

    if ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "" && $sort5 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4 . $orderby5;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3;
    } elseif ($sort1 != "" && $sort2 != "") {
        $sql = $sql . $orderby1 . $orderby2;
    } elseif ($sort1 != "") {
        $sql = $sql . $orderby1;
    }
    $result = mysqli_query($mysqli, $sql);
    //echo $sql;
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error . '--SQL--' . $sql);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSalesReport_tb($salecode, $year, $type, $buyer, $sort1, $sort2, $sort3, $sort4, $sort5)
{
    global $mysqli;

    // Sanitize inputs
    $salecode = mysqli_real_escape_string($mysqli, $salecode);
    $year = mysqli_real_escape_string($mysqli, $year);
    $type = mysqli_real_escape_string($mysqli, $type);
    $buyer = mysqli_real_escape_string($mysqli, $buyer);

    // Start building the search query
    $searchParam = 'WHERE Price > 0';

    // Add conditions based on input
    if (!empty($year)) {
        $searchParam .= ' AND YEAR(Saledate) = ' . $year;
    }
    if (!empty($salecode)) {
        $searchParam .= ' AND Salecode = "' . $salecode . '"';
    }
    if (!empty($type)) {
        $searchParam .= ' AND Type = "' . $type . '"';
    }
    if (!empty($buyer)) {
        $searchParam .= ' AND Purlname = "' . $buyer . '"';
    }

    // Start building the SQL query
    $sql = 'SELECT
        HIP,
        Horse,
        Purfname,
        Purlname,
        `Type`,
        Datefoal,
        Price,
        Currency,
        Salecode,
        `Day`,
        b.Sire,
        b.Dam,
        Bredto,
        IF(LastBred = "1900-01-01", NULL, LastBred) AS LastBred,
        Age,
        Rating
    FROM tsales a
    LEFT JOIN tdamsire b ON a.damsire_Id = b.damsire_ID ' . $searchParam;

    // Dynamically add sorting based on inputs
    $sortFields = ['Purfname', 'Purlname', 'Hip', 'Horse', 'Type', 'Price', 'Salecode', 'Day', 'Sire', 'Dam', 'Bredto', 'Lastbred', 'Age', 'Rating'];
    $sortParams = [];
    if (!empty($sort1) && in_array($sort1, $sortFields)) {
        $sortParams[] = $sort1;
    }
    if (!empty($sort2) && in_array($sort2, $sortFields)) {
        $sortParams[] = $sort2;
    }
    if (!empty($sort3) && in_array($sort3, $sortFields)) {
        $sortParams[] = $sort3;
    }
    if (!empty($sort4) && in_array($sort4, $sortFields)) {
        $sortParams[] = $sort4;
    }
    if (!empty($sort5) && in_array($sort5, $sortFields)) {
        $sortParams[] = $sort5;
    }

    // Append sorting if any valid sort fields are given
    if (count($sortParams) > 0) {
        $sql .= ' ORDER BY ' . implode(', ', $sortParams);
    }

    // Debugging: Print the SQL query
    //echo $sql; // This will print the SQL query before execution. Remove after debugging.

    // Execute the query
    $result = mysqli_query($mysqli, $sql);

    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error . '--SQL--' . $sql);
        return [];
    }

    // Fetch all results as an associative array
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $json;
}

function fetchSalesAuctionReport($year, $type, $salecode)
{
    global $mysqli;

    $searchParam = ' AND YEAR(Saledate)= IF("' . $year . '" = "", YEAR(Saledate), "' . $year . '")
                     AND Type= IF("' . $type . '"  = "", Type, "' . $type . '")
                     AND left(Salecode,4)= IF("' . $salecode . '"  = "", left(Salecode,4), "' . $salecode . '") ';

    $sql = 'SELECT Salecode,Total, Gross, Avg, CONCAT(Total1, " - ",ROUND(Total1/Total*100,1)) AS V1,
            CONCAT(Total2, " - ",ROUND(Total2/Total*100,1)) AS V2,
            CONCAT(Total3, " - ",ROUND(Total3/Total*100,1)) AS V3, CONCAT(Total4, " - ",ROUND(Total4/Total*100,1)) AS V4, 
            CONCAT(Total5, " - ",ROUND(Total5/Total*100,1)) AS V5, CONCAT(Total6, " - ",ROUND(Total6/Total*100,1)) AS V6  FROM
            (SELECT Salecode,count(*) AS Total,sum(price) as Gross, avg(price) AS Avg FROM sales WHERE
            price>0 ' . $searchParam . ' group by salecode) A
            LEFT JOIN 
            (SELECT Salecode AS SC1,count(*) AS Total1 FROM sales WHERE 
            price>=100000 ' . $searchParam . ' group by salecode) B
            ON A.Salecode=B.SC1
            LEFT JOIN
            (SELECT Salecode AS SC2,count(*) AS Total2 FROM sales WHERE 
            price>=50000 and price<=99999 ' . $searchParam . ' group by salecode) C
            ON A.Salecode=C.SC2
            LEFT JOIN
            (SELECT Salecode AS SC3,count(*) AS Total3 FROM sales WHERE 
            price>=25000 and price<=49999 ' . $searchParam . ' group by salecode) D
            ON A.Salecode=D.SC3
            LEFT JOIN 
            (SELECT Salecode AS SC4,count(*) AS Total4 FROM sales WHERE 
            price>=10001 and price<=24999 ' . $searchParam . ' group by salecode) E
            ON A.Salecode=E.SC4
            LEFT JOIN 
            (SELECT Salecode AS SC5,count(*) AS Total5 FROM sales WHERE 
            price>=5000 and price<=10000 ' . $searchParam . ' group by salecode) F
            ON A.Salecode=F.SC5
            LEFT JOIN 
            (SELECT Salecode AS SC6,count(*) AS Total6 FROM sales WHERE 
            price<=4999 and price>0 ' . $searchParam . ' group by salecode) G
            ON A.Salecode=G.SC6';
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}


function fetchSalesAuctionReport_tb($year, $type, $salecode)
{
    global $mysqli;

    $searchParam = ' AND YEAR(Saledate)= IF("' . $year . '" = "", YEAR(Saledate), "' . $year . '")
                     AND Type= IF("' . $type . '"  = "", Type, "' . $type . '")
                     AND Salecode= IF("' . $salecode . '"  = "", Salecode, "' . $salecode . '") ';

    $sql = 'SELECT Salecode,Total, Gross, Avg, CONCAT(Total1, " - ",ROUND(Total1/Total*100,1)) AS V1,
            CONCAT(Total2, " - ",ROUND(Total2/Total*100,1)) AS V2,
            CONCAT(Total3, " - ",ROUND(Total3/Total*100,1)) AS V3, CONCAT(Total4, " - ",ROUND(Total4/Total*100,1)) AS V4,
            CONCAT(Total5, " - ",ROUND(Total5/Total*100,1)) AS V5, CONCAT(Total6, " - ",ROUND(Total6/Total*100,1)) AS V6  FROM
            (SELECT Salecode,count(*) AS Total,sum(price) as Gross, avg(price) AS Avg FROM tsales WHERE
            price>0 ' . $searchParam . ' group by salecode) A
            LEFT JOIN
            (SELECT Salecode AS SC1,count(*) AS Total1 FROM tsales WHERE
            price>=100000 ' . $searchParam . ' group by salecode) B
            ON A.Salecode=B.SC1
            LEFT JOIN
            (SELECT Salecode AS SC2,count(*) AS Total2 FROM tsales WHERE
            price>=50000 and price<=99999 ' . $searchParam . ' group by salecode) C
            ON A.Salecode=C.SC2
            LEFT JOIN
            (SELECT Salecode AS SC3,count(*) AS Total3 FROM tsales WHERE
            price>=25000 and price<=49999 ' . $searchParam . ' group by salecode) D
            ON A.Salecode=D.SC3
            LEFT JOIN
            (SELECT Salecode AS SC4,count(*) AS Total4 FROM tsales WHERE
            price>=10001 and price<=24999 ' . $searchParam . ' group by salecode) E
            ON A.Salecode=E.SC4
            LEFT JOIN
            (SELECT Salecode AS SC5,count(*) AS Total5 FROM tsales WHERE
            price>=5000 and price<=10000 ' . $searchParam . ' group by salecode) F
            ON A.Salecode=F.SC5
            LEFT JOIN
            (SELECT Salecode AS SC6,count(*) AS Total6 FROM tsales WHERE
            price<=4999 and price>0 ' . $searchParam . ' group by salecode) G
            ON A.Salecode=G.SC6';
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSalesSummary($year,$type,$salecode)
{
    global $mysqli;
    
    $searchParam = ' AND YEAR(Saledate)= IF("'.$year.'" = "", YEAR(Saledate), "'.$year.'")
                     AND Type= IF("'.$type.'"  = "", Type, "'.$type.'") 
                     AND left(Salecode,4)= IF("'.$salecode.'"  = "", left(Salecode,4), "'.$salecode.'") ';
    
    $sql = 'SELECT a.Salecode,a.Horse As PACER, a.Max AS PMax,b.Horse As Trotter,b.Max As TMax FROM 
    (SELECT Salecode, Horse, Price as Max FROM sales WHERE GAIT ="P" '.$searchParam.'
        GROUP BY Salecode,horse ORDER BY salecode,Price Desc) a
    LEFT JOIN 
    (SELECT Salecode, Horse, Price AS Max FROM sales WHERE GAIT ="T" '.$searchParam.'
        GROUP BY Salecode,Horse ORDER BY Salecode,Price Desc) b on a.Salecode=b.Salecode Group by salecode';

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $json;
}

function fetchSalesSummary_tb($year, $type, $salecode)
{
    global $mysqli;

    $searchParam = ' AND YEAR(Saledate)= IF("' . $year . '" = "", YEAR(Saledate), "' . $year . '")
                     AND Type= IF("' . $type . '"  = "", Type, "' . $type . '")
                     AND left(Salecode,4)= IF("' . $salecode . '"  = "", left(Salecode,4), "' . $salecode . '") ';

    $sql = 'SELECT 
    a.Salecode,
    MAX(a.Horse) AS PACER,
    a.PMax,
    MAX(b.Horse) AS Trotter,
    b.TMax
FROM (
    SELECT 
        Salecode,
        Horse,
        MAX(Price) AS PMax
    FROM 
        tsales 
    WHERE 
        GAIT = "P" ' . $searchParam . '
    GROUP BY 
        Salecode, Horse
) AS a
LEFT JOIN (
    SELECT 
        Salecode,
        Horse,
        MAX(Price) AS TMax
    FROM 
        tsales 
    WHERE 
        GAIT = "T" ' . $searchParam . '
    GROUP BY 
        Salecode, Horse
) AS b ON a.Salecode = b.Salecode 
GROUP BY 
    a.Salecode, a.PMax, b.TMax;';

    //     if ($year != "") {
    //         $sql = "SELECT a.Salecode,a.Horse As PACER, a.Max AS PMax,b.Horse As Trotter,b.Max As TMax FROM
    //     (SELECT Salecode, Horse, MAX(Price) AS Max FROM sales WHERE GAIT ='P' AND Type='Y' AND year(Saledate)=".$year."
    //         GROUP BY Salecode ORDER BY Salecode) a
    //     LEFT JOIN
    //     (SELECT Salecode, Horse, MAX(Price) AS Max FROM sales WHERE GAIT ='T' AND Type='Y' AND year(Saledate)=".$year."
    //         GROUP BY Salecode ORDER BY Salecode) b on a.Salecode=b.Salecode";
    //     }
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchTopBuyers($year, $sort1, $sort2, $sort3, $sort4, $sort5)
{
    global $mysqli;
    //     if ($year=="") {
    //         $year=null;
    //     } 
    $sql = 'SELECT CONCAT(Purlname," ", Purfname) AS BuyerFullName, count(*) AS Total,SUM(Price) AS Gross,ROUND(Avg(Price),0) AS Avg
FROM sales WHERE Type="Y" AND Price>0 AND YEAR(Saledate)= IF("' . $year . '" = "", YEAR(Saledate), "' . $year . '") GROUP BY CONCAT(Purlname," ",Purfname)';


    //     if ($year != "") {
    //         $sql = 'SELECT Purlname AS BuyerLastName,Purfname AS BuyerFirstName,count(*) AS Total,SUM(Price) AS Gross,ROUND(Avg(Price),0) AS Avg
    //         FROM sales WHERE Type="Y" AND Price>0 AND YEAR(Saledate)='.$year.'
    //         GROUP BY CONCAT(Purlname," ",Purfname)';
    //     }
    $orderby1 = ' ORDER BY ' . $sort1;
    $orderby2 = ', ' . $sort2;
    $orderby3 = ', ' . $sort3;
    $orderby4 = ', ' . $sort4;
    $orderby5 = ', ' . $sort5;
    if ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "" && $sort5 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4 . $orderby5;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3;
    } elseif ($sort1 != "" && $sort2 != "") {
        $sql = $sql . $orderby1 . $orderby2;
    } elseif ($sort1 != "") {
        $sql = $sql . $orderby1;
    }

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}


function fetchTopYearlingBuyers_tb($year, $sort1, $sort2, $sort3, $sort4, $sort5)
{
    global $mysqli;
    //     if ($year=="") {
    //         $year=null;
    //     }
    $sql = 'SELECT CONCAT(Purlname," ", Purfname) AS BuyerFullName, count(*) AS Total, IFNULL(CAST(SUM(Price) AS DECIMAL(10, 2)), 0) AS Gross,  -- Casting to DECIMAL
       IFNULL(ROUND(AVG(Price), 0), 0) AS Avg, GROUP_CONCAT(CONCAT(Salecode, " - ", Hip) SEPARATOR ", ") AS Hips
    FROM tsales WHERE Saletype="Y" AND Price>0 AND YEAR(Saledate)= IF("' . $year . '" = "", YEAR(Saledate), "' . $year . '") GROUP BY CONCAT(Purlname," ",Purfname)';

    //     if ($year != "") {
    //         $sql = 'SELECT Purlname AS BuyerLastName,Purfname AS BuyerFirstName,count(*) AS Total,SUM(Price) AS Gross,ROUND(Avg(Price),0) AS Avg
    //         FROM sales WHERE Type="Y" AND Price>0 AND YEAR(Saledate)='.$year.'
    //         GROUP BY CONCAT(Purlname," ",Purfname)';
    //     }
    $orderby1 = ' ORDER BY ' . $sort1;
    $orderby2 = ', ' . $sort2;
    $orderby3 = ', ' . $sort3;
    $orderby4 = ', ' . $sort4;
    $orderby5 = ', ' . $sort5;
    if ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "" && $sort5 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4 . $orderby5;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3;
    } elseif ($sort1 != "" && $sort2 != "") {
        $sql = $sql . $orderby1 . $orderby2;
    } elseif ($sort1 != "") {
        $sql = $sql . $orderby1;
    }

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchAllTopBuyers_tb($year, $sort1, $sort2, $sort3, $sort4, $sort5)
{
    global $mysqli;
    //     if ($year=="") {
    //         $year=null;
    //     }
    $sql = 'SELECT CONCAT(Purlname," ", Purfname) AS BuyerFullName, count(*) AS Total, IFNULL(CAST(SUM(Price) AS DECIMAL(10, 2)), 0) AS Gross,  -- Casting to DECIMAL
       IFNULL(ROUND(AVG(Price), 0), 0) AS Avg, GROUP_CONCAT(CONCAT(Salecode, " - ", Hip) SEPARATOR ", ") AS Hips
    FROM tsales WHERE  Saletype="Y" AND Saletype="M" AND Price>0 AND YEAR(Saledate)= IF("' . $year . '" = "", YEAR(Saledate), "' . $year . '") GROUP BY CONCAT(Purlname," ",Purfname)';

    //     if ($year != "") {
    //         $sql = 'SELECT Purlname AS BuyerLastName,Purfname AS BuyerFirstName,count(*) AS Total,SUM(Price) AS Gross,ROUND(Avg(Price),0) AS Avg
    //         FROM sales WHERE Type="Y" AND Price>0 AND YEAR(Saledate)='.$year.'
    //         GROUP BY CONCAT(Purlname," ",Purfname)';
    //     }
    $orderby1 = ' ORDER BY ' . $sort1;
    $orderby2 = ', ' . $sort2;
    $orderby3 = ', ' . $sort3;
    $orderby4 = ', ' . $sort4;
    $orderby5 = ', ' . $sort5;
    if ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "" && $sort5 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4 . $orderby5;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3;
    } elseif ($sort1 != "" && $sort2 != "") {
        $sql = $sql . $orderby1 . $orderby2;
    } elseif ($sort1 != "") {
        $sql = $sql . $orderby1;
    }

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchTopMixedBuyers_tb($year, $sort1, $sort2, $sort3, $sort4, $sort5)
{
    global $mysqli;
    //     if ($year=="") {
    //         $year=null;
    //     }
    $sql = 'SELECT CONCAT(Purlname," ", Purfname) AS BuyerFullName, count(*) AS Total, IFNULL(CAST(SUM(Price) AS DECIMAL(10, 2)), 0) AS Gross,  -- Casting to DECIMAL
       IFNULL(ROUND(AVG(Price), 0), 0) AS Avg, GROUP_CONCAT(CONCAT(Salecode, " - ", Hip) SEPARATOR ", ") AS Hips
    FROM tsales WHERE Saletype="M" AND Price>0 AND YEAR(Saledate)= IF("' . $year . '" = "", YEAR(Saledate), "' . $year . '") GROUP BY CONCAT(Purlname," ",Purfname)';

    //     if ($year != "") {
    //         $sql = 'SELECT Purlname AS BuyerLastName,Purfname AS BuyerFirstName,count(*) AS Total,SUM(Price) AS Gross,ROUND(Avg(Price),0) AS Avg
    //         FROM sales WHERE Type="Y" AND Price>0 AND YEAR(Saledate)='.$year.'
    //         GROUP BY CONCAT(Purlname," ",Purfname)';
    //     }
    $orderby1 = ' ORDER BY ' . $sort1;
    $orderby2 = ', ' . $sort2;
    $orderby3 = ', ' . $sort3;
    $orderby4 = ', ' . $sort4;
    $orderby5 = ', ' . $sort5;
    if ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "" && $sort5 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4 . $orderby5;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3;
    } elseif ($sort1 != "" && $sort2 != "") {
        $sql = $sql . $orderby1 . $orderby2;
    } elseif ($sort1 != "") {
        $sql = $sql . $orderby1;
    }

    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchIndividualSaleData($year, $salecode, $type, $elig, $gait, $sort1, $sort2, $sort3, $sort4, $sort5)
{
    global $mysqli;
    if ($year == "" && $salecode == "" && $type == "" && $elig == "" && $gait == "") {
        return "";
    }

    $searchParam = ' AND YEAR(Saledate)= IF("' . $year . '" = "", YEAR(Saledate), "' . $year . '")
                     AND Salecode= IF("' . $salecode . '"  = "", Salecode, "' . $salecode . '")
                     AND Type= IF("' . $type . '"  = "", Type, "' . $type . '")
                     AND Elig= IF("' . $elig . '"  = "", Elig, "' . $elig . '")
                     AND Gait= IF("' . $gait . '"  = "", Gait, "' . $gait . '") ';

    $sql =
        'SELECT ORank,Frank,CRank, HIP, Horse, Sex, Color, Gait, Type, ET,Datefoal, Elig, Sire, Dam, Salecode, Consno, Saledate, Day,
        a.Price, Currency, Purlname, Purfname, Rating FROM (
        SELECT
        HIP,
        Horse,
        Sex,
        Color,
        Gait,
        a.Type,
        ET,
        Datefoal,
        Elig,
        b.Sire,
        b.Dam,
        Salecode,
        Consno,
        Saledate,
        a.Day,
        Price,
        Currency,
        Purlname,
        Purfname,
        Rating
        FROM sales a
        JOIN damsire b ON a.damsire_Id=b.damsire_ID
        WHERE PRICE>0 ' . $searchParam . ') a 
    LEFT JOIN
    (SELECT Price AS Rankprice ,(@curRank := @curRank + 1) AS ORank from (
        SELECT Price FROM sales a
        JOIN damsire b ON a.damsire_Id=b.damsire_ID WHERE PRICE>0 ' . $searchParam . '
        group by Price ORDER BY Price desc) as a,(SELECT @curRank := 0) r) b
        on a.price=b.Rankprice
    LEFT JOIN
    (select price  AS P1,sex AS S1,(@curRank1 := @curRank1 + 1) AS FRank from (
            SELECT price, sex FROM sales a
            JOIN damsire b ON a.damsire_Id=b.damsire_ID WHERE Sex IN ("F","M") AND PRICE>0 ' . $searchParam . '
            group by price,sex ORDER BY price desc) as a,(SELECT @curRank1 := 0) r) c
            on a.price=c.P1 and a.Sex=c.S1
    LEFT JOIN
    (select price  AS P2,sex AS S2,(@curRank2 := @curRank2 + 1) AS CRank from (
            SELECT price, sex FROM sales a
            JOIN damsire b ON a.damsire_Id=b.damsire_ID WHERE Sex IN ("C","H","G") AND PRICE>0 ' . $searchParam . '
            group by price,sex ORDER BY price desc) as a,(SELECT @curRank2 := 0) r) d
            on a.price=d.P2 and a.Sex=d.S2';

    $orderby1 = ' ORDER BY ' . $sort1;
    $orderby2 = ', ' . $sort2;
    $orderby3 = ', ' . $sort3;
    $orderby4 = ', ' . $sort4;
    $orderby5 = ', ' . $sort5;

    if ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "" && $sort5 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4 . $orderby5;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3;
    } elseif ($sort1 != "" && $sort2 != "") {
        $sql = $sql . $orderby1 . $orderby2;
    } elseif ($sort1 != "") {
        $sql = $sql . $orderby1;
    }

    //echo $sql;
    $result = mysqli_query($mysqli, $sql);

    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
        echo $sql;
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function fetchIndividualSaleData_tb($year, $salecode, $type, $elig, $gait, $sort1, $sort2, $sort3, $sort4, $sort5)
{
    global $mysqli;
    //     if ($year == "" && $salecode == "" && $type == "" && $elig == "" && $gait == "") {
    //         return "";
    //     }

    $searchParam = ' AND YEAR(Saledate)= IF("' . $year . '" = "", YEAR(Saledate), "' . $year . '")
                     AND Salecode= IF("' . $salecode . '"  = "", Salecode, "' . $salecode . '")
                     AND Type= IF("' . $type . '"  = "", Type, "' . $type . '")
                     AND Elig= IF("' . $elig . '"  = "", Elig, "' . $elig . '")
                     AND Gait= IF("' . $gait . '"  = "", Gait, "' . $gait . '") ';
    $sql =
        'SELECT ORank,Frank,CRank, HIP, Horse, Sex, Color, `Type`, Datefoal, Elig, Sire, Dam, Salecode, Consno, Saledate, `Day`,
        a.Price, Currency, Purlname, Purfname, Rating FROM (
        SELECT
        HIP,
        Horse,
        Sex,
        Color,
        a.`Type`,
        Datefoal,
        Elig,
        b.Sire,
        b.Dam,
        Salecode,
        Consno,
        Saledate,
        a.`Day`,
        Currency,
        Price,
        Purlname,
        Purfname,
        Rating
        FROM tsales a
        JOIN tdamsire b ON a.damsire_Id=b.damsire_ID
        WHERE PRICE>0 ' . $searchParam . ') a
	LEFT JOIN
    (SELECT Price AS Rankprice ,(@curRank := @curRank + 1) AS ORank from (
		SELECT Price FROM tsales a
		JOIN tdamsire b ON a.damsire_Id=b.damsire_ID WHERE PRICE>0 ' . $searchParam . '
        group by Price ORDER BY Price desc) as a,(SELECT @curRank := 0) r) b
		on a.price=b.Rankprice
    LEFT JOIN
    (select price  AS P1,sex AS S1,(@curRank1 := @curRank1 + 1) AS FRank from (
             SELECT price, sex FROM tsales a
             JOIN tdamsire b ON a.damsire_Id=b.damsire_ID WHERE Sex IN ("F","M") AND PRICE>0 ' . $searchParam . '
             group by price,sex ORDER BY price desc) as a,(SELECT @curRank1 := 0) r) c
             on a.price=c.P1 and a.Sex=c.S1
    LEFT JOIN
    (select price  AS P2,sex AS S2,(@curRank2 := @curRank2 + 1) AS CRank from (
             SELECT price, sex FROM tsales a
             JOIN tdamsire b ON a.damsire_Id=b.damsire_ID WHERE Sex IN ("C","H","G") AND PRICE>0 ' . $searchParam . '
             group by price,sex ORDER BY price desc) as a,(SELECT @curRank2 := 0) r) d
             on a.price=d.P2 and a.Sex=d.S2';

    $orderby1 = ' ORDER BY ' . $sort1;
    $orderby2 = ', ' . $sort2;
    $orderby3 = ', ' . $sort3;
    $orderby4 = ', ' . $sort4;
    $orderby5 = ', ' . $sort5;


    if ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "" && $sort5 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4 . $orderby5;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "" && $sort4 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3 . $orderby4;
    } elseif ($sort1 != "" && $sort2 != "" && $sort3 != "") {
        $sql = $sql . $orderby1 . $orderby2 . $orderby3;
    } elseif ($sort1 != "" && $sort2 != "") {
        $sql = $sql . $orderby1 . $orderby2;
    } elseif ($sort1 != "") {
        $sql = $sql . $orderby1;
    }

    //echo $sql;
    $result = mysqli_query($mysqli, $sql);

    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
        echo $sql;
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function getDamsireID($csire, $cdam)
{
    global $mysqli;
    $sqlDamsireCheck = "SELECT damsire_ID FROM damsire WHERE csire='" . $csire . "' AND cdam='" . $cdam . "'";
    try {
        $result = $mysqli->query($sqlDamsireCheck);

        // Check if the result is valid and has at least one row
        if ($result && $result->num_rows > 0) {
            $damsire_ID = $result->fetch_assoc();
            return $damsire_ID['damsire_ID'];
        } else {
            // Handle the case where no records are found
            return null; // Or return a default value, or handle it as needed
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    // If the query fails, print the error message
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }

    // Return null if no result found or if there was an error
    return null;
}

function getTDamsireID($csire, $cdam)
{
    global $mysqli;
    $sqlDamsireCheck = "SELECT damsire_ID FROM tdamsire WHERE csire='" . $csire . "' AND cdam='" . $cdam . "'";
    try {
        $result = $mysqli->query($sqlDamsireCheck);

        // Check if the result is valid and has at least one row
        if ($result && $result->num_rows > 0) {
            $damsire_ID = $result->fetch_assoc();
            return $damsire_ID['damsire_ID'];
        } else {
            // Handle the case where no records are found
            return null; // Or return a default value, or handle it as needed
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    // If the query fails, print the error message
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }

    // Return null if no result found or if there was an error
    return null;
}

function getLastDamsireID()
{
    global $mysqli;
    $sqlDamsireId = "SELECT max(damsire_ID) as ID FROM damsire";
    $result = $mysqli->query($sqlDamsireId);
    try {
        $damsire_ID = $result->fetch_assoc();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    return $damsire_ID['ID'];
}

function getLastTDamsireID()
{
    global $mysqli;
    $sqlDamsireId = "SELECT max(damsire_ID) as ID FROM tdamsire";
    $result = $mysqli->query($sqlDamsireId);
    try {
        $damsire_ID = $result->fetch_assoc();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    return $damsire_ID['ID'];
}

function checkSalesData($tattoo, $hip, $chorse, $salecode, $saledate)
{
    global $mysqli;

    // Ensure $tattoo is handled when it is null
    if ($tattoo === "") {
        // If tattoo is null, modify the query accordingly
        $sqlDamsireId = "SELECT SALEID FROM sales WHERE HIP='$hip' AND CHORSE='$chorse' AND SALECODE='$salecode' AND SALEDATE='$saledate'";
    } else {
        $sqlDamsireId = "SELECT SALEID FROM sales WHERE TATTOO='$tattoo' AND HIP='$hip' AND CHORSE='$chorse' AND SALECODE='$salecode' AND SALEDATE='$saledate'";
    }

    $result = $mysqli->query($sqlDamsireId);
    try {
        $salesExist = $result->fetch_assoc();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    // Check if the result is not false and has results
    if ($result && $result->num_rows > 0) {
        $salesExist = $result->fetch_assoc();
        // Ensure SALEID is set before accessing
        if (isset($salesExist['SALEID'])) {
            return $salesExist['SALEID'];
        }
    }

    // If we reach here, handle the case where there is no matching sale ID
    return null; // Or handle as needed
}

function checkSalesforUpdate($hip, $salecode, $datefoal)
{
    global $mysqli;
    $sqlDamsireId = "SELECT SALEID FROM sales WHERE HIP='" . $hip . "'
                     AND SALECODE='" . $salecode . "' AND DATEFOAL='" . $datefoal . "'";
    $result = $mysqli->query($sqlDamsireId);
    try {
        $salesExist = $result->fetch_assoc();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    return $salesExist['SALEID'];
}

function checkSalesforETUpdate($tattoo, $salecode, $datefoal)
{
    global $mysqli;
    $sqlDamsireId = "SELECT SALEID FROM sales WHERE TATTOO='" . $tattoo . "'
                     AND SALECODE='" . $salecode . "' AND DATEFOAL='" . $datefoal . "'";
    $result = $mysqli->query($sqlDamsireId);
    try {
        $salesExist = $result->fetch_assoc();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    return $salesExist['SALEID'];
}

function checkTSalesData($tattoo, $hip, $chorse, $salecode, $saledate)
{
    global $mysqli;

    // Ensure $tattoo is handled when it is null
    if ($tattoo === "") {
        // If tattoo is null, modify the query accordingly
        $sqlDamsireId = "SELECT SALEID FROM tsales WHERE HIP='$hip' AND CHORSE='$chorse' AND SALECODE='$salecode' AND SALEDATE='$saledate'";
    } else {
        $sqlDamsireId = "SELECT SALEID FROM tsales WHERE TATTOO='$tattoo' AND HIP='$hip' AND CHORSE='$chorse' AND SALECODE='$salecode' AND SALEDATE='$saledate'";
    }

    $result = $mysqli->query($sqlDamsireId);
    try {
        $salesExist = $result->fetch_assoc();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    // Check if the result is not false and has results
    if ($result && $result->num_rows > 0) {
        $salesExist = $result->fetch_assoc();
        // Ensure SALEID is set before accessing
        if (isset($salesExist['SALEID'])) {
            return $salesExist['SALEID'];
        }
    }

    // If we reach here, handle the case where there is no matching sale ID
    return null; // Or handle as needed
}

function getUserID($user)
{
    global $mysqli;
    $sql = "SELECT user_id as ID FROM users WHERE username = '" . $user . "'";
    $result = $mysqli->query($sql);
    try {
        $user_ID = $result->fetch_assoc();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    return $user_ID['ID'];
}

function fetchUserDetails($user)
{
    global $mysqli;
    $sql = "SELECT * FROM users WHERE username = '" . $user . "'";
    $result = $mysqli->query($sql);
    try {
        $user = $result->fetch_assoc();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    return $user;
}

function getUserData()
{
    global $mysqli;
    $sql = "SELECT USER_ID, USERNAME, FNAME, LNAME, ACTIVE, USERROLE FROM users";
    $result = mysqli_query($mysqli, $sql);
    if (!$result) {
        printf("Errormessage: %s\n", $mysqli->error);
    }
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $json;
}

function unauthorizeUser($userId)
{
    global $mysqli;
    $sql = 'UPDATE users set ACTIVE="N" WHERE USER_ID = ' . $userId;
    //echo $sql;
    if ($mysqli->query($sql) === TRUE) {
        $result = $userId . " - User Unauthorized successfully";
    } else {
        $result = "Error updating record: " . $mysqli->error;
    }
    //echo "aaaaa";
    return $result;
}

function authorizeUser($userId)
{
    global $mysqli;
    $sql = 'UPDATE users set ACTIVE="Y" WHERE USER_ID = ' . $userId;
    //echo $sql;
    if ($mysqli->query($sql) === TRUE) {
        $result = $userId . " - User Authorized successfully";
    } else {
        $result = "Error updating record: " . $mysqli->error;
    }
    //echo "aaaaa";
    return $result;
}

function array_to_csv_download($array, $filename = "export.csv", $delimiter = ";")
{
    //     ob_start();
    //     // open raw memory as file so no temp files needed, you might run out of memory though
    //     $f = fopen('php://memory', 'w');

    //     // loop over the input array
    //     foreach ($array as $line) {
    //         // generate csv lines from the inner arrays
    //         fputcsv($f, $line, $delimiter);
    //     }
    //     // reset the file pointer to the start of the file
    //     fseek($f, 0);
    //     // tell the browser it's going to be a csv file
    //     //header('Content-Type:application/csv');
    //     // tell the browser we want to save it instead of displaying it
    //     //header('Content-Disposition:attachment; filename="'.$filename.'";');
    //     // make php send the generated csv lines to the browser
    //     header('Content-Type: text/csv; charset=utf-8');
    //     header('Content-Disposition: attachment; filename=data.csv');
    //     fpassthru($f);

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sample.csv"');
    $data = array(
        'aaa,bbb,ccc,dddd',
        '123,456,789',
        '"aaa","bbb"'
    );

    $fp = fopen('php://output', 'wb');
    foreach ($data as $line) {
        $val = explode(",", $line);
        fputcsv($fp, $val);
    }
    fclose($fp);
}

function getsaledata($breed)
{
    if ($breed == "") {
        return [];
    }

    $orderBy = isset($_GET['orderby']) ? $_GET['orderby'] : 'Saledate';
    $sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'ASC';

    global $mysqli;

    // Validate orderBy to prevent SQL injection
    $allowed_columns = ['Salecode', 'DAY', 'Saletype', 'Saledate', 'upload_date'];
    if (!in_array($orderBy, $allowed_columns)) {
        $orderBy = 'Saledate';
    }

    // Validate sortOrder
    $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

    // Default query for the 'sales' table - FIXED GROUP BY
    $sql = "
        SELECT s.Salecode, s.DAY as Session, s.Saletype, s.Saledate, d.upload_date, COUNT(*) as count
        FROM sales s
        LEFT JOIN documents d ON s.Salecode = d.file_name
        GROUP BY s.Salecode, s.DAY, s.Saletype, s.Saledate, d.upload_date
        ORDER BY $orderBy $sortOrder
    ";

    // If the breed is 'T', use the 'tsales' table - FIXED GROUP BY
    if ($breed == "T") {
        $sql = "
            SELECT s.Salecode, s.DAY as Session, s.Saletype, s.Saledate, d.upload_date, COUNT(*) as count
            FROM tsales s
            LEFT JOIN documents d ON s.Salecode = d.file_name
            GROUP BY s.Salecode, s.DAY, s.Saletype, s.Saledate, d.upload_date
            ORDER BY $orderBy $sortOrder
        ";
    }

    // Debug: Uncomment these lines to see the actual error
    // echo "SQL: " . $sql . "<br>";
    
    $result = mysqli_query($mysqli, $sql);
    
    if (!$result) {
        // Get the actual MySQL error
        $error = mysqli_error($mysqli);
        error_log("MySQL Error in getsaledata: " . $error);
        error_log("Failed SQL: " . $sql);
        return []; // Return empty array instead of proceeding
    }
    
    $json = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    return $json;
}

function deleteSalecode($breed, $salecode)
{
    global $mysqli;
    echo $breed;
    echo $salecode;
    $sql = 'DELETE FROM sales WHERE Salecode = "' . $salecode . '"';
    if ($breed == "T") {
        $sql = 'DELETE FROM tsales WHERE Salecode = "' . $salecode . '"';
    }
    //echo $sql;
    if ($mysqli->query($sql) === TRUE) {
        $result = "Record deleted successfully";
    } else {
        $result = "Error deleting record: " . $mysqli->error;
    }
    //echo "aaaaa";
    return $result;
}

function fetchHorseList($sort1, $sort2, $sort3, $sort4, $sort5, $horseSearch = '', $damSearch = '', $LocationSearch = '', $FoalSearch = '', $ConsignerSearch = '', $SalecodeSearch = '', $salecodeFilter = '', $farmnameFilter = '', $farmcodeFilter = '')
{
    global $mysqli;

    // Define sortable columns
    $sortableColumns = [
        'horse' => 'Horse',
        'yearfoal' => 'YEARFOAL',
        'sex' => 'Sex',
        'sire' => 'Sire',
        'dam' => 'Dam',
        'farmname' => 'FARMNAME',
        'datefoal' => "DATEFOAL",
        'salecode' => 'SALECODE',
        'hip' => 'HIP',
        'consigner' => 'CONSLNAME' 
    ];

    // Build the SQL query base
    $sql = "
        SELECT DISTINCT
            HIP,
            horse,
            YEARFOAL,
            sex,
            sire,
            dam,
            DATEFOAL,
            type,
            color,
            gait,
            FARMNAME,
            FARMCODE,
            bredto,
            CONSLNAME,
            SALECODE
        FROM sales
    ";

    // Add search conditions if provided
    $conditions = [];
    if (!empty($horseSearch)) {
        $conditions[] = "horse LIKE '%" . $mysqli->real_escape_string($horseSearch) . "%'";
    }

    if (!empty($damSearch)) {
        $conditions[] = "dam LIKE '%" . $mysqli->real_escape_string($damSearch) . "%'";
    }

    if (!empty($LocationSearch)) {
        $conditions[] = "FARMNAME LIKE '%" . $mysqli->real_escape_string($LocationSearch) . "%'";
    }

    if (!empty($FoalSearch)) {
        $conditions[] = "YEARFOAL LIKE '%" . $mysqli->real_escape_string($FoalSearch) . "%'";
    }

    if (!empty($ConsignerSearch)) {
        $conditions[] = "CONSLNAME LIKE '%" . $mysqli->real_escape_string($ConsignerSearch) . "%'";
    }

    if (!empty($SalecodeSearch)) {
        $conditions[] = "SALECODE LIKE '%" . $mysqli->real_escape_string($SalecodeSearch) . "%'";
    }

    if (!empty($salecodeFilter)) {
        $conditions[] = "SALECODE = '" . $mysqli->real_escape_string($salecodeFilter) . "'";
    }

    if (!empty($farmnameFilter)) {
        $conditions[] = "FARMNAME = '" . $mysqli->real_escape_string($farmnameFilter) . "'";
    }

    if (!empty($farmcodeFilter)) {
        $conditions[] = "FARMCODE = '" . $mysqli->real_escape_string($farmcodeFilter) . "'";
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    // Sorting columns from the GET parameters
    $orderConditions = [];
    $sortParams = ['sort1', 'sort2', 'sort3', 'sort4', 'sort5'];
    $sortIndex = 1;

    foreach ($sortParams as $sortParam) {
        if (!empty($$sortParam)) {
            // Check if sort order is specified for each sort column
            $sortOrder = isset($_GET["{$sortParam}_order"]) ? $_GET["{$sortParam}_order"] : 'ASC';

            // Check if this sort column exists in the sortable columns
            $column = strtolower($$sortParam);
            if (isset($sortableColumns[$column])) {
                $orderConditions[] = $sortableColumns[$column] . ' ' . $sortOrder;
            }
        }
        $sortIndex++;
    }

    // If sorting conditions exist, append ORDER BY
    if (!empty($orderConditions)) {
        $sql .= ' ORDER BY ' . implode(', ', $orderConditions);
    }

    $sql .= ' LIMIT 500';

    // Prepare, bind, and execute the query
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->execute();
        $result = $stmt->get_result();
        $json = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $json;
    } else {
        error_log("MySQL Error: " . $mysqli->error . " | SQL: " . $sql);
        return [];
    }
}

function updateHorseDetails($horseId, $data)
{
    global $mysqli;

    // Debug: Log incoming data
    error_log("updateHorseDetails called with horseId: $horseId and data: " . print_r($data, true));

    // Validate database connection
    if (!$mysqli || !$mysqli->ping()) {
        error_log("Database connection failed");
        return ['success' => false, 'error' => 'Database connection failed'];
    }

    // Validate inputs
    if (empty($horseId) || empty($data)) {
        return ['success' => false, 'error' => 'Invalid input'];
    }

    // Define all editable fields with their configurations
    // This includes fields from both sales and horse_inspection tables
    $editableFields = [
        // Sales table fields
        'YEARFOAL' => ['type' => 'i', 'allow_null' => true, 'table' => 'sales'],
        'SEX' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        'Sire' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        'DAM' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        'DATEFOAL' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        'COLOR' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        'GAIT' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        'TYPE' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        'BREDTO' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        'FARMNAME' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        'HORSE' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        'Sireofdam' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        'TATTOO' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        'PRICE' => ['type' => 'd', 'allow_null' => true, 'table' => 'sales'],
        'SALECODE' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        'BREED' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        'LASTBRED' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        'SALEYEAR' => ['type' => 'i', 'allow_null' => true, 'table' => 'sales'],
        'salesection' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        'salestall' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        'PEMCODE' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        'FARMCODE' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        'PURFNAME' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        'PURLNAME' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        'SALEDATE' => ['type' => 's', 'allow_null' => true, 'table' => 'sales'],
        
        // Horse Inspection table fields
        'inspector_name' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection'],
        'inspection_date' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection'],
        'req_day' => ['type' => 'i', 'allow_null' => true, 'table' => 'horse_inspection'],
        'dr_psd' => ['type' => 'i', 'allow_null' => true, 'table' => 'horse_inspection'],
        'day_rating_indicator' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection'],
        'arr_num' => ['type' => 'i', 'allow_null' => true, 'table' => 'horse_inspection'],
        'farm_barn_id' => ['type' => 'i', 'allow_null' => true, 'table' => 'horse_inspection'],
        'farm_id' => ['type' => 'i', 'allow_null' => true, 'table' => 'horse_inspection'],
        'pedigree' => ['type' => 'd', 'allow_null' => true, 'table' => 'horse_inspection'],
        'consignor' => ['type' => 'd', 'allow_null' => true, 'table' => 'horse_inspection'],
        'biomechanics' => ['type' => 'd', 'allow_null' => true, 'table' => 'horse_inspection'],
        'in_foal_to' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection'],
        'brd_status' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection'],
        'rating2_sb' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection'],
        'rating6_le' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection'],
        'rating7_guest' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection'],
        'purchasers_comments_post_sale' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection'],
        'broker_comments' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection'],
        'initial_trainer_first_name' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection'],
        'initial_trainer_last_name' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection'],
        'initial_trainer_location' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection'],
        'trainer_comments_as_two_year_old' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection']
    ];

    // Separate updates by table
    $salesUpdates = [];
    $inspectionUpdates = [];
    $salesTypes = '';
    $inspectionTypes = '';
    $salesValues = [];
    $inspectionValues = [];
    $updatedFields = [];

    foreach ($editableFields as $field => $config) {
        // Check if field exists in data (case-insensitive)
        $dataKey = array_key_exists_case_insensitive($field, $data);

        if ($dataKey !== false) {
            $value = $data[$dataKey];

            // Handle NULL/empty values
            if ($value === null || $value === '') {
                if ($config['allow_null']) {
                    $value = null;
                } else {
                    continue; // Skip this field if empty values not allowed
                }
            }

            // Special handling for YEARFOAL
            if ($field === 'YEARFOAL' && $value !== null && !is_numeric($value)) {
                error_log("Invalid YEARFOAL value: $value");
                return ['success' => false, 'error' => 'YEARFOAL must be a number'];
            }

            // Special handling for PRICE (double)
            if ($field === 'PRICE' && $value !== null) {
                $value = floatval($value);
            }

            // Add to appropriate table updates
            if ($config['table'] === 'sales') {
                $salesUpdates[] = "$field = ?";
                $salesTypes .= $config['type'];
                $salesValues[] = $value;
            } else {
                $inspectionUpdates[] = "$field = ?";
                $inspectionTypes .= $config['type'];
                $inspectionValues[] = $value;
            }
            
            $updatedFields[$field] = $value;
            error_log("Preparing to update $field with value: " . var_export($value, true));
        }
    }

    $success = true;
    $affectedRows = 0;
    $messages = [];

    // Update sales table
    if (!empty($salesUpdates)) {
        try {
            // Add horseId to values
            $salesValues[] = $horseId;
            $salesTypes .= 's';

            $sql = "UPDATE sales SET " . implode(', ', $salesUpdates) . " WHERE HORSE = ?";
            error_log("Executing SQL (sales): $sql with types: $salesTypes and values: " . print_r($salesValues, true));

            $stmt = $mysqli->prepare($sql);

            if (!$stmt) {
                throw new Exception('Failed to prepare sales statement: ' . $mysqli->error);
            }

            // Special handling for binding parameters with NULL values
            $refValues = [];
            foreach ($salesValues as $key => $value) {
                $refValues[$key] = &$salesValues[$key];
            }

            $stmt->bind_param($salesTypes, ...$refValues);

            if (!$stmt->execute()) {
                throw new Exception('Failed to update sales: ' . $stmt->error);
            }

            $affectedRows += $stmt->affected_rows;
            $stmt->close();
            $messages[] = 'Sales table updated successfully';
        } catch (Exception $e) {
            error_log("Sales update failed: " . $e->getMessage());
            $success = false;
            $messages[] = 'Sales update error: ' . $e->getMessage();
        }
    }

    // Update horse_inspection table
    if (!empty($inspectionUpdates)) {
        try {
            // Check if record exists first
            $checkSql = "SELECT id FROM horse_inspection WHERE horse = ?";
            $checkStmt = $mysqli->prepare($checkSql);
            $checkStmt->bind_param('s', $horseId);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $recordExists = $checkResult->num_rows > 0;
            $checkStmt->close();

            if ($recordExists) {
                // Update existing record
                $inspectionValues[] = $horseId;
                $inspectionTypes .= 's';
                
                $sql = "UPDATE horse_inspection SET " . implode(', ', $inspectionUpdates) . " WHERE horse = ?";
                error_log("Executing SQL (inspection - UPDATE): $sql");
                
                $stmt = $mysqli->prepare($sql);
                if (!$stmt) {
                    throw new Exception('Failed to prepare inspection update statement: ' . $mysqli->error);
                }
                
                $refValues = [];
                foreach ($inspectionValues as $key => $value) {
                    $refValues[$key] = &$inspectionValues[$key];
                }
                
                $stmt->bind_param($inspectionTypes, ...$refValues);
                
                if (!$stmt->execute()) {
                    throw new Exception('Failed to update inspection: ' . $stmt->error);
                }
                
                $affectedRows += $stmt->affected_rows;
                $stmt->close();
                $messages[] = 'Inspection table updated successfully';
            } else {
                // Insert new record
                $inspectionFields = implode(', ', array_keys($inspectionUpdates));
                $inspectionPlaceholders = implode(', ', array_fill(0, count($inspectionUpdates), '?'));
                $inspectionValues[] = $horseId; // Add horse name at the end
                $inspectionTypes .= 's';
                
                $sql = "INSERT INTO horse_inspection (horse, " . $inspectionFields . ") VALUES (?, " . $inspectionPlaceholders . ")";
                error_log("Executing SQL (inspection - INSERT): $sql");
                
                $stmt = $mysqli->prepare($sql);
                if (!$stmt) {
                    throw new Exception('Failed to prepare inspection insert statement: ' . $mysqli->error);
                }
                
                // Reorder values: horse name first, then the field values
                $insertValues = array_merge([$horseId], $inspectionValues);
                $insertTypes = 's' . $inspectionTypes;
                
                $refValues = [];
                foreach ($insertValues as $key => $value) {
                    $refValues[$key] = &$insertValues[$key];
                }
                
                $stmt->bind_param($insertTypes, ...$refValues);
                
                if (!$stmt->execute()) {
                    throw new Exception('Failed to insert inspection: ' . $stmt->error);
                }
                
                $affectedRows += $stmt->affected_rows;
                $stmt->close();
                $messages[] = 'Inspection record created successfully';
            }
        } catch (Exception $e) {
            error_log("Inspection update failed: " . $e->getMessage());
            $success = false;
            $messages[] = 'Inspection update error: ' . $e->getMessage();
        }
    }

    // Return the result
    return [
        'success' => $success,
        'affected_rows' => $affectedRows,
        'updatedData' => $updatedFields,
        'message' => $success 
            ? ($affectedRows > 0 
                ? 'Horse details updated successfully' 
                : 'No changes made (data may be identical)')
            : implode('; ', $messages)
    ];
}

function fetchHorseListTb($sort1, $sort2, $sort3, $sort4, $sort5, $horseSearch = '', $damSearch = '', $LocationSearch = '', $FoalSearch = '', $ConsignerSearch = '', $SalecodeSearch = '', $salecodeFilter = '', $farmnameFilter = '', $farmcodeFilter = '')
{
    global $mysqli;

    // Define sortable columns
    $sortableColumns = [
        'horse' => 'Horse',
        'yearfoal' => 'YEARFOAL',
        'sex' => 'SEX',
        'sire' => 'tSire',
        'dam' => 'TDAM',
        'farmname' => 'FARMNAME',
        'datefoal' => "DATEFOAL",
        'salecode' => 'SALECODE',
        'hip' => 'HIP',
        'consigner' => 'CONSLNAME' 
    ];

    // Build the SQL query base
    $sql = "
        SELECT DISTINCT
            HIP,
            horse,
            YEARFOAL,
            SEX,
            tSire,
            TDAM,
            DATEFOAL,
            TYPE as type,
            COLOR as color,
            GAIT as gait,
            FARMNAME,
            FARMCODE,
            BREDTO as bredto,
            CONSLNAME,
            SALECODE
        FROM tsales
    ";

    // Add search conditions if provided
    $conditions = [];
    if (!empty($horseSearch)) {
        $conditions[] = "horse LIKE '%" . $mysqli->real_escape_string($horseSearch) . "%'";
    }

    if (!empty($damSearch)) {
        $conditions[] = "TDAM LIKE '%" . $mysqli->real_escape_string($damSearch) . "%'";
    }

    if (!empty($LocationSearch)) {
        $conditions[] = "FARMNAME LIKE '%" . $mysqli->real_escape_string($LocationSearch) . "%'";
    }

    if (!empty($FoalSearch)) {
        $conditions[] = "YEARFOAL LIKE '%" . $mysqli->real_escape_string($FoalSearch) . "%'";
    }

    if (!empty($ConsignerSearch)) {
        $conditions[] = "CONSLNAME LIKE '%" . $mysqli->real_escape_string($ConsignerSearch) . "%'";
    }

    if (!empty($SalecodeSearch)) {
        $conditions[] = "SALECODE LIKE '%" . $mysqli->real_escape_string($SalecodeSearch) . "%'";
    }

    if (!empty($salecodeFilter)) {
        $conditions[] = "SALECODE = '" . $mysqli->real_escape_string($salecodeFilter) . "'";
    }

    if (!empty($farmnameFilter)) {
        $conditions[] = "FARMNAME = '" . $mysqli->real_escape_string($farmnameFilter) . "'";
    }

    if (!empty($farmcodeFilter)) {
        $conditions[] = "FARMCODE = '" . $mysqli->real_escape_string($farmcodeFilter) . "'";
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    // Sorting columns from the GET parameters
    $orderConditions = [];
    $sortParams = ['sort1', 'sort2', 'sort3', 'sort4', 'sort5'];
    $sortIndex = 1;

    foreach ($sortParams as $sortParam) {
        if (!empty($$sortParam)) {
            // Check if sort order is specified for each sort column
            $sortOrder = isset($_GET["{$sortParam}_order"]) ? $_GET["{$sortParam}_order"] : 'ASC';

            // Check if this sort column exists in the sortable columns
            $column = strtolower($$sortParam);
            if (isset($sortableColumns[$column])) {
                $orderConditions[] = $sortableColumns[$column] . ' ' . $sortOrder;
            }
        }
        $sortIndex++;
    }

    // If sorting conditions exist, append ORDER BY
    if (!empty($orderConditions)) {
        $sql .= ' ORDER BY ' . implode(', ', $orderConditions);
    }

    $sql .= ' LIMIT 500';

    // Prepare, bind, and execute the query
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->execute();
        $result = $stmt->get_result();
        $json = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $json;
    } else {
        error_log("MySQL Error: " . $mysqli->error . " | SQL: " . $sql);
        return [];
    }
}

function updateHorseDetailsTb($horseId, $data)
{
    global $mysqli;

    // Debug: Log incoming data
    error_log("updateHorseDetailsTb called with horseId: $horseId and data: " . print_r($data, true));

    // Validate database connection
    if (!$mysqli || !$mysqli->ping()) {
        error_log("Database connection failed");
        return ['success' => false, 'error' => 'Database connection failed'];
    }

    // Validate inputs
    if (empty($horseId) || empty($data)) {
        return ['success' => false, 'error' => 'Invalid input'];
    }

    // Define all editable fields with their configurations
    // This includes fields from both tsales and horse_inspection_tb tables
    $editableFields = [
        // tsales table fields
        'YEARFOAL' => ['type' => 'i', 'allow_null' => true, 'table' => 'tsales'],
        'SEX' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        'Sire' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        'DAM' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        'DATEFOAL' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        'COLOR' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        'GAIT' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        'TYPE' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        'BREDTO' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        'FARMNAME' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        'HORSE' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        'Sireofdam' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        'TATTOO' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        'PRICE' => ['type' => 'd', 'allow_null' => true, 'table' => 'tsales'],
        'SALECODE' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        'BREED' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        'LASTBRED' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        'SALEYEAR' => ['type' => 'i', 'allow_null' => true, 'table' => 'tsales'],
        'salesection' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        'salestall' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        'PEMCODE' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        'FARMCODE' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        'PURFNAME' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        'PURLNAME' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        'SALEDATE' => ['type' => 's', 'allow_null' => true, 'table' => 'tsales'],
        
        // Horse Inspection table fields (corrected table name)
        'inspector_name' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection_tb'],
        'inspection_date' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection_tb'],
        'req_day' => ['type' => 'i', 'allow_null' => true, 'table' => 'horse_inspection_tb'],
        'dr_psd' => ['type' => 'i', 'allow_null' => true, 'table' => 'horse_inspection_tb'],
        'day_rating_indicator' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection_tb'],
        'arr_num' => ['type' => 'i', 'allow_null' => true, 'table' => 'horse_inspection_tb'],
        'farm_barn_id' => ['type' => 'i', 'allow_null' => true, 'table' => 'horse_inspection_tb'],
        'farm_id' => ['type' => 'i', 'allow_null' => true, 'table' => 'horse_inspection_tb'],
        'pedigree' => ['type' => 'd', 'allow_null' => true, 'table' => 'horse_inspection_tb'],
        'consignor' => ['type' => 'd', 'allow_null' => true, 'table' => 'horse_inspection_tb'],
        'biomechanics' => ['type' => 'd', 'allow_null' => true, 'table' => 'horse_inspection_tb'],
        'in_foal_to' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection_tb'],
        'brd_status' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection_tb'],
        'rating2_sb' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection_tb'],
        'rating6_le' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection_tb'],
        'rating7_guest' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection_tb'],
        'purchasers_comments_post_sale' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection_tb'],
        'broker_comments' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection_tb'],
        'initial_trainer_first_name' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection_tb'],
        'initial_trainer_last_name' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection_tb'],
        'initial_trainer_location' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection_tb'],
        'trainer_comments_as_two_year_old' => ['type' => 's', 'allow_null' => true, 'table' => 'horse_inspection_tb']
    ];

    // Separate updates by table
    $salesUpdates = [];
    $inspectionUpdates = [];
    $salesTypes = '';
    $inspectionTypes = '';
    $salesValues = [];
    $inspectionValues = [];
    $updatedFields = [];

    foreach ($editableFields as $field => $config) {
        // Check if field exists in data (case-insensitive)
        $dataKey = array_key_exists_case_insensitive($field, $data);

        if ($dataKey !== false) {
            $value = $data[$dataKey];

            // Handle NULL/empty values
            if ($value === null || $value === '') {
                if ($config['allow_null']) {
                    $value = null;
                } else {
                    continue; // Skip this field if empty values not allowed
                }
            }

            // Special handling for YEARFOAL
            if ($field === 'YEARFOAL' && $value !== null && !is_numeric($value)) {
                error_log("Invalid YEARFOAL value: $value");
                return ['success' => false, 'error' => 'YEARFOAL must be a number'];
            }

            // Special handling for PRICE (double)
            if ($field === 'PRICE' && $value !== null) {
                // Remove currency formatting
                $value = preg_replace('/[^0-9.-]/', '', $value);
                $value = floatval($value);
            }

            // Add to appropriate table updates
            if ($config['table'] === 'tsales') {
                $salesUpdates[] = "$field = ?";
                $salesTypes .= $config['type'];
                $salesValues[] = $value;
            } else {
                $inspectionUpdates[] = "$field = ?";
                $inspectionTypes .= $config['type'];
                $inspectionValues[] = $value;
            }
            
            $updatedFields[$field] = $value;
            error_log("Preparing to update $field with value: " . var_export($value, true));
        }
    }

    $success = true;
    $affectedRows = 0;
    $messages = [];

    // Update tsales table
    if (!empty($salesUpdates)) {
        try {
            // Add horseId to values
            $salesValues[] = $horseId;
            $salesTypes .= 's';

            // Use HORSE (uppercase) as the identifier field in tsales
            $sql = "UPDATE tsales SET " . implode(', ', $salesUpdates) . " WHERE HORSE = ?";
            error_log("Executing SQL (tsales): $sql with types: $salesTypes and values: " . print_r($salesValues, true));

            $stmt = $mysqli->prepare($sql);

            if (!$stmt) {
                throw new Exception('Failed to prepare sales statement: ' . $mysqli->error);
            }

            // Special handling for binding parameters with NULL values
            $refValues = [];
            foreach ($salesValues as $key => $value) {
                $refValues[$key] = &$salesValues[$key];
            }

            $stmt->bind_param($salesTypes, ...$refValues);

            if (!$stmt->execute()) {
                throw new Exception('Failed to update tsales: ' . $stmt->error);
            }

            $affectedRows += $stmt->affected_rows;
            $stmt->close();
            $messages[] = 'tSales table updated successfully';
        } catch (Exception $e) {
            error_log("tSales update failed: " . $e->getMessage());
            $success = false;
            $messages[] = 'tSales update error: ' . $e->getMessage();
        }
    }

    // Update horse_inspection_tb table (corrected table name)
    if (!empty($inspectionUpdates)) {
        try {
            // Check if record exists first - using correct table name
            $checkSql = "SELECT id FROM horse_inspection_tb WHERE horse = ?";
            $checkStmt = $mysqli->prepare($checkSql);
            $checkStmt->bind_param('s', $horseId);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $recordExists = $checkResult->num_rows > 0;
            $checkStmt->close();

            if ($recordExists) {
                // Update existing record
                $inspectionValues[] = $horseId;
                $inspectionTypes .= 's';
                
                $sql = "UPDATE horse_inspection_tb SET " . implode(', ', $inspectionUpdates) . " WHERE horse = ?";
                error_log("Executing SQL (inspection_tb - UPDATE): $sql with values: " . print_r($inspectionValues, true));
                
                $stmt = $mysqli->prepare($sql);
                if (!$stmt) {
                    throw new Exception('Failed to prepare inspection update statement: ' . $mysqli->error);
                }
                
                $refValues = [];
                foreach ($inspectionValues as $key => $value) {
                    $refValues[$key] = &$inspectionValues[$key];
                }
                
                $stmt->bind_param($inspectionTypes, ...$refValues);
                
                if (!$stmt->execute()) {
                    throw new Exception('Failed to update inspection_tb: ' . $stmt->error);
                }
                
                $affectedRows += $stmt->affected_rows;
                $stmt->close();
                $messages[] = 'Inspection table updated successfully';
            } else {
                // Insert new record - note: horse is the first field in the table
                $inspectionFields = implode(', ', array_keys($inspectionUpdates));
                $inspectionPlaceholders = implode(', ', array_fill(0, count($inspectionUpdates), '?'));
                
                // For INSERT, we need to include the horse field
                $sql = "INSERT INTO horse_inspection_tb (horse, " . $inspectionFields . ") VALUES (?, " . $inspectionPlaceholders . ")";
                error_log("Executing SQL (inspection_tb - INSERT): $sql");
                
                $stmt = $mysqli->prepare($sql);
                if (!$stmt) {
                    throw new Exception('Failed to prepare inspection insert statement: ' . $mysqli->error);
                }
                
                // Reorder values: horse name first, then the field values
                $insertValues = array_merge([$horseId], $inspectionValues);
                $insertTypes = 's' . $inspectionTypes;
                
                $refValues = [];
                foreach ($insertValues as $key => $value) {
                    $refValues[$key] = &$insertValues[$key];
                }
                
                $stmt->bind_param($insertTypes, ...$refValues);
                
                if (!$stmt->execute()) {
                    throw new Exception('Failed to insert inspection_tb: ' . $stmt->error);
                }
                
                $affectedRows += $stmt->affected_rows;
                $stmt->close();
                $messages[] = 'Inspection record created successfully';
            }
        } catch (Exception $e) {
            error_log("Inspection update failed: " . $e->getMessage());
            $success = false;
            $messages[] = 'Inspection update error: ' . $e->getMessage();
        }
    }

    // Return the result
    return [
        'success' => $success,
        'affected_rows' => $affectedRows,
        'updatedData' => $updatedFields,
        'message' => $success 
            ? ($affectedRows > 0 
                ? 'Horse details updated successfully' 
                : 'No changes made (data may be identical)')
            : implode('; ', $messages)
    ];
}

// Helper function for case-insensitive array key search
function array_key_exists_case_insensitive($key, $array) {
    $lowerKey = strtolower($key);
    foreach ($array as $k => $v) {
        if (strtolower($k) === $lowerKey) {
            return $k;
        }
    }
    return false;
}
function sanitizeHorseIdForImage($horseId)
{
    // Remove any non-alphanumeric characters, spaces, and special characters
    return preg_replace('/[^a-zA-Z0-9_-]/', '', $horseId);
}

function getS3Client() {
    static $s3Client = null;
    
    if ($s3Client === null) {
        $s3Client = new S3Client([
            'region' => 'us-east-1',
            'version' => 'latest',
            'suppress_php_deprecation_warning' => true
        ]);
    }
    
    return $s3Client;
}

function getHorseDetails($horseId)
{
    global $mysqli;

    if (!$mysqli) {
        return ['error' => 'Invalid or lost database connection'];
    }

    // Validate and sanitize horse ID
    if (!preg_match('/^[a-zA-Z0-9 _\-\(\)]+$/', $horseId)) {
        return ['error' => 'Invalid horse ID format'];
    }

    // Fetch horse details from tsales
    $stmt = $mysqli->prepare("SELECT * FROM sales WHERE HORSE = ? LIMIT 1");
    $stmt->bind_param("s", $horseId);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result || $result->num_rows === 0) {
        return ['error' => 'Horse not found'];
    }

    $horse = $result->fetch_assoc();
    $stmt->close();

    // Fetch inspection data from horse_inspection_tb
    $inspectionStmt = $mysqli->prepare("SELECT * FROM horse_inspection WHERE horse = ? LIMIT 1");
    $inspectionStmt->bind_param("s", $horseId);
    $inspectionStmt->execute();
    $inspectionResult = $inspectionStmt->get_result();
    
    if ($inspectionResult && $inspectionResult->num_rows > 0) {
        $inspectionData = $inspectionResult->fetch_assoc();
        // Merge inspection data with horse data (overwrites any duplicate fields from tsales if needed)
        $horse = array_merge($horse, $inspectionData);
    }
    $inspectionStmt->close();

    try {
        // Setup Secrets Manager
        $region = 'us-east-1'; // Change the region if needed
        $bucket = 'horse-list-photos-and-details'; // bucket name
        // $roleArn = 'arn:aws:iam::211125609145:role/python-website-logs'; // Role to assume
        // $sessionName = 'GetHorseDetailsSession';

        $s3 = getS3Client();

        // Sanitize the horseId specifically for images (remove spaces and special characters)
        $sanitizedHorseId = sanitizeHorseIdForImage($horseId);

        // Fetch all image keys for the horse from the DB
        $stmt2 = $mysqli->prepare("SELECT image_url FROM horse_images WHERE horse_id = ?");
        $stmt2->bind_param("s", $sanitizedHorseId);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        $images = [];

        // Log the results of the image query to check if data is being retrieved correctly
        if ($result2 && $result2->num_rows > 0) {
            while ($row = $result2->fetch_assoc()) {
                $objectKey = $row['image_url'];

                // Generate presigned URL for each image (valid for 5 minutes)
                $cmd = $s3->getCommand('GetObject', [
                    'Bucket' => $bucket,
                    'Key'    => $objectKey,
                ]);

                // Generate the presigned URL valid for 5 minutes
                $request = $s3->createPresignedRequest($cmd, '+5 minutes');

                // Add the presigned URL to the images array
                $images[] = (string) $request->getUri();
            }
        } else {
            // If no images found for this horse, log a message
            error_log("No images found for horse: $horseId");
        }

        $stmt2->close();

        // Attach the images array to the horse data
        $horse['images'] = $images;

        // Log the images array to verify the result
        error_log("Images for horse $horseId: " . json_encode($images));

        return $horse;
    } catch (AwsException $e) {
        error_log("AWS Error: " . $e->getMessage());
        return ['error' => 'Failed to load images. Try again later.'];
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        return ['error' => 'Unexpected error retrieving horse details'];
    }
}

function getHorseDetailsTb($horseId)
{
    global $mysqli;

    if (!$mysqli) {
        return ['error' => 'Invalid or lost database connection'];
    }

    // Validate and sanitize horse ID
    if (!preg_match('/^[a-zA-Z0-9 _\-\(\)]+$/', $horseId)) {
        return ['error' => 'Invalid horse ID format'];
    }

    // Fetch horse details from tsales
    $stmt = $mysqli->prepare("SELECT * FROM tsales WHERE HORSE = ? LIMIT 1");
    $stmt->bind_param("s", $horseId);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result || $result->num_rows === 0) {
        return ['error' => 'Horse not found'];
    }

    $horse = $result->fetch_assoc();
    $stmt->close();

    // Fetch inspection data from horse_inspection_tb
    $inspectionStmt = $mysqli->prepare("SELECT * FROM horse_inspection_tb WHERE horse = ? LIMIT 1");
    $inspectionStmt->bind_param("s", $horseId);
    $inspectionStmt->execute();
    $inspectionResult = $inspectionStmt->get_result();
    
    if ($inspectionResult && $inspectionResult->num_rows > 0) {
        $inspectionData = $inspectionResult->fetch_assoc();
        // Merge inspection data with horse data (overwrites any duplicate fields from tsales if needed)
        $horse = array_merge($horse, $inspectionData);
    }
    $inspectionStmt->close();

    try {
        // Setup Secrets Manager
        $region = 'us-east-1'; // Change the region if needed
        $bucket = 'horse-list-photos-and-details-tb'; // bucket name
        // $roleArn = 'arn:aws:iam::211125609145:role/python-website-logs'; // Role to assume
        // $sessionName = 'GetHorseDetailsSession';

        $s3 = getS3Client();

        // Sanitize the horseId specifically for images (remove spaces and special characters)
        $sanitizedHorseId = sanitizeHorseIdForImage($horseId);

        // Fetch all image keys for the horse from the DB
        $stmt2 = $mysqli->prepare("SELECT image_url FROM horse_images_tb WHERE horse_id = ?");
        $stmt2->bind_param("s", $sanitizedHorseId);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        $images = [];

        // Log the results of the image query to check if data is being retrieved correctly
        if ($result2 && $result2->num_rows > 0) {
            while ($row = $result2->fetch_assoc()) {
                $objectKey = $row['image_url'];

                // Generate presigned URL for each image (valid for 5 minutes)
                $cmd = $s3->getCommand('GetObject', [
                    'Bucket' => $bucket,
                    'Key'    => $objectKey,
                ]);

                // Generate the presigned URL valid for 5 minutes
                $request = $s3->createPresignedRequest($cmd, '+5 minutes');

                // Add the presigned URL to the images array
                $images[] = (string) $request->getUri();
            }
        } else {
            // If no images found for this horse, log a message
            error_log("No images found for horse: $horseId");
        }

        $stmt2->close();

        // Attach the images array to the horse data
        $horse['images'] = $images;

        // Log the images array to verify the result
        error_log("Images for horse $horseId: " . json_encode($images));

        return $horse;
    } catch (AwsException $e) {
        error_log("AWS Error: " . $e->getMessage());
        return ['error' => 'Failed to load images. Try again later.'];
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        return ['error' => 'Unexpected error retrieving horse details'];
    }
}