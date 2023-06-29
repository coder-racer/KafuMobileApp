class platonus {
    #token = null;
    #JSESSIONID = null;
    userId = null;

    constructor() {
        if (localStorage.getItem('login')) {
            this.LogIn(localStorage.getItem('login'), localStorage.getItem('pass'));
        }
    }

    getListDocs(callBack) {
        this.getData('getListDocs', {}, (data) => {
            callBack(data)
        })
    }

    getData(url, data, callback = function () {
    }) {
        const formData = new FormData();
        Object.entries(data).forEach(([key, value]) => formData.append(key, value));
        formData.append('token', this.token);
        formData.append('JSESSIONID', this.JSESSIONID);
        formData.append('userId', this.userId);
        fetch('/api/' + url, {
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


    getProfile(callBack) {
        this.getData('getUserData', {}, (el) => {
            callBack(el);
        })
    }

    getGrade(callBack) {
        this.getData('getGrade', {
                'username': localStorage.getItem('login'),
                'password': localStorage.getItem('pass')
            }
            , (el) => {
                callBack(el);
            })
    }


    LogIn(login, pass) {
        const data = {
            login: login,
            pass: pass,
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

    getJournal(year, academic, callback = function () {
    }) {
        const data = {
            year: year,
            academic: academic,
        };

        this.getData('getJournal', data, response => {
            callback(response)
        });
    }

    getNews(callback) {
        fetch('/api/getNews').then(response => response.json())
            .then(data => {
                callback(data)
            })
            .catch((e) => {
                console.log(e);
                console.log("BAD KAFU");
            });
    }

    getNew(link, callback) {

        this.getData('getNew', {'link': link}, (data) => {
            callback(data)
        })
    }


}