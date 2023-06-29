App.templates['global/journal'] = () => {
    App.setContent(`
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
    `);

    const year = new Date().getFullYear() - 1;
    const academic = new Date().getMonth() > 5 ? 2 : 1;

    const update = () => {
        App.getBlock(".course_list").classList.remove('active')
        const yearValue = App.getBlock("#select_year").value;
        const academicValue = App.getBlock("#select_academic").value;
        platon.getJournal(yearValue, academicValue, data => {
            const html = data.map((item, i) => courseItem(item, i % 4)).join('');
            App.bind(".course_list", html);
            App.getBlock(".course_list").classList.remove('mb-none')
            if (!data.length)
            {
                App.getBlock(".course_list").classList.add('mb-none')
                App.bind(".course_list", `
                    <div class="page-404">
                    <img src="/template/img/404.png"> 
                    </div>
                `);
            }

            App.getBlock(".course_list").classList.add('active')
            renderProgressBar();
        });
    };

    App.on('change', "#select_year", update);
    App.on('change', "#select_academic", update);

    App.bind("#select_year", Array.from({length: 7}, (_, i) => year - 3 + i)
        .map(y => `<option${y === year ? ' selected' : ''}>${y}</option>`).join(''));

    App.bind("#select_academic", `<option${academic === 1 ? ' selected' : ''}>1</option><option${academic === 2 ? ' selected' : ''}>2</option>`);

    update();

    function courseItem(data, num) {
        const colors = ['blue', 'yellow', 'green', 'red'];
        let color = colors[num % 4];
        if (data.centerMark > 80) color = 'green';
        else if (data.centerMark > 69) color = 'blue';
        else if (data.centerMark > 49) color = 'yellow';
        else color = 'red';
        const exams = data.exams.filter(el => el instanceof Object && !Array.isArray(el) && el !== null).map(el => examsItem(el));
        return `
            <div class="course_item">
              <div class="progress" data-value="${renderNum(data.centerMark)}" data-color="${color}"></div>
              <div class="course_text">
                <div class="name" title="${data.subjectName}">${kitCut(data.subjectName, 50)}</div>
                <div class="hover_name" title="${data.subjectName}">${data.subjectName}</div>
                <div class="teacher">${data.tutorList}</div>
              </div>
              <div class="data_list">
                ${exams.join('<div class="line"></div>')}
              </div>
            </div>
        `;
    }

    const examsItem = data => `
      <div class="boundary_one">
        <div class="data_value">${renderNum(data.mark)}</div>
        <div class="data_label">${data.name}</div>
      </div>
    `;

    const renderNum = num => {
        const nums = num.split('.');
        return nums.length < 2 || nums[1] > 0 ? num : nums[0];
    };


}