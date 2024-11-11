<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $jobTitle = $_POST['jobTitle'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $coverLetter = $_POST['message'];
    
    // Get uploaded resume
    $resume = $_FILES['resume'];
    $resumePath = $resume['tmp_name'];
    $resumeName = $resume['name'];
    
    // Job experience details
    $employers = $_POST['employer_name'];
    $startDates = $_POST['start_date'];
    $endDates = $_POST['end_date'];
    $stillWorking = $_POST['still_working'];
    $duties = $_POST['duties'];
    
    // Software experience details
    $softwareExperience = $_POST['software_experience'];
    $yearsExperience = $_POST['years_experience'];
    $otherSoftware = $_POST['other_software'];

    // Create email subject
    $subject = "$jobTitle - $name";

    // Prepare email body content
    $message = "Job Application for $jobTitle\n";
    $message .= "Applicant Name: $name\n";
    $message .= "Email: $email\n";
    $message .= "Phone: $phone\n\n";
    $message .= "Cover Letter:\n$coverLetter\n\n";

    // Job Experience details
    foreach ($employers as $index => $employer) {
        $message .= "Employer " . ($index + 1) . ": $employer\n";
        $message .= "Start Date: " . $startDates[$index] . "\n";
        $message .= "End Date: " . ($stillWorking[$index] ? "Still working" : $endDates[$index]) . "\n";
        $message .= "Duties: " . $duties[$index] . "\n\n";
    }

    // Software experience details
    $softwareList = ['Bluebeam Revu', 'Asta Power Project', 'MS Project', 'PlanSwift', 'AutoCAD'];
    foreach ($softwareList as $index => $software) {
        $message .= "$software Experience: " . $softwareExperience[$index] . "\n";
        $message .= "Years of Experience: " . ($yearsExperience[$index] ?? '0') . "\n\n";
    }
    $message .= "Other Construction-Related Software: $otherSoftware\n";

    // Send email to both you and Thilakshana
    $mail = new PHPMailer(true);
    try {
        $mail->setFrom('no-reply@yourdomain.com', 'Job Application');
        
        // Add both recipients (your email and Thilakshana's email)
        $mail->addAddress('info@dwconsultancies.com');  // Your email
        $mail->addAddress('thilakshana@dwconsultancies.com');  // Thilakshana's email

        $mail->Subject = $subject;
        $mail->Body = $message;
        
        // Attach resume (PDF)
        if (is_uploaded_file($resumePath)) {
            $mail->addAttachment($resumePath, $resumeName);
        }

        // Send email
        $mail->send();
    } catch (Exception $e) {
        echo "Error sending email: {$mail->ErrorInfo}";
    }

    // Generate Excel file
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Applicant Data');

    // Header row for individual applicant data
    $headers = ['Job Title', 'Applicant Name', 'Email', 'Phone', 'Cover Letter', 'Software Experience', 'Years of Experience', 'Employer Name', 'Start Date', 'End Date', 'Duties'];
    $sheet->fromArray($headers, NULL, 'A1');

    // Add individual applicant data to Excel
    $row = 2;
    $sheet->setCellValue("A$row", $jobTitle);
    $sheet->setCellValue("B$row", $name);
    $sheet->setCellValue("C$row", $email);
    $sheet->setCellValue("D$row", $phone);
    $sheet->setCellValue("E$row", $coverLetter);

    // Adding software experience to Excel
    foreach ($softwareList as $index => $software) {
        $sheet->setCellValue("F$row", $software . ': ' . $softwareExperience[$index]);
        $sheet->setCellValue("G$row", $yearsExperience[$index]);
        $row++;
    }

    // Adding job experience to Excel
    foreach ($employers as $index => $employer) {
        $sheet->setCellValue("H$row", $employer);
        $sheet->setCellValue("I$row", $startDates[$index]);
        $sheet->setCellValue("J$row", ($stillWorking[$index] ? "Still working" : $endDates[$index]));
        $sheet->setCellValue("K$row", $duties[$index]);
        $row++;
    }

    // Save the file to the server
    $writer = new Xlsx($spreadsheet);
    $fileName = "Applicant_Data_$jobTitle.xlsx";
    $filePath = "uploads/$fileName";  // Make sure the folder exists or change path as needed
    $writer->save($filePath);

    // Attach summary Excel file to the email
    try {
        $mail->addAttachment($filePath);
        $mail->send();
        echo "Application submitted successfully.";
    } catch (Exception $e) {
        echo "Error sending email with attachment: {$mail->ErrorInfo}";
    }
}
?>
