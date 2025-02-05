<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Example</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f9;
    }

    .container {
        width: 90%;
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
        font-size: 24px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        border: 1px solid transparent;
        padding: 12px;
        text-align: left;
    }

    th {
        background-color: rgb(13, 131, 131);
        color: white;
    }

    td {
        background-color: #f9f9f9;
    }

    tr:hover td {
        background-color: #e4e4e4;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 5px;
        font-weight: bold;
        font-size: 1rem;
        text-decoration: none;
        color: white;
        background-color: rgb(14, 104, 200);
        display: inline-block;
    }

    .btn:hover {
        background-color: rgb(167, 218, 200);
    }

    .action-btns {
        display: flex;
        gap: 5px;
    }

    /* Role-Based Colors */
    .role {
        font-weight: bold;
        padding: 5px 10px;
        border-radius: 5px;
        text-align: center;
        display: inline-block;
    }

    .role-admin {
        background-color: #007bff; /* Blue */
        color: white;
    }

    .role-superadmin {
        background-color: #f1c40f; /* Yellow */
        color: black;
    }

    .role-user {
        background-color: #28a745; /* Green */
        color: white;
    }

</style>
</head>
<body class="vertical light">

<?php include('navandside/nav.php'); ?>
<?php include('navandside/sidebar.php'); ?>
<?php include('navandside/head.php'); ?>

<div class="container">
    <h2><i class="fas fa-user-circle"></i> Personal Account</h2>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>John Doe</td>
                <td>johndoe@example.com</td>
                <td><span class="role role-admin">Admin</span></td>
                <td class="action-btns">
                    <button class="btn"><i class="fas fa-sync-alt"></i></button> <!-- Update Icon -->
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td>Jane Smith</td>
                <td>janesmith@example.com</td>
                <td><span class="role role-user">User</span></td>
                <td class="action-btns">
                    <button class="btn"><i class="fas fa-sync-alt"></i></button> <!-- Update Icon -->
                </td>
            </tr>
            <tr>
                <td>3</td>
                <td>Anna Johnson</td>
                <td>annajohnson@example.com</td>
                <td><span class="role role-superadmin">Super Admin</span></td>
                <td class="action-btns">
                    <button class="btn"><i class="fas fa-sync-alt"></i></button> <!-- Update Icon -->
                </td>
            </tr>
            <tr>
                <td>4</td>
                <td>Mark Taylor</td>
                <td>marktaylor@example.com</td>
                <td><span class="role role-user">User</span></td>
                <td class="action-btns">
                    <button class="btn"><i class="fas fa-sync-alt"></i></button> <!-- Update Icon -->
                </td>
            </tr>
            <tr>
                <td>5</td>
                <td>Emily White</td>
                <td>emilywhite@example.com</td>
                <td><span class="role role-admin">Admin</span></td>
                <td class="action-btns">
                    <button class="btn"><i class="fas fa-sync-alt"></i></button> <!-- Update Icon -->
                </td>
            </tr>
        </tbody>
    </table>
</div>

</body>
</html>

