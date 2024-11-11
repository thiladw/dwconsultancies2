<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $project_name = $_POST['project_name'];
    $project_details = $_POST['project'];

    // Define recipient emails
    $to1 = "info@dwconsultancies.com";  
    $to2 = "thilakshana@dwconsultancies.com";  
    $to3 = "keshan@dwconsultancies.com";  

    // Define the subject as the project name
    $subject = "Project Quote Request: " . $project_name;

    // Define the email message content
    $message = "
    Name: $name\n
    Email: $email\n
    Phone: $phone\n
    Project Name: $project_name\n
    Project Details:\n$project_details
    ";

    // Define headers
    $headers = "From: no-reply@dwconsultancies.com" . "\r\n" .
               "Reply-To: $email" . "\r\n" .
               "X-Mailer: PHP/" . phpversion();

    // Send the email to the three recipients
    $success = true;
    $error_message = '';

    // Send email to info@dwconsultancies.com
    if (!mail($to1, $subject, $message, $headers)) {
        $success = false;
        $error_message .= "Failed to send to info@dwconsultancies.com.\n";
    }

    // Send email to thilakshana@dwconsultancies.com
    if (!mail($to2, $subject, $message, $headers)) {
        $success = false;
        $error_message .= "Failed to send to thilakshana@dwconsultancies.com.\n";
    }

    // Send email to keshan@dwconsultancies.com
    if (!mail($to3, $subject, $message, $headers)) {
        $success = false;
        $error_message .= "Failed to send to keshan@dwconsultancies.com.\n";
    }

    // Output success or failure message
    if ($success) {
        echo "Thank you for your inquiry. We will get back to you soon!";
    } else {
        echo "There was a problem sending your message. Please try again later.\n";
        echo $error_message;  // Display specific errors if any
    }
}
?>
