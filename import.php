<!-- <!DOCTYPE html>
<html>

<head>
    <title>Table Of Uloded Excel file </title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>

<body> -->
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

            // Get the highest column and row indexes
            $highestColumn = $sheet->getHighestColumn();
            $highestRow = $sheet->getHighestRow();
            // echo $highestColumn;
            // echo $highestRow;
            // die();
            // Start building the HTML table
            echo '<table id="excelTable" class="table table-bordered">';
        
            // Iterate through each row of the worksheet
            for ($row = 1; $row <= $highestRow; $row++) {
                echo '<tr>';

                // Iterate through each cell of the row
                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $cellValue = $sheet->getCell($col . $row)->getValue();
                    echo '<td class="test' . $col . '"><div class="td_width" data-value="' . $cellValue . '">' . $cellValue . '</div></td>';
                }

                echo '</tr>';
            }

            echo '</table>';
        } catch (Exception $e) {
            echo 'Error loading file: ', $e->getMessage();
        }
    }
    ?>
<!-- 
</body>

</html> -->
<!-- Include the D3.js library and custom JavaScript code -->
<script src="https://d3js.org/d3.v7.min.js"></script>
<script>
    // JavaScript code to create a heatmap using D3.js
    // d3.selectAll("#excelTable td .test").each(function() {
    d3.selectAll("#excelTable td.testH .td_width").each(function() {
        var cellValue = +d3.select(this).attr("data-value");
        if (cellValue > 100 && cellValue <= 160) {
            var background = "rgb(241, 196, 15)";
        } else if (cellValue > 160) {
            var background = "rgb(255, 0, 0)";
        } else {
            var background = "rgb(0, 255, 0)";
        }
        d3.select(this).style("background-color", background);
        d3.select(this).style("width", cellValue);
    });
</script>