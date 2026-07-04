<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dokumentasi API BillMate">
    <title>BillMate API Documentation</title>
    <!-- Swagger UI CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui.css" />
    <style>
        html {
            box-sizing: border-box;
            overflow: -margin-top-collapse;
        }
        *, *:before, *:after {
            box-sizing: inherit;
        }
        body {
            margin: 0;
            background: #fafafa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        /* Custom Header */
        .swagger-header {
            background-color: #1b1b1b;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .swagger-header .logo {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #4ade80; /* Sleek green branding color */
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .swagger-header .logo span {
            color: #ffffff;
        }
        .swagger-header .badge {
            background-color: #3b82f6;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <header class="swagger-header">
        <a href="#" class="logo">
            💸 Bill<span>Mate API Docs</span>
        </a>
        <span class="badge">v1.0.0</span>
    </header>

    <div id="swagger-ui"></div>

    <!-- Swagger UI JS Bundle and Preset -->
    <script src="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui-bundle.js" crossorigin></script>
    <script src="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui-standalone-preset.js" crossorigin></script>
    
    <script>
        window.onload = () => {
            window.ui = SwaggerUIBundle({
                url: '/swagger/openapi.json',
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                layout: "BaseLayout", // Standard simple layout without the search/bar at the top
                docExpansion: "list", // Keep endpoints listed
                defaultModelsExpandDepth: 1,
                defaultModelExpandDepth: 1,
                persistAuthorization: true
            });
        };
    </script>
</body>
</html>
