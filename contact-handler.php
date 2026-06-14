<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone'] ?? '');
    $message = sanitize($_POST['message']);
    
    if (empty($name) || empty($email) || empty($message)) {
        $_SESSION['error'] = 'Please fill in all required fields';
        redirect('contact.php');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Please enter a valid email address';
        redirect('contact.php');
    }
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $message);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Thank you for your message! We\'ll get back to you soon.';
    } else {
        $_SESSION['error'] = 'Failed to send message. Please try again.';
    }
    
    $stmt->close();
    $conn->close();
    
    redirect('contact.php');
} else {
    redirect('index.php');
}
