<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
?>

<!DOCTYPE html>
<html>

<head>
    <title>Project Data Accordion</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1>Excel Upload</h1>

        <!-- <form action="import.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="excel_file" />
    <div class="form-group">
        <button type="submit" name="Submit" class="btn btn-success">Upload</button>
    </div>
</form> -->

        <!-- import.php -->
        <form method="POST" action="#" enctype="multipart/form-data">
            <div class="form-group">
                <label>Upload Excel File</label>
                <input type="file" name="excel_file" class="form-control">
            </div>
            <div class="form-group">
                <button type="submit" name="Submit" class="btn btn-success">Upload</button>
            </div>
            <p>Download Demo File from here : <a href="demo.ods"><strong>Demo.ods</strong></a></p>
        </form>
        <?php
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
                $projects = [];

                // Iterate through each row of the worksheet
                for ($row = 2; $row <= $highestRow; $row++) { // Start from row 2 to skip headers
                    $projectName = $sheet->getCell('K' . $row)->getValue(); // Column K for project name
                    $teamName = $sheet->getCell('J' . $row)->getValue(); // Column J for team name
                    $userName = $sheet->getCell('B' . $row)->getValue(); // Column B for user name
                    $hours = $sheet->getCell('H' . $row)->getValue(); // Column H for hours

                    // Check if the project already exists in the array
                    if (!isset($projects[$projectName])) {
                        $projects[$projectName] = [];
                    }

                    // Check if the team name already exists in the project
                    if (!isset($projects[$projectName][$teamName])) {
                        $projects[$projectName][$teamName] = [];
                    }

                    // Check if the user already exists in the team
                    if (!isset($projects[$projectName][$teamName][$userName])) {
                        $projects[$projectName][$teamName][$userName] = $hours;
                    } else {
                        $projects[$projectName][$teamName][$userName] += $hours;
                    }
                }

                // Display the project data in an accordion format
                // Display the project data in an accordion format
                echo '<div id="accordion">';
                $index = 1;

                foreach ($projects as $projectName => $teams) {
                    echo '<div class="card">';
                    echo '<div class="card-header" data-toggle="collapse" data-target="#collapse' . $index . '" aria-expanded="false" aria-controls="collapse' . $index . '"  id="heading' . $index . '">';
                    echo '<h5 class="mb-0">';
                    echo $projectName;
                    echo '</h5>';
                    echo '</div>';

                    echo '<div id="collapse' . $index . '" class="collapse" aria-labelledby="heading' . $index . '" data-parent="#accordion">';
                    echo '<div class="card-body">';
                    foreach ($teams as $teamName => $users) {
                        echo '<div class="alert alert-primary" role="alert">
                        <h6>' . $teamName . '</h6></div>';
                        foreach ($users as $userName => $hours) {
                            if ($hours > 100 &&  $hours <= 160) {
                                $background = "rgb(241, 196, 15)";
                            } else if ($hours > 160) {
                                $background = "rgb(255, 0, 0)";
                            } else {
                                $background = "rgb(0, 255, 0)";
                            }
                            
                            echo '<div class="user_hours" style="margin-left: 30px !important;">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <strong>' . $userName . '</strong> -'.$hours.' hours 
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: '. $hours .'%;background-color:' . $background . '" aria-valuenow="'. $hours .' " aria-valuemin="0" aria-valuemax="180">'. $hours .' hours</div>
                                        </div>
                                        
                                    </li>
                                </ul>
                            </div>';
                        }
                    }
                    echo '</div>';
                    echo '</div>';

                    echo '</div>';
                    $index++;
                }
                // <span class="badge badge-pill" style="width:' . $hours . 'px' . ';background-color:' . $background . '">' . $hours . ' hours</>

                echo '</div>';

                // Display in table format
                echo '<table id="excelTable" class="table table-bordered">';
                echo '<thead><tr><th>Project</th><th>Team</th><th>User</th><th>Hours</th></tr></thead>';
                echo '<tbody>';
                foreach ($projects as $projectName => $teams) {
                    foreach ($teams as $teamName => $users) {
                        foreach ($users as $userName => $hours) {
                            if ($hours > 100 &&  $hours <= 160) {
                                $background = "rgb(241, 196, 15)";
                            } else if ($hours > 160) {
                                $background = "rgb(255, 0, 0)";
                            } else {
                                $background = "rgb(0, 255, 0)";
                            }

                            echo '<tr>';
                            echo '<td>' . $projectName . '</td>';
                            echo '<td>' . $teamName . '</td>';
                            echo '<td>' . $userName . '</td>';
                            echo '<td class="td_hours"> <div class="div_width" style="width:' . $hours . 'px' . ';background-color:' . $background . '"  data-value="' . $hours . '">' . $hours . '</div></td>';
                            echo '</tr>';
                        }
                    }
                }
                echo '</tbody>';
                echo '</table>';
            } catch (Exception $e) {
                echo 'Error loading file: ', $e->getMessage();
            }
        }
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>