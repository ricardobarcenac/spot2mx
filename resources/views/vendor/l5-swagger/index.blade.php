<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $documentationTitle ?? 'API Documentation' }}</title>
    <link rel="stylesheet" type="text/css" href="{{ l5_swagger_asset($documentation ?? 'default', 'swagger-ui.css') }}">
    <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation ?? 'default', 'favicon-32x32.png') }}" sizes="32x32"/>
    <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation ?? 'default', 'favicon-16x16.png') }}" sizes="16x16"/>
    <style>
    html
    {
        box-sizing: border-box;
        overflow: -moz-scrollbars-vertical;
        overflow-y: scroll;
    }
    *,
    *:before,
    *:after
    {
        box-sizing: inherit;
    }

    body {
      margin:0;
      background: #fafafa;
    }
    </style>
    @if(config('l5-swagger.defaults.ui.display.dark_mode'))
        <style>
            body#dark-mode,
            #dark-mode .scheme-container {
                background: #1b1b1b;
            }
            #dark-mode .scheme-container,
            #dark-mode .opblock .opblock-section-header{
                box-shadow: 0 1px 2px 0 rgba(255, 255, 255, 0.15);
            }
            #dark-mode .operation-filter-input,
            #dark-mode .dialog-ux .modal-ux,
            #dark-mode input[type=email],
            #dark-mode input[type=file],
            #dark-mode input[type=password],
            #dark-mode input[type=search],
            #dark-mode input[type=text],
            #dark-mode textarea{
                background: #343434;
                color: #e7e7e7;
            }
            #dark-mode .title,
            #dark-mode li,
            #dark-mode p,
            #dark-mode td,
            #dark-mode th,
            #dark-mode span,
            #dark-mode div{
                color: #e7e7e7;
            }
            #dark-mode .opblock .opblock-summary-description,
            #dark-mode .parameter__name,
            #dark-mode .parameter__type{
                color: #e7e7e7;
            }
            #dark-mode .response-col_status,
            #dark-mode .response-col_links{
                color: #e7e7e7;
            }
            #dark-mode .opblock-description-wrapper p,
            #dark-mode .opblock-external-docs-wrapper p,
            #dark-mode .opblock-title_normal p{
                color: #e7e7e7;
            }
            #dark-mode .opblock .opblock-summary-description,
            #dark-mode .parameter__name,
            #dark-mode .parameter__type{
                color: #e7e7e7;
            }
            #dark-mode .response-col_status,
            #dark-mode .response-col_links{
                color: #e7e7e7;
            }
            #dark-mode .opblock-description-wrapper p,
            #dark-mode .opblock-external-docs-wrapper p,
            #dark-mode .opblock-title_normal p{
                color: #e7e7e7;
            }
            #dark-mode .btn{
                color: #e7e7e7;
                border-color: #e7e7e7;
            }
            #dark-mode .btn:hover{
                color: #1b1b1b;
                background-color: #e7e7e7;
            }
        </style>
    @endif
</head>

<body id="dark-mode">
    <div id="swagger-ui"></div>

    <script src="{{ l5_swagger_asset($documentation ?? 'default', 'swagger-ui-bundle.js') }}"></script>
    <script src="{{ l5_swagger_asset($documentation ?? 'default', 'swagger-ui-standalone-preset.js') }}"></script>
    <script>
        window.onload = function() {
            // Build a system
            const ui = SwaggerUIBundle({
                url: "{{ $urlToDocs ?? url('api-docs.json') }}",
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "StandaloneLayout",
                validatorUrl: null,
                onComplete: function() {
                    console.log('Swagger UI loaded successfully');
                },
                onFailure: function(data) {
                    console.error('Swagger UI failed to load:', data);
                },
                docExpansion: "{{ config('l5-swagger.defaults.ui.display.doc_expansion', 'none') }}",
                apisSorter: "{{ config('l5-swagger.defaults.ui.operations_sort', 'alpha') }}",
                operationsSorter: "{{ config('l5-swagger.defaults.ui.operations_sort', 'alpha') }}",
                filter: {{ config('l5-swagger.defaults.ui.display.filter') ? 'true' : 'false' }},
                persistAuthorization: {{ config('l5-swagger.defaults.ui.authorization.persist_authorization') ? 'true' : 'false' }},
                showExtensions: true,
                showCommonExtensions: true,
                tryItOutEnabled: true,
                supportedSubmitMethods: [
                    'get',
                    'post',
                    'put',
                    'delete',
                    'patch'
                ]
            });

            window.ui = ui;
        };
    </script>
</body>
</html>