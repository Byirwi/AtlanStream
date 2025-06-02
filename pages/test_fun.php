<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>wrs</title>
    <style>
        body {
            margin: 0;
            background-color: #0078D7;
            color: #ffffff;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(255, 255, 255, 0.1);
            border-top: 5px solid #ffffff;
            border-radius: 50%;
            animation: spin 2s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .message {
            margin-top: 20px;
            font-size: 18px;
        }
        .small-text {
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="spinner"></div>
    <div class="message">Restarting...</div>
    <div class="small-text">Your PC will restart in a few moments.</div>
</body>
</html>