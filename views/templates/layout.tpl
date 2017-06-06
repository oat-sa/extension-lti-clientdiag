<?php
use oat\tao\helpers\Template;
use oat\tao\helpers\Layout;
use oat\tao\model\theme\Theme;
?><!doctype html>
<html class="no-js no-version-warning" lang="<?= tao_helpers_I18n::getLangCode() ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= Layout::getTitle() ?></title>
        <?= tao_helpers_Scriptloader::render() ?>
        <link rel="stylesheet" href="<?= Template::css('tao-main-style.css', 'tao') ?>" />
        <link rel="stylesheet" href="<?= Template::css('tao-3.css', 'tao') ?>" />
        <link rel="stylesheet" href="<?= Template::css('diagnostics.css', 'taoClientDiagnostic') ?>"/>
        <link rel="stylesheet" href="<?= Layout::getThemeStylesheet(Theme::CONTEXT_FRONTOFFICE) ?>" />
        <link rel="shortcut icon" href="<?= Template::img('favicon.ico', 'tao') ?>"/>
        <?= Layout::getAmdLoader(Template::js('loader/backoffice.min.js', 'tao'), 'controller/backoffice') ?>
    </head>
    <body class="diagnostic-scope">
<?php Template::inc('blocks/requirement-check.tpl', 'tao'); ?>
        <div class="content-wrap<?php if (!get_data('showControls')) :?> no-controls<?php endif; ?>">
            <?php if (get_data('showControls')){ ?>
                <header class="dark-bar clearfix">
                    <?= Layout::renderThemeTemplate(Theme::CONTEXT_BACKOFFICE, 'header-logo') ?>
                </header>
            <?php }?>


            <div id="feedback-box"></div>
            <?php Template::inc(get_data('content-template'), get_data('content-template-ext')); ?>
        </div>

        <?php if (get_data('showControls')){ ?>
            <?= Layout::renderThemeTemplate(Theme::CONTEXT_BACKOFFICE, 'footer') ?>
        <?php }?>

        <div class="loading-bar"></div>
    </body>
</html>
