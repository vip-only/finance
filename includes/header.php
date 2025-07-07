<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Finance Pro'; ?></title>
    <link rel="stylesheet" href="statics/css/style.css">
    <?php if(isset($custom_styles)) echo $custom_styles; ?>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="header-right">
                <div class="header-title">Finance Pro</div>
            </div>
        </div>
    </header>