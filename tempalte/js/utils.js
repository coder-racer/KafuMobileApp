function randomString(length) {
    return [...Array(length)].map(() => String.fromCharCode(Math.random() * 26 + (Math.random() > 0.5 ? 65 : 97))).join('')
}

function currentDate() {
    return new Intl.DateTimeFormat("ru", {
        day: "numeric",
        month: "long",
        year: "numeric",
        weekday: "short"
    }).format(new Date()).replace(/(\s?\г\.?)/, "");
}

function absoluteNumber(num) {
    return (num < 0) ? num * -1 : num;
}

function renderProgressBar() {
    const colors = {
        blue: ['#1969DB', '#7FC1EB'],
        green: ['#3d7d41', '#b3d492'],
        red: ['#de4f39', '#E6978A'],
        yellow: ['#F0A60A', '#EBD888']
    };

    document.querySelectorAll(".progress").forEach((el) => {
        if (el.dataset.on !== 'true') {
            el.id = randomString(5);
            el.classList.add(el.dataset.color);
            const [start, end] = colors[el.dataset.color] || colors.blue;
            el.dataset.on = 'true';
            let value = el.dataset.value;
            value = value + " ";
            const num = value.includes('.') ? 2 : 0;

            const semiBar = new ProgressBar.Circle('#' + el.id, {
                color: `url(#gradient_${el.id})`,
                strokeWidth: 15,
                trailWidth: 15,
                easing: 'easeInOut',
                lineCap: 'round',
                text: {autoStyleContainer: false},
                step: (state, circle) => {
                    const val = (circle.value() * 100).toFixed(num);
                    circle.setText((val == 0) ? 0 : val);
                    circle.path.setAttribute('stroke-linecap', 'round');
                }
            });

            semiBar.svg.insertAdjacentHTML('afterBegin', `
        <defs>
          <linearGradient id="gradient_${el.id}" x1="0%" y1="0%" x2="100%" y2="0%" gradientUnits="userSpaceOnUse">
            <stop offset="10%" stop-color="${end}"/>
            <stop offset="60%" stop-color="${start}"/>
          </linearGradient>
        </defs>`);

            let barPosition = el.dataset.value;
            semiBar.animate(barPosition / 100, {duration: 3000});
        }
    });
}

