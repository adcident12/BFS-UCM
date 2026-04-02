<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — UCM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            background-color: #0f172a;
            background-image:
                linear-gradient(rgba(99,102,241,0.07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99,102,241,0.07) 1px, transparent 1px);
            background-size: 40px 40px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: #f1f5f9;
        }
        .wrap { width: 100%; max-width: 400px; text-align: center; }
        .icon-box {
            width: 5rem; height: 5rem;
            border-radius: 1.25rem;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .icon-box svg { width: 2.25rem; height: 2.25rem; }
        .code {
            font-size: 4.5rem; font-weight: 800; line-height: 1;
            background: linear-gradient(135deg, #818cf8, #c084fc);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.75rem;
        }
        .title { font-size: 1.25rem; font-weight: 700; color: #f1f5f9; margin-bottom: 0.5rem; }
        .desc { font-size: 0.875rem; color: #94a3b8; line-height: 1.6; margin-bottom: 1.75rem; }
        .card {
            background: rgba(30,41,59,0.9);
            border: 1px solid rgba(99,102,241,0.15);
            border-radius: 1.25rem;
            padding: 2.5rem 2rem;
            box-shadow: 0 25px 50px rgba(0,0,0,0.4), 0 0 80px rgba(99,102,241,0.06);
        }
        .btn {
            display: inline-flex; align-items: center; gap: 0.375rem;
            padding: 0.625rem 1.5rem;
            background: #6366f1; color: #fff;
            border-radius: 0.75rem; text-decoration: none;
            font-size: 0.875rem; font-weight: 600;
            transition: background 0.15s, transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 12px rgba(99,102,241,0.35);
        }
        .btn:hover { background: #5254cc; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(99,102,241,0.45); }
        .btn-ghost {
            display: inline-flex; align-items: center; gap: 0.375rem;
            padding: 0.625rem 1.25rem;
            color: #64748b;
            border-radius: 0.75rem; text-decoration: none;
            font-size: 0.8125rem; font-weight: 600;
            margin-top: 0.75rem;
            transition: color 0.15s;
        }
        .btn-ghost:hover { color: #94a3b8; }
        .actions { display: flex; flex-direction: column; align-items: center; gap: 0.25rem; }
        .ucm-badge {
            margin-top: 2rem;
            font-size: 0.75rem; color: #334155; font-weight: 500; letter-spacing: 0.05em;
        }
        .detail-box {
            background: rgba(15,23,42,0.6);
            border: 1px solid rgba(99,102,241,0.1);
            border-radius: 0.75rem;
            padding: 0.875rem 1rem;
            font-size: 0.8rem; color: #64748b;
            margin-bottom: 1.5rem;
            text-align: left;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            @yield('body')
        </div>
        <p class="ucm-badge">User Centralized Management</p>
    </div>
</body>
</html>
