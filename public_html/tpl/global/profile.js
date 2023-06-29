App.templates['global/profile'] = () => {
    App.setContent(`
        <section id="section_profile">
            <div class="section_profile_block">
                <div class="profile_block_flex">
                    <div class="docs">
                        <span>Выберите справку</span>
                        <select id="select_docs" name="">
                            
                        </select>
                           <div class="button get_doc">Выдать справку</div>
                    </div>
                    <div class="profile_block">
                       <div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
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

    let fio = ``

    platon.getProfile((data) => {

        let description = ``;
        let profName = data.professionName.split(' - ')
        description += itemData("Номер Профессии", profName[0])
        description += itemData(profName[1])
        description += itemData("Номер группы", data.groupName)
        description += itemData("Курс", data.courseNumber)
        description += itemData("GPA", data.gpa)
        fio = data.lastName + ' ' + data.firstName + ' ' + data.patronymic
        document.querySelector('.lds-roller').remove()
        App.bind('.profile_block',
            itemProfile(
                data.lastName + ' ' + data.firstName + ' ' + data.patronymic,
                'data:image/png;base64,' + data.photoBase64,
                description
            )
        )

        platon.getListDocs((data)=>{

            let html = ``
            data.forEach(el => html += `<option value="${el}">${el}</option>`)

            App.bind('#select_docs', html);
        })

    });

    App.on('click', '.get_doc', ()=>{
        const baseUrl = window.location.origin;
        const url = new URL("/getDocument", baseUrl);

        url.searchParams.append("fio", fio);
        url.searchParams.append("name", App.getBlock('#select_docs').value);

        window.open(url.toString(), '_blank');

    })

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