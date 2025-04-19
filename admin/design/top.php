<?php
include '../internet/connect_ka.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Sidebar</title>
     <link rel="icon" href="../logo/logo.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Sidebar Styles */
        .sidebar {
            transition: transform 0.3s ease-in-out, width 0.3s ease-in-out;
            width: 16rem;
            overflow: hidden;
        }

        /* Mobile Mode: Sidebar Hidden by Default */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                height: 100vh;
                z-index: 50;
            }

            .sidebar.open {
                transform: translateX(0);
            }
        }

        /* PC Mode: Collapsible Sidebar */
        @media (min-width: 769px) {
            .sidebar.closed {
                width: 0;
            }

            .main-content {
                transition: margin-left 0.3s ease-in-out;
                margin-left: 16rem;
            }

            .main-content.shrink {
                margin-left: 0;
            }
        }
    </style>
</head>