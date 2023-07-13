<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
?>

<!DOCTYPE html>
<html>

<head>
    <title>Project Data Accordion</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">


    <style>
        table thead {
            border: none !important;
        }

        table thead th {
            border-right: 1px solid #ecf0f1 !important;
            padding: 6px !important;
            text-align: center !important;
            background-color: #154360 !important;
            color: #fff !important;
        }

        table thead th:last-child {
            border-right: none !important;
        }

        table tbody tr td {
            border: 1px solid #ecf0f1 !important;
            padding: 6px !important;
        }

        table tbody tr td:first-child {
            border-left: 2px solid red;
        }

        .custom_styling {
            border: 1px solid #154360 !important;
            border-left: 2px solid #154360 !important;
            border-top: none !important;
        }
    </style>
</head>

<body>
    <div class="container-fluid mt-4">
        <h3 class="text-center" style="font-weight:500">Upload Excel File</h3>
        <!-- import.php -->
        <form method="POST" action="#" enctype="multipart/form-data">
            <div class="form-row">
                <!-- <div class="form-group  col-md-4">
                    <label></label>
                    <input type="file" name="excel_file" class="form-control">
                </div> -->
                <div class="col"></div>
                <div class="custom-file mb-3 col-md-6  text-center">
                    <div class="invalid-feedback">Please upload excel file to caluculate the total hours. </div>
                    <input type="file" name="excel_file" class="custom-file-input form-control-sm" id="validatedCustomFile" required>
                    <label class="custom-file-label" for="validatedCustomFile">Choose file...</label>
                </div>
                <div class="col"></div>
            </div>
            <div class="form-group text-center">
                <button type="submit" name="Submit" style="width: 300px;" class="btn btn-outline-primary btn-sm">Submit</button>
            </div>
        </form>
        <?php
        // Check if a file is uploaded

        // Check if a file is uploaded
        if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] === UPLOAD_ERR_OK) {
            $inputFileName = $_FILES['excel_file']['tmp_name'];

            try {
                // Load the Excel file
                $spreadsheet = IOFactory::load($inputFileName);
                $sheet = $spreadsheet->getActiveSheet();

                // Get the highest row index
                $highestRow = $sheet->getHighestRow();

                // Create an empty array to store the project data
                $data = [];

                // Iterate through each row of the worksheet
                for ($row = 2; $row <= $highestRow; $row++) { // Start from row 2 to skip headers
                    $projectName = $sheet->getCell('K' . $row)->getValue(); // Column K for project name
                    $teamName = $sheet->getCell('J' . $row)->getValue(); // Column J for team name
                    $userName = $sheet->getCell('B' . $row)->getValue(); // Column B for user name
                    $hours = $sheet->getCell('H' . $row)->getValue(); // Column H for hours
                    $date = $sheet->getCell('C' . $row)->getValue(); // Column C for date

                    $NewDate = date('Y-m-d', strtotime('+' . ($date - 2) . ' days', strtotime("1900-01-01")));

                    // Add the hours to the corresponding nested array structure
                    if (!isset($data[$projectName])) {
                        $data[$projectName] = [];
                    }

                    if (!isset($data[$projectName][$teamName])) {
                        $data[$projectName][$teamName] = [];
                    }

                    if (!isset($data[$projectName][$teamName][$userName])) {
                        $data[$projectName][$teamName][$userName] = [];
                    }

                    if (!isset($data[$projectName][$teamName][$userName][$NewDate])) {
                        $data[$projectName][$teamName][$userName][$NewDate] = 0;
                    }

                    $data[$projectName][$teamName][$userName][$NewDate] += $hours;
                }

                // Create an array with the days of the month


                $monthStartDate = date("01", strtotime($NewDate));
                $monthEndDate = date("t", strtotime($NewDate));

                $daysOfMonth = range($monthStartDate, $monthEndDate);

                // Display the project data in a table


                echo '<div class="custom_styling">';

                echo '<table class="table-sm w-100">';
                echo '<thead><tr><th>Project Name</th>';

                // Create the table headers for each day of the month
                foreach ($daysOfMonth as $day) {
                    echo '<th>' . $day . '</th>';
                }
                echo '<th>Total</th>';
                echo '</tr></thead>';
                echo '<tbody>';

                // Iterate through the project data
                foreach ($data as $projectName => $teams) {
                    echo '<tr>';
                    echo '<td style="background-color:rgb( 171, 235, 198 )" colspan="' . (count($daysOfMonth) + 2) . '"><b>' . $projectName . '<b></td>';
                    // echo '<td style="background-color:rgb( 240, 240, 240 ); border:none;"></td>';
                    echo '</tr>';

                    // Iterate through each team
                    foreach ($teams as $teamName => $users) {
                        echo '<tr>';
                        echo '<td style="background-color:rgb( 174, 214, 241 )"  colspan="' . (count($daysOfMonth) + 2) . '"><span style="margin-left: 20px;"><b>' . $teamName . '<b><span></td>';
                        // echo '<td style="background-color:rgb( 240, 240, 240 ); border:none;"></td>';
                        echo '</tr>';
                        $total_hours = 0;
                        // Iterate through each user
                        foreach ($users as $userName => $dates) {
                            echo '<tr>';
                            echo '<td><span style="margin-left: 32px;">' . $userName . '</sapn></td>';

                            // Display the hours for each day
                            foreach ($daysOfMonth as $day) {

                                $month = date('m', strtotime($NewDate));

                                $date = date('Y-m-d', mktime(0, 0, 0, $month, $day, date('Y')));
                                $hours = isset($dates[$date]) ? $dates[$date] : 0;
                                if ($hours > 6 &&  $hours <= 8) {
                                    $background = "rgb(241, 196, 15)";
                                } else if ($hours > 8) {
                                    $background = "rgb(255, 0, 0)";
                                } else {
                                    $background = "rgb(0, 255, 0)";
                                }
                                echo '<td  style="background-color:' . $background . '">' . $hours . '</td>';
                                $total_hours += $hours;
                            }
                            if ($total_hours > 100 &&  $total_hours <= 160) {
                                $total_background = "rgb(255, 255, 255)";
                            } else if ($total_hours > 160) {
                                $total_background = "rgb(255, 255, 255)";
                            } else {
                                $total_background = "rgb(255, 255, 255)";
                            }
                            echo '<td style="background-color:' . $total_background . '; font-weight:600;">' . $total_hours . '</td>';
                            echo '</tr>';
                        }
                    }
                }

                echo '</tbody>';
                echo '</table>';
                echo '</div>';
            } catch (Exception $e) {
                echo 'Error loading file: ', $e->getMessage();
            }
        }
        ?>
    </div>

    <div style=" padding:10px;">

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>