document.querySelector(".date").innerHTML = currentDate();

setTimeout(() => {
    document.querySelector('.preloader').classList.remove('show');
    // document.querySelectorAll(".nav_item").forEach(el => {
    //     el.addEventListener('click', () => {
    //         clearMenu();
    //         let id = el.id;
    //         // document.querySelectorAll(`.nav_item#${id}`).forEach(el => {
    //         //     let icon = document.querySelector("header .header_left i");
    //         //     icon.classList.forEach(className => {
    //         //         icon.classList.remove(className);
    //         //     });
    //         //     if (el.querySelector('i')) el.querySelector('i').classList.forEach(el => {
    //         //         icon.classList.add(el);
    //         //     });
    //         //     if (el.querySelector('span'))
    //         //         document.querySelector("header .header_right .name").innerHTML = el.querySelector('span').innerHTML;
    //         //     el.classList.add('active');
    //         // });
    //     });
    // });
}, 1200);

function clearMenu() {
    document.querySelectorAll(".nav_item").forEach(el => {
        el.classList.remove('active');
    });
}

const authEvent = new Event('auth');
const platon = new platonus();


function login(event) {
    event.preventDefault();
    let login = "Васипёнок_Владислав"// document.querySelector('#login').value;
    let pass = "7JuzkC3d"//document.querySelector('#pass').value;
    platon.LogIn(login, pass)
}

function logOut() {
    localStorage.clear();
    location.reload()
}

document.querySelector('footer').innerHTML = document.querySelector('.nav_bg').innerHTML;

window.addEventListener("message", function (event) {
    if (event.data == 'slide:start') {
        location.reload();
    }
}, false);

window.App = new SPA('body', '.container_app')

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(()=> {
        App.on("click", ".nav_item", (nav)=> {
            document.querySelectorAll(".nav_item").forEach(el => {
                el.classList.remove('active');
            });
            nav.classList.add("active")
            App.location(nav.dataset.link)
        }, true)
    }, 500)

});