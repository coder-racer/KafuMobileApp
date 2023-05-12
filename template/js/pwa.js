let showAddToHomeScreen = true;
let today = new Date().toISOString().slice(0, 10);

if (localStorage.getItem('lastAddToHomeScreenPrompt') === today) {
    showAddToHomeScreen = false;
}

window.addEventListener('beforeinstallprompt', function(e) {
    e.preventDefault();
    let addToHomeScreen = document.getElementById('add-to-home-screen');
    let addToHomeScreenBtn = document.getElementById('add-to-home-screen-btn');
    let skipAddToHomeScreenBtn = document.getElementById('skip-add-to-home-screen-btn');

    //if (showAddToHomeScreen) {
    if (true) {
        addToHomeScreen.classList.add("active");// = 'block';

        addToHomeScreenBtn.addEventListener('click', function() {
            e.prompt();
            e.userChoice.then(function(choiceResult) {
                addToHomeScreen.style.display = 'none';
                showAddToHomeScreen = false;
                localStorage.setItem('lastAddToHomeScreenPrompt', today);
            });
        });

        skipAddToHomeScreenBtn.addEventListener('click', function() {
            addToHomeScreen.classList.remove("active");
            showAddToHomeScreen = false;
            localStorage.setItem('lastAddToHomeScreenPrompt', today);
        });
    }
});