<!DOCTYPE html>
<html>

<head>
    <title>Excel Uploading PHP</title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
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
    </div>
    <?php
    require 'vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\IOFactory;

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
                        echo '<td class="td_hours"> <div class="div_width" style="width:' . $hours .'px'.';background-color:'.$background.'"  data-value="' . $hours . '">' . $hours . '</div></td>';
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

</body>
<!-- Include the D3.js library and custom JavaScript code -->
<!-- <script src="https://d3js.org/d3.v7.min.js"></script>
<script>
    // JavaScript code to create a heatmap using D3.js
    // d3.selectAll("#excelTable td .test").each(function() {
    d3.selectAll("#excelTable td.td_hours .div_width").each(function() {
        var cellValue = +d3.select(this).attr("data-value");
        if (cellValue > 100 && cellValue <= 160) {
            var background = "rgb(241, 196, 15)";
        } else if (cellValue > 160) {
            var background = "rgb(255, 0, 0)";
        } else {
            var background = "rgb(0, 255, 0)";
        }
        // d3.select(this).style("background-color", background);
        // d3.select(this).style("width", 10);
    });
</script> -->

</html>