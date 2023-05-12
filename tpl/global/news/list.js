App.templates['global/news/list'] = () => {
    App.setContent(`
        <section id="section_news">
            <div class="news_list">
            </div>
        </section>
    `);


    platon.getNews((res)=>{
        const html = res.map(el => elementNew(el)).join('');
        App.bind(".news_list", html)

        document.querySelectorAll(".new_item").forEach((item, index) => {
            setTimeout(() => {
                    item.classList.remove('disable')
            }, 200 * index);
        });
    })

    App.on('click', ".new_item", (el)=>{
    })

    function elementNew(data) {
        return `
            <span data-href="${data.url}" class="new_item disable">
                <img src="${data.img}" alt="">
                <div class="name">
                    ${data.title}
                </div>
            </span>
        `;
    }
}