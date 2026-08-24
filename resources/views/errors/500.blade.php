<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Server Error | InvoiceFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            margin: 0;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
        }

        .error-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            padding: 48px 32px;
            max-width: 400px;
            width: 90%;
        }

        .error-code {
            font-size: 80px;
            font-weight: 800;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }

        .error-title {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin: 16px 0 8px;
        }

        .error-message {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 24px;
        }

        .btn-home {
            display: inline-block;
            padding: 10px 24px;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="error-card">
        <h1 class="error-code">500</h1>
        <h2 class="error-title">Something Went Wrong</h2>
        <p class="error-message">An unexpected error occurred. Please try again later or contact support if the problem persists.</p>
        <a href="{{ url('/dashboard') }}" class="btn-home">Go to Dashboard</a>
    </div>
</body>

</html>