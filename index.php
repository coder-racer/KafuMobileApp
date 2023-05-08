<?php
header("Cache-Control: no-cache, must-revalidate");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Приложение KAFU</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="tempalte/css/style.css">
    <link rel="stylesheet" href="tempalte/css/ui.css">
    <link rel="stylesheet" href="tempalte/css/media.css">
    <link rel="stylesheet"
          href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">

</head>
<body>
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
                <img src="tempalte/img/LogoLogin.png" alt="">
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
        <img src="tempalte/img/LogoLogin.png" alt="">
        <div class="logo_label">
            KAFU
        </div>
    </div>

    <nav>
        <div class="nav_bg">
            <a class="nav_item" id="profile">
                <i class="las la-user"></i>
                <span class="text">
                    Профиль
                </span>
            </a>

            <a onclick="clearMenu()" id="journal" class="nav_item active">
                <i class="las la-book"></i>
                <span class="text">
                    Журнал
                </span>
            </a>
            <a class="nav_item" id="news">
                <i class="las la-newspaper"></i>
                <span class="text">
                    Новости
                </span>
            </a>
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
        <section id="section_profile">
            <div class="section_profile_block">
                <div class="profile_block">
                    <div class="left">
                        <div class="fio"></div>
                        <img src="" alt="">
                    </div>
                    <div class="right">
                        <div class="item_list">
                            <div class="profile_item">
                                <span class="name"></span>
                                <span class="value"></span>
                            </div>
                        </div>
                        <button onclick="logOut()" class="logout">Выйти из аккаунта</button>
                    </div>
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
            </div>

        </section>
        <section id="section_journal" class="active">
            <div class="filter">
                <label>
                    <span>Год</span>
                    <select id="select_year" name="">
                    </select>
                </label>
                <label>
                    <span>Семестр</span>
                    <select id="select_academic" name="">
                    </select>
                </label>
            </div>
            <div class="course_list">
            </div>
        </section>
        <section id="section_news">
            <div class="news_list">
            </div>
        </section>
    </div>
    <footer>
    </footer>
</div>

<script src="tempalte/js/utils.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="tempalte/js/progressBar.js"></script>
<script src="tempalte/js/kafuApi.js"></script>
<script src="tempalte/js/main.js"></script>

</body>
</html>