<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }
        
        .content {
            flex: 1; /* Ensures footer stays at the bottom */
        }

        footer {
            background: linear-gradient(135deg, #a60aa1, #6a0572);
            color: white;
            text-align: center;
            padding: 15px 10px;
            font-size: 14px;
            font-weight: 500;
            width: 100%;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.2);
        }

        footer a {
            color: #ffdd57;
            text-decoration: none;
            margin: 0 10px;
            font-weight: 600;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="content">
    <!-- Page content goes here -->
</div>

<footer>
    <div>
        &copy; <?php echo date('Y'); ?> Carpooling Service. All Rights Reserved.
    </div>
    <div style="margin-top: 5px;">
        <a href="privacy_policy.php">Privacy Policy</a> |
        <a href="terms.php">Terms of Service</a> 
    
    </div>
</footer>

</body>
</html>
