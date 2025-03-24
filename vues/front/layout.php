<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $page_description ?? '' ?>">
    <title><?= $title ?></title>
    <link rel="shortcut icon" href="<?= IMAGES_URL ?>oh!LiveRatingLogo.png" type="image/x-icon"/>

    <!-- Styles CSS communs -->
    <link rel="stylesheet" href="<?= CSS_URL ?>theme.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>header.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>footer.css">
    <link rel="stylesheet" href="<?= CSS_URL ?>tables.css">
    
    <!-- Styles CSS spécifiques à la page -->
    <?php foreach ($css as $stylesheet): ?>
        <link rel="stylesheet" href="<?= CSS_URL ?><?= $stylesheet ?>">
    <?php endforeach; ?>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

    <!-- Contenu principal -->
    <main>
        <?= $content ?>
    </main>

    <!-- Scripts JavaScript communs -->
    <script>
        const URL = '<?= URL ?>';
        const IMAGES_URL = '<?= IMAGES_URL ?>';
        const CSS_URL = '<?= CSS_URL ?>';
        const JS_URL = '<?= JS_URL ?>';
    </script>
    <script src="<?= JS_URL ?>main.js"></script>
    <script src="<?= JS_URL ?>theme.js"></script>
    <script src="<?= JS_URL ?>tables.js"></script>
    
    <!-- Scripts JavaScript spécifiques à la page -->
    <?php if (isset($js) && is_array($js)): ?>
        <?php foreach ($js as $script): ?>
            <script src="<?= JS_URL ?><?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>