App.templates['global/grades'] = () => {
    App.setContent(`
        <section id="section_news">
            <div class="news_list grade_block_list">
                 <div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
            </div>
        </section>
    `);

    platon.getGrade((data) => {

        let html = ``
        data.forEach(el =>{
            let items = ``

            el.data.forEach(task => items += itemScope(task))

            html += itemGrade(el, items)
        })
        document.querySelector('.lds-roller').remove()
        App.bind('.news_list', html)
        document.querySelectorAll(".grade_item").forEach((item, index) => {
            setTimeout(() => {
                item.classList.remove('disable')
            }, (200 * index) + 20);
        });
        renderProgressBar();
    })

    function itemGrade(data, items) {
        let img = ``
        if (data.course_image)
            img = `<img src="${data.course_image}" alt="">`
        return `
            <span class="new_item grade_item disable">
                ${img}
                <div class="info">
                    <div class="desc">
                         <div class="name">
                            ${data.course_name}
                        </div>
                        <div class="description">
                            ${data.course_description}
                        </div>
                    </div>
                    <div class="scope">
                        <div class="progress" data-value="${data.scope}" data-color="green"></div>
                    </div>
                </div>
                <div class="details buttons">
                    <div class="button">Подробнее</div>
                    <div class="grade_list">
                       ${items}
                    </div>
                </div>
               
            </span>
        `
    }

    function itemScope(data)
    {
        return `
             <div class="grade_item_scope">
                <div class="description">
                    ${data.task_name}
                </div>
                <div class="scope">
                    <div class="progress" data-value="${data.scope}" data-color="green"></div>
                </div>                                
            </div>
        `
    }
}
