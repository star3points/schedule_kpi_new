<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Summit</title>
    <link rel="stylesheet" href="dist/app.css" />
{{--    <script src="https://crm.lichishop.com/bitrix/js/api.js?v18"></script>--}}
    <script>
        window.app_settings = {
            'USER_DATA' : "<?php echo $_REQUEST['USER_DATA'] ?? ''; ?>",
        };
    </script>
</head>
<body>
<div id="app"></div>
<script src="dist/app.js"></script>
</body>
</html>
