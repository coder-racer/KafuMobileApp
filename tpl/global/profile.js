App.templates['global/profile'] = () => {
    App.setContent(`
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
    `);


}