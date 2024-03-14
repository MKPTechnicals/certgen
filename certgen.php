<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Generator</title>
    <style>
        /* Modern look with internal CSS */
        body {
            font-family: Arial, sans-serif;
            background: url('https://source.unsplash.com/random') no-repeat center center fixed; 
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }
        .page-container {
            max-width: 960px;
            margin: auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.8);
            box-shadow: 0px 0px 20px rgba(0,0,0,0.1);
        }
        .page-text {
            font-size: 1.2em;
            color: #333;
        }
        .page-form {
            margin-top: 20px;
        }
        .page-textinput {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .page-textinput[type="submit"] {
            background: #333;
            color: #fff;
            cursor: pointer;
        }
        .page-textinput[type="submit"]:hover {
            background: #444;
        }
        .page-footer {
            margin-top: 20px;
            text-align: center;
            color: #333;
            font-size: 0.9em;
        }

        .page-footer span {
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <h2 class="page-text">Generate Certificates</h2>
        <form method="POST" enctype="multipart/form-data" class="page-form" id="form1">
            <label for="cert_image" class="page-text">Upload Certificate Image:</label>
            <input type="file" name="cert_image" accept="image/*" class="page-textinput">
            <br>
            <label for="overlay_height" class="page-text">Overlay Height (in pixels):</label>
            <input type="number" name="overlay_height" min="0" step="1" class="page-textinput">
            <br>
            <label for="csv_file" class="page-text">Upload CSV File:</label>
            <input type="file" name="csv_file" accept=".csv" class="page-textinput">
            <br>
            <label for="font_file" class="page-text">Upload Font (TTF/OTF) File:</label>
            <input type="file" name="font_file" accept=".ttf,.otf" class="page-textinput">
            <br>
            <label for="font_size" class="page-text">Font Size:</label>
            <input type="number" name="font_size" min="1" step="1" class="page-textinput">
            <br>
            <label for="font_color" class="page-text">Font Color:</label>
            <input type="color" name="font_color" class="page-textinput">
            <br>
            <input type="submit" name="submit" value="Generate Certificates" class="page-textinput">
        </form>
        <p style="color :red;" class="page-text">Recommendation : Use Breezip to unzip the file</p>

        <?php

        // File paths for storing visitor count and certificate count
$visitorCountFile = 'visitor_count.txt';
$certificateCountFile = 'certificate_count.txt';
$certificateCount = (int)file_get_contents($certificateCountFile);

// Function to read and increment the count from a file
function incrementCount($countFile) {
    $count = 1;
    if (file_exists($countFile)) {
        $count = (int)file_get_contents($countFile);
        $count++;
    }
    file_put_contents($countFile, $count);
    return $count;
}
if(isset($_POST['submit'])) {
    // Check if files were uploaded
    if(isset($_FILES['cert_image']) && $_FILES['cert_image']['error'] === UPLOAD_ERR_OK &&
       isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK &&
       isset($_FILES['font_file']) && $_FILES['font_file']['error'] === UPLOAD_ERR_OK) {

        $cert_image = $_FILES['cert_image']['tmp_name'];
        $cert_image_info = getimagesize($cert_image);
        
        if($cert_image_info !== false && in_array($cert_image_info['mime'], array('image/jpeg', 'image/png'))) {
            $overlay_height = isset($_POST['overlay_height']) ? (int)$_POST['overlay_height'] : 100;
            $font_size = isset($_POST['font_size']) ? (int)$_POST['font_size'] : 20;
            $font_file = $_FILES['font_file']['tmp_name'];
            $font_color = isset($_POST['font_color']) ? $_POST['font_color'] : '#000000'; // Default to black if not provided
            
            $cert_image_width = $cert_image_info[0];
            $cert_image_height = $cert_image_info[1];

            $names = array();

            if(($handle = fopen($_FILES['csv_file']['tmp_name'], 'r')) !== FALSE) {
                while(($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    $name = $data[0];
                    $names[] = $name;
                }
                fclose($handle);
            }

            // Create a temporary directory to store generated images
            $tempDir = 'temp_' . time();
            mkdir($tempDir);

            // Generate certificates with centered text overlay
            foreach($names as $name) {
                $image = imagecreatefromstring(file_get_contents($cert_image));
                $text_color = imagecolorallocate($image, 
                                                hexdec(substr($font_color, 1, 2)), // Red component
                                                hexdec(substr($font_color, 3, 2)), // Green component
                                                hexdec(substr($font_color, 5, 2))); // Blue component

                // Load the custom font
                $font = $font_file;

                // Calculate text position
                $bbox = imagettfbbox($font_size, 0, $font, $name);
                $text_width = $bbox[2] - $bbox[0]; // Text width
                $text_x = ($cert_image_width - $text_width) / 2; // X coordinate for centering
                $text_y = $overlay_height; // Y coordinate

                // Overlay the centered name on the image
                imagettftext($image, $font_size, 0, $text_x, $text_y, $text_color, $font, $name);

                // Save the image to the temporary directory
                $imageFileName = $tempDir . '/' . $name . '.png';
                imagepng($image, $imageFileName);

                // Clean up
                imagedestroy($image);
                $certificateCount = incrementCount($certificateCountFile);
            }

            // Create a zip archive
            $zipFileName = 'generated_certificates.zip';
            $zip = new ZipArchive();
            if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
                // Add all files from the temporary directory to the zip
                $files = scandir($tempDir);
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..') {
                        $zip->addFile($tempDir . '/' . $file, $file);
                    }
                }
                $zip->close();
                
                // Provide the zip file for download
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
                readfile($zipFileName);

                // Clean up
                unlink($zipFileName);
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..') {
                        unlink($tempDir . '/' . $file);
                    }
                }
                rmdir($tempDir);
            } else {
                echo 'Failed to create zip archive.';
            }
        } else {
            echo '<p>Please upload a valid image file.</p>';
        }
    } else {
        echo '<p>Error uploading files.</p>';
    }
}

$visitorCount = incrementCount($visitorCountFile);
?>
        <div class="page-footer">
            <span>Number of Visitors: <?php echo $visitorCount; ?></span>
            <span>Number of Certificates Generated: <?php echo $certificateCount; ?></span>
        </div>
    </div>
</body>
</html>
