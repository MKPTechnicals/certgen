# Mass Certificate Generator (CertGen)

CertGen is a PHP-based web application designed to generate multiple certificates efficiently. This application allows users to upload a certificate template image, a CSV file containing names, and a font file for customization. It then generates individual certificates by overlaying names onto the template image, creating a downloadable zip file containing all the generated certificates.

## Features

- **Bulk Certificate Generation**: Upload a certificate template image and a CSV file with names to generate multiple certificates at once.
- **Customization Options**: Adjust overlay height, font size, and font color to customize the appearance of the generated certificates.
- **Font Support**: Upload TrueType (.ttf) or OpenType (.otf) font files for personalized text styling.
- **Efficient Processing**: Automatically centers text overlays on the certificate template image for a professional look.
- **Zip Archive Download**: Download a zip file containing all the generated certificates for easy distribution.

## Usage

1. **Setup Environment**: Ensure your server environment supports PHP.
2. **Upload Files**: Fill out the form by uploading the certificate template image, CSV file with names, and font file. Adjust customization options as needed.
3. **Generate Certificates**: Click on the "Generate Certificates" button to initiate the certificate generation process.
4. **Download**: Once the generation process completes, download the zip file containing all the certificates.

## Recommendations

- **Image Requirements**: Ensure that the certificate template image is in either JPEG or PNG format.
- **CSV File Format**: The CSV file should contain a single column with names to be included on the certificates.
- **Font Selection**: Choose a font file that best suits the style and design of your certificates.
- **File Extraction**: If needed, use tools like Breezip to unzip the downloaded zip file.

## Notes

- **Visitor Count**: The application keeps track of the number of visitors accessing the system.
- **Certificate Count**: It also tracks the total number of certificates generated.
- **Temporary Files**: Temporary files are stored in a directory named `temp_<timestamp>`, which is cleaned up after certificate generation.

## Authors

- [@mkptechnicals](https://www.github.com/MKPTechnicals)

Feel free to contribute to the project by submitting bug reports, feature requests, or pull requests.
