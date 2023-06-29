App.templates['global/news/list'] = () => {
    App.setContent(`
        <section id="section_news">
            <div class="news_list">
                <!-- Спиннер загрузки -->
                <div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
            </div>
        </section>
    `);

    platon.getNews((res)=>{
        // Скрываем спиннер загрузки
        document.querySelector('.lds-roller').remove()

        const html = res.map(el => elementNew(el)).join('');
        App.bind(".news_list", html);

        document.querySelectorAll(".new_item").forEach((item, index) => {
            setTimeout(() => {
                item.classList.remove('disable')
            }, 200 * index);
        });
    })

    App.on('click', ".new_item", (el)=>{
        window.open(el.dataset.href, "_blank")
    })

    function elementNew(data) {
        let img = `<img class="non_image" src="https://kafu.edu.kz/wp-content/themes/kafu/images/header/building-logo.svg" alt="">`
        if (data.img)
            img = `<img src="${data.img}" alt="">`
        return `
            <span data-href="${data.url}" class="new_item disable">
                ${img}
                <div class="info">
                    <div class="name">
                        ${data.title}
                    </div>
                    <div class="desc">
                        ${data.text}
                    </div>
                </div>
            </span>
        `;
    }
}
