class platonus {
    year = (new Date()).getFullYear() - 1;
    academic = ((new Date()).getMonth() > 5) ? 2 : 1;
    news = [];
    userData = null;
    journal = null;
    login = null;
    pass = null;
    token = null;
    JSESSIONID = null
    userId = null

    constructor() {
        // this.getNews();
        //this.createEvents();
      //  if (localStorage.getItem('login')) {
            this.LogIn(localStorage.getItem('login'), localStorage.getItem('pass'));
        //}
    }

    getData(url, data, callback = function () {}){
        const formData = new FormData();
        Object.entries(data).forEach(([key, value]) => formData.append(key, value));
        formData.append('token', this.token);
        formData.append('JSESSIONID', this.JSESSIONID);
        formData.append('userId', this.userId);
        fetch('/platonus/platonHandler.php?act=' + url, {
            method: 'POST',
            body: formData
        }).then(response => response.json())
            .then(data => {
                callback(data);
            })
            .catch(error => {
                console.error(error);
            });
    }

    getJournal(year, academic, callback = function(){})
    {
        const data = {
            year: year,
            academic: academic,
        };

        this.getData('getJournal', data, response => {
            callback(response)
        });
    }

    getNews(callback)
    {
        fetch('/platonus/platonHandler.php?act=getNews', {
            method: 'GET',
            headers: {'Content-Type': 'application/json; charset=UTF-8'}
        }).then(response => response.json())
            .then(data => {
                callback(this.parseNews(data))
            })
            .catch(() => {
                console.log("Sdfsdf");
            });
    }

    LogIn(login, pass) {
        const data = {
            login: 'Васипёнок_Владислав',
            pass: '7JuzkC3d',
        };

        this.getData('login', data, response => {
            if (response.res === true) {
                localStorage.setItem('login', login);
                localStorage.setItem('pass', pass);
                document.querySelector('.login_modal').classList.remove('show');
                this.token = response.data.token;
                this.JSESSIONID = response.data.JSESSIONID;
                this.userId = response.data.userId;
                window.dispatchEvent(authEvent);
            } else {
                setTimeout(() => swal("Ошибка!", response.data, "error"), 200);
            }
        });
    }


    createEvents() {
        const yearElem = document.querySelector("#select_year");
        const academicElem = document.querySelector("#select_academic");

        yearElem.innerHTML = Array.from({length: 7}, (_, i) => this.year - 3 + i)
            .map(y => `<option${y === this.year ? ' selected' : ''}>${y}</option>`).join('');
        academicElem.innerHTML = `<option${this.academic === 1 ? ' selected' : ''}>1</option><option${this.academic === 2 ? ' selected' : ''}>2</option>`;

        academicElem.addEventListener('change', () => this.updateAuth(academicElem, 'academic'));
        yearElem.addEventListener('change', () => this.updateAuth(yearElem, 'year'));
    }

    updateAuth(elem, prop) {
        let old = this[prop];
        this[prop] = elem.value;
        if (old !== this[prop]) this.auth(localStorage.getItem('login'), localStorage.getItem('pass'));
    }

    renderProfile() {
        const {
            photoBase64,
            groupName,
            professionName,
            courseNumber,
            gpa,
            lastName,
            firstName,
            patronymic
        } = this.userData;
        document.querySelector("#section_profile .profile_block .left img").src = `data:image/png;base64, ${photoBase64}`;

        const html = ['Группа', 'Профессия', 'Курс', 'GPA'].map((name, i) => {
            const value = [groupName, professionName, courseNumber, gpa][i];
            return this.elementProfile(name, value);
        }).join('');

        document.querySelector("#section_profile .profile_block .right .item_list").innerHTML = html;
        document.querySelector(".fio").innerHTML = `${lastName} ${firstName} ${patronymic}`;
    }

    auth(login, pass) {
        const data =
            {login: 'Васипёнок_Владислав', pass: '7JuzkC3d', year: this.year, academic: this.academic};
        const dataString = Object.entries(data).map(([key, value]) => `${key}=${value}`).join('&');

        fetch(`/platonus/platonHandler.php?act=login&${dataString}`, {
            method: 'GET',
            headers: {'Content-Type': 'application/json; charset=UTF-8'}
        }).then(response => response.json())
            .then(data => this.handleAuthResponse(data, login, pass))
            .catch(() => {
            });
    }

    sgetNews() {
        fetch('/platonus/platonHandler.php?act=getNews', {
            method: 'GET',
            headers: {'Content-Type': 'application/json; charset=UTF-8'}
        }).then(response => response.json())
            .then(data => {
                this.parseNews(data.data['news']);
                this.renderNews();
            })
            .catch(() => {
            });
    }

    handleAuthResponse(data, login, pass, func) {
        if (data.res === true) {
            localStorage.setItem('login', login);
            localStorage.setItem('pass', pass);
            document.querySelector('.login_modal').classList.remove('show');

            this.renderJournal(data['data']['journal']);
            this.userData = data['data']['user'];
            console.log(data);
            this.renderProfile();
            document.querySelector('.login_modal').classList.remove('show');
        } else {
            setTimeout(() => swal("Ошибка!", data['data'], "error"), 200);
        }
    }

    renderJournal(data) {
        const html = data.map((item, i) => this.courseItem(item, i % 4)).join('');
        document.querySelector('.course_list').innerHTML = html;
        renderProgressBar();
    }

    renderNews() {
        const html = this.news.map(el => this.elementNew(el)).join('');
        document.querySelector('.news_list').innerHTML = html;
    }

    parseNews(newsText) {
        const htmlDom = new DOMParser().parseFromString(newsText, 'text/html');
        const listNews = htmlDom.querySelectorAll('.article-content.posts-list a');

       return Array.from(listNews).map(el => ({
            title: el.querySelector("h2").innerText,
            url: el.href,
            img: el.querySelector("img").dataset.src,
            text: el.querySelector(".desc").innerText
        }));
    }

    renderNum(num) {
        const nums = num.split('.');
        return nums.length < 2 || nums[1] > 0 ? num : nums[0];
    }

    kitcut(text, limit) {
        text = text.trim();
        return text.length <= limit ? text : `${text.slice(0, limit).trim()}...`;
    }

    courseItem(data, num) {
        const colors = ['blue', 'yellow', 'green', 'red'];
        let color = colors[num % 4];

        if (data['centerMark'] > 49) color = 'yellow';
        if (data['centerMark'] > 70) color = 'blue';
        if (data['centerMark'] > 80) color = 'green';

        const exams = data.exams
            .filter(el => typeof el === 'object' && !Array.isArray(el) && el !== null)
            .map(el => this.examsItem(el));

        return `
        <div class="course_item">
            <div class="progress" data-value="${this.renderNum(data['centerMark'])}" data-color="${color}"></div>
            <div class="course_text">
                <div class="name" title="${data['subjectName']}">${this.kitcut(data['subjectName'], 50)}</div>
                <div class="hover_name" title="${data['subjectName']}">${data['subjectName']}</div>
                <div class="teacher">${data['tutorList']}</div>
            </div>
            <div class="data_list">
                ${exams.join('<div class="line"></div>')}
            </div>
        </div>
    `;
    }


    elementNew(data) {
        return `
            <a href="${data.url}" class="new_item">
                <img src="${data.img}" alt="">
                <div class="name">
                    ${data.title}
                </div>
            </a>
        `;
    }

    elementProfile(name, value) {
        return `
            <div class="profile_item">
                <span class="name">${name}</span>
                <span class="value">${value}</span>
            </div>
        `
    }

    examsItem(data) {
        return `
             <div class="boundary_one">
                <div class="data_value">${this.renderNum(data['mark'])}</div>
                <div class="data_label">${data['name']}</div>
            </div>
        `
    }

}