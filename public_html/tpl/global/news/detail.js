App.templates['global/news/detail'] = () => {
    App.setContent(`
        <section id="section_new">
                <!-- Спиннер загрузки -->
                <div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
        </section>
    `);

    platon.getNew(App.getQueryParam('url'),(res)=>{
        loadImages(res.images, ()=>{
            document.querySelector('.lds-roller').remove()
            App.bind("#section_new", res.data);
        })
    })

    function loadImages(images, callback) {
        if (!images.length)
            callback()
        let loadedCount = 0;
        let totalImages = images.length;

        function imageLoaded() {
            loadedCount++;

            if (loadedCount === totalImages) {
                callback();
            }
        }

        for (let i = 0; i < totalImages; i++) {
            let image = new Image();
            image.onload = imageLoaded;
            image.onerror = imageLoaded;
            image.src = images[i];
        }
    }

}
