<?php
//header('Clear-Site-Data: "cache", "storage", "executionContexts"');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Приложение KAFU</title>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Kafu">
    <link rel="apple-touch-icon" href="/template/img/favicon.png">
    <link rel="icon" type="image/png" href="/template/img/favicon.png">
    <link rel="manifest" href="/manifest.json">


    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/template/css/style.css">
    <link rel="stylesheet" href="/template/css/ui.css">
    <link rel="stylesheet" href="/template/css/media.css">
    <link rel="stylesheet"
          href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">


</head>
<body>
<div id="add-to-home-screen">
    <div class="bg"></div>
    <div class="modal">
        <div class="flex">
            <div class="icon">
                <img src="/template/img/favicon.png" alt="">
            </div>
            <div class="desc">
                <h4>Установить приложение KAFU</h4>
                <p>@coder_racer</p>
            </div>
        </div>
        <div class="buttons">
            <button id="skip-add-to-home-screen-btn">Отмена</button>
            <button id="add-to-home-screen-btn">Установить</button>
        </div>

    </div>
</div>
<div class="preloader overlay show">
    <div class="preloader_bg"></div>
    <div class="preloader_label">
        @coder_racer
        <div class="preloader_bg_white"></div>
    </div>
</div>

<div class="login_modal overlay show">
    <div class="login_modal_bg"></div>
    <div class="login_modal_label">
        <div class="login_top">
            <div class="login_logo">
                <img src="/template/img/LogoLogin.png" alt="">
            </div>


            <div class="form">
                <div class="text_label">
                    <span>Авторизация</span>
                    <div class="line"></div>
                </div>
                <label>
                    <span>Логин</span>
                    <input type="text" id="login" name="login">
                </label>
                <label>
                    <span>Пароль</span>
                    <input type="password" id="pass" name="password">
                </label>
                <button onclick="login(event)">Вход</button>

            </div>
        </div>

        <div class="login_copyright">
            @coder_racer
        </div>

    </div>
</div>

<div class="site">
    <div class="logo">
        <img src="/template/img/LogoLogin.png" alt="">
        <div class="logo_label">
            KAFU
        </div>
    </div>

    <nav>
        <div class="nav_bg">
            <span data-link="global/profile" class="nav_item" id="profile">
                <i class="las la-user"></i>
                <span class="text">
                    Профиль
                </span>
            </span>

            <span data-link="global/journal" id="journal" class="nav_item active">
                <i class="las la-book"></i>
                <span class="text">
                    Журнал
                </span>
            </span>
            <span data-link="global/news/list" class="nav_item" id="news">
                <i class="las la-newspaper"></i>
                <span class="text">
                    Новости
                </span>
            </span>
        </div>
        <div class="copyright">
            <div class="copyright_arrow"><i class="las la-angle-up"></i></div>
            <div class="copyright_content">
                <a href="https://t.me/coder_racer">
                    <i class="lab la-telegram"></i>
                    @coder_racer
                </a>
                <a href="https://vk.com/coderracer">
                    <i class="lab la-vk"></i>
                    Владислав Костров
                </a>
                <a href="mailto: admin@racer-cr.ru">
                    <i class="las la-envelope"></i>
                    admin@racer-cr.ru
                </a>
            </div>
            <div class="copyright_label">@coder_racer</div>
        </div>
    </nav>
    <header>
        <div class="header_left">
            <div class="icon">
                <i class="las la-book"></i>
            </div>
        </div>
        <div class="header_right">
            <div class="name">Журнал</div>
            <div class="date"></div>
        </div>
    </header>
    <div class="content container">

        <div class="shadow"></div>
        <section class="container_app"></section>

    </div>
    <footer>
    </footer>
</div>

<script src="/template/js/Delta.js"></script>
<script src="/template/js/spa.js"></script>
<script src="/template/js/utils.js"></script>
<script src="/template/js/pwa.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="/template/js/progressBar.js"></script>
<script src="/template/js/platonus.js"></script>
<script src="/template/js/main.js"></script>

</body>
</html>

</body>
</html>