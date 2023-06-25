App.templates['global/profile'] = () => {
    App.setContent(`
        <section id="section_profile">
            <div class="section_profile_block">
                <div class="profile_block">
                   
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
    platon.getProfile((data) => {
        console.log(data);

        let description = ``;
        let profName = data.professionName.split(' - ')
        description += itemData("Номер Профессии", profName[0])
        description += itemData(profName[1])
        description += itemData("Номер группы", data.groupName)
        description += itemData("Курс", data.courseNumber)
        description += itemData("GPA", data.gpa)

        App.bind('.profile_block',
            itemProfile(
                data.lastName + ' ' + data.firstName + ' ' + data.patronymic,
                'data:image/png;base64,' + data.photoBase64,
                description
            )
        )

    });


    function itemData(name, value = null)
    {
        if (value)
        return `
            <div class="profile_item">
                <span class="name">${name}</span>
                <span class="value">${value}</span>
            </div>
        `
        return `
            <div class="profile_item">
                <span style="max-width: fit-content;" class="value">${name}</span>
            </div>
        `

    }

    function itemProfile(fio, image, data)
    {
        return `
            <div class="left">
                <div class="fio">${fio}</div>
                <img src="${image}" alt="">
            </div>
            <div class="right">
                <div class="item_list">
                    ${data}                    
                </div>
                <button onclick="logOut()" class="logout">Выйти из аккаунта</button>
            </div>
        `
    }

}