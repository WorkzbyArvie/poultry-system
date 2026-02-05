<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poultry Management System</title>
    <style>
        /* Force these styles so the page isn't white-on-white */
        body { background-color: #1a1a1a !important; color: white !important; margin: 0; display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: sans-serif; }
        .content { text-align: center; }
        h1 { color: #63b3ed; font-size: 4rem; letter-spacing: 0.2em; text-transform: uppercase; margin-bottom: 10px; }
        p { color: #a0aec0; font-size: 1.2rem; margin-bottom: 40px; }
        .btn { background-color: #ed8936 !important; color: white !important; padding: 15px 35px; border-radius: 8px; font-weight: bold; text-decoration: none; font-size: 1.2rem; display: inline-block; border: none; cursor: pointer; }
        .btn:hover { background-color: #f6ad55 !important; }
    </style>
</head>
<body>
    <div class="content">
    <h1>POULTRY SYSTEM</h1>
    <p>Integrated Management & Strategic Decision Support</p>

    <a href="{{ route('login') }}" class="btn">SUPER ADMIN PORTAL</a>
    
    <p style="font-size: 0.8rem; color: #718096; margin-top: 20px; font-style: italic;">
        Authorized Personnel Only
    </p>
</div>
</body>
</html>