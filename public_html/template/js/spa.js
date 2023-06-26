class SPA {
    host = 'http://localhost:18650'
    #fullBlock = null
    #body = null
    #eventList = {}
    #tplFolder = 'tpl'
    #mainLocation = 'global/journal'
    #errorLocation = '404'
    #currentLocation = null
    templates = {}
    #globalBlocks = {}
    #globalEvents = {}
    #param = []
    #loadEvent = []
    animationPosition = 0

    setAnimationPosition(current, next) {
        let currentIndex = 0
        let nextIndex = 0
        document.querySelectorAll(".nav_bg span").forEach((el, index) => {
            if (current == el.dataset.link) {
                currentIndex = index;
            }
            if (next == el.dataset.link) {
                nextIndex = index;
            }
        })
        if (currentIndex > nextIndex)
            this.animationPosition = 0
        else
            this.animationPosition = 1
    }

    #locationEvent = []
    #blockList = {
        'body': null, 'additional': null
    }

    addLocationEvent(callBack) {
        this.#locationEvent.push(callBack)
    }
    addLoadEvent(callBack) {
        this.#loadEvent.push(callBack)
    }

    pushLocationEvent() {
        this.#locationEvent.forEach(el => el(this.#currentLocation))
    }
    pushLoadEvent() {
        this.#loadEvent.forEach(el => el())
    }

    getMainTpl() {
        return this.#mainLocation
    }

    getLocation() {
        return this.#currentLocation;
    }

    setMainTpl(tpl) {
        this.#mainLocation = tpl
    }

    #callBackContentUpdate = () => {
    }

    constructor(fullBlock, body) {
        if (/complete|interactive|loaded/.test(document.readyState)) {
            window.auth ? this.run(fullBlock, body) : window.addEventListener('auth', () => this.run(fullBlock, body));
        } else {
            document.addEventListener('DOMContentLoaded', () => window.auth ? this.run(fullBlock, body) : window.addEventListener('auth', () => this.run(fullBlock, body)));
        }
    }


    setCallBackContentUpdate(func) {
        this.#callBackContentUpdate = func;
    }

    getParam() {
        return this.#param
    }

    getBlock(block) {
        return this.#body.querySelector(block)
    }


    bind(block, content) {
        if (!this.#fullBlock.querySelector(block)) return null

        this.#fullBlock.querySelector(block).innerHTML = content
    }

    locationReload() {
        this.templates[this.#currentLocation]()
    }

    run(fullBlock, body) {
        this.#fullBlock = document.querySelector(fullBlock)
        this.#blockList.body = this.#fullBlock.querySelector(body)
        this.#body = this.#fullBlock.querySelector(body)
        this.setHistoryEvent()
        this.location(null, true, false)
        this.pushLoadEvent();
    }

    setContent(content) {
        this.#body.innerHTML = content
        this.#callBackContentUpdate()
    }

    clearContent() {
        this.setContent('')
    }

    parseHistory(url = null) {
        let mabeUrl = location.href.replace(location.origin, '')
        let fullUrl = (url == null) ? mabeUrl : url

        if (fullUrl[0] === '/' || fullUrl[0] === '\\') fullUrl = fullUrl.substring(1)

        let listUrl = fullUrl.split('#')

        let baseUrl = listUrl[0]

        listUrl.shift()

        this.#param = listUrl
        return {
            'base': baseUrl, 'url': fullUrl
        }
    }

    locationBack() {
        window.history.back()
    }

    locationForward() {
        window.history.forward()
    }

    appendGlobalBlock(name, block) {
        if (!this.#fullBlock.querySelector(block)) return null;
        this.#globalBlocks[name] = (this.#fullBlock.querySelector(block))
    }

    setGBlock(name, content) {
        if (this.#globalBlocks[name]) this.#globalBlocks[name].innerHTML = content;
    }

    setHistoryEvent() {
        window.addEventListener('popstate', (e) => {
            // if (e?.state?.url)
            this.location(e.state.url, false)
        }, false)
    }

    location(url = null, historyPush = true, animation = true) {
        let historyList = this.parseHistory(url)
        url = historyList.base
        if (this.#currentLocation === url && url != 'customcert') return;

        if (historyPush) history.pushState({url: historyList.url}, '', '/' + historyList.url)
        if (!url.length) url = this.#mainLocation
        this.setAnimationPosition(this.#currentLocation, url)
        let test = this.animationPosition
        if (animation) {
            this.hideContent(() => {
                this.loadLocation(url, animation)
            }, test)
        } else {
            this.loadLocation(url, animation)
        }


    }

    loadLocation(url, animation) {
        this.clearAllEvents()
        this.clearContent()
        this.setAnimationPosition(this.#currentLocation, url)
        let test = this.animationPosition
        this.#currentLocation = url
        this.pushLocationEvent()
        if (!this.templates.hasOwnProperty(url)) {
            let elem = document.createElement('script')
            elem.src = '/' + this.#tplFolder + '/' + url + '.js?v=' + Math.random()

            document.body.appendChild(elem)
            elem.onerror = () => {
                this.location(this.#errorLocation)
            }
            elem.onload = () => {

                this.templates[url]()
                if (animation)
                    this.showContent(() => {
                    }, test)
            }
        } else {
            this.templates[url]()
            if (animation)
                this.showContent(() => {
                }, test)
        }
    }

    hideContent(func = () => {
    },pos) {
        if (pos === 1) {
            let to = ((document.body.offsetWidth / 2) + (this.#body.offsetWidth)) * -1;
            App.animate(
                [
                    {
                        from: 0,
                        to: 100,
                        func: (x) => {
                            this.#body.style.filter = `grayscale(${Math.round(x)}%)`
                        },
                        delta: x => x
                    },
                    {
                        from: 0,
                        to: to,
                        func: (x) => {
                            // this.#body.style.marginLeft = `${x}px`
                            this.#body.style.transform = `translateX(${x}px)`
                        },
                        delta: x => x
                    },
                    {
                        from: 1,
                        to: 0,
                        func: (x) => {
                            this.#body.style.opacity = x
                        }
                    }
                ]
                , 300
                , () => {
                    func()
                }
            )
        } else {
            let to = ((document.body.offsetWidth / 2) + (this.#body.offsetWidth)) * -1;
            to *= -1;
            App.animate(
                [
                    {
                        from: 0,
                        to: 100,
                        func: (x) => {
                            this.#body.style.filter = `grayscale(${Math.round(x)}%)`
                        },
                        delta: x => x
                    },
                    {
                        from: 0,
                        to: to,
                        func: (x) => {
                            // this.#body.style.marginLeft = `${x}px`
                            this.#body.style.transform = `translateX(${x}px)`
                        },
                        delta: x => x
                    },
                    {
                        from: 1,
                        to: 0,
                        func: (x) => {
                            this.#body.style.opacity = x
                        }
                    }
                ]
                , 300
                , () => {
                    func()
                }
            )
        }
    }

    showContent(func = () => {
    }, pos) {
        if (pos === 1) {
            let from = (document.body.offsetWidth / 2) + (this.#body.offsetWidth / 2);
            App.animate(
                [
                    {
                        from: 100,
                        to: 0,
                        func: (x) => {
                            this.#body.style.filter = `grayscale(${Math.round(x)}%)`
                        },
                        delta: x => x
                    },
                    {
                        from: from,
                        to: 0,
                        func: (x) => {
                            //  this.#body.style.marginLeft = `${x}px`
                            this.#body.style.transform = `translateX(${x}px)`
                        },
                        delta: x => x
                    },
                    {
                        func: (x) => {
                            this.#body.style.opacity = x
                        }
                    }
                ]
                , 300
                , () => {
                    func()
                }
            )
        } else {
            let from = (document.body.offsetWidth / 2) + (this.#body.offsetWidth / 2);
            from *= -1


            App.animate(
                [
                    {
                        from: 100,
                        to: 0,
                        func: (x) => {
                            this.#body.style.filter = `grayscale(${Math.round(x)}%)`
                        },
                        delta: x => x
                    },
                    {
                        from: from,
                        to: 0,
                        func: (x) => {
                            //  this.#body.style.marginLeft = `${x}px`
                            this.#body.style.transform = `translateX(${x}px)`
                        },
                        delta: x => x
                    },
                    {
                        func: (x) => {
                            this.#body.style.opacity = x
                        }
                    }
                ]
                , 300
                , () => {
                    func()
                }
            )
        }
    }

    animate(option = [], duration = 500, callBack = null) {
        let timeStart = performance.now();
        window.requestAnimationFrame(function step(timestamp) {
            let progress = (timestamp - timeStart) / duration;

            option.forEach((item) => {
                if (item.func && typeof item.func == 'function') {
                    let from = item?.from ?? 0
                    let to = item?.to ?? 1

                    if (!item.delta || typeof item.delta != 'function') {
                        item.delta = (x) => x;
                    }

                    item.func((to - from) * item.delta(progress) + from)
                }
            })

            if (progress < 1)
                window.requestAnimationFrame(step)
            else {
                option.forEach((item) => {
                    if (typeof item.func == 'function') {
                        item.func(item?.to ?? 1)
                    }
                })
                if (typeof callBack == 'function')
                    callBack()
            }
        })
    }

    getContainer() {
        return this.#body;
    }

    on(eventName, block, func, global = false) {
        if (!global && !this.#eventList[eventName]) {
            this.#eventList[eventName] = {
                'func': eventPush, 'list': {}
            }
            this.#fullBlock.addEventListener(eventName, eventPush, false)
        } else if (global && !this.#globalEvents[eventName]) {
            this.#globalEvents[eventName] = {
                'func': eventPushGlobal, 'list': {}
            }
            this.#fullBlock.addEventListener(eventName, eventPushGlobal, false)
        }

        if (!global && !this.#eventList[eventName]['list'][block]) {
            this.#eventList[eventName]['list'][block] = func
        } else if (global && !this.#globalEvents[eventName]['list'][block]) {
            this.#globalEvents[eventName]['list'][block] = func
        }

        let thisClass = this

        function eventPush(event) {
            let elem = []
            if (thisClass.#eventList[event.type]?.['list']) {
                Object.entries(thisClass.#eventList[event.type]?.['list']).forEach(entry => {
                    const [element, funcElem] = entry
                    if (!elem.includes(element)) {
                        elem.push(element)
                        push(element, event.target, funcElem, event)
                    }
                })
            }
        }

        function eventPushGlobal(event) {
            if (thisClass.#globalEvents[event.type]?.['list']) {
                Object.entries(thisClass.#globalEvents[event.type]?.['list']).forEach(entry => {
                    const [element, funcElem] = entry
                    push(element, event.target, funcElem, event)
                })
            }
        }

        function push(block, target, func, event) {
            if (Array.prototype.indexOf.call(document.querySelectorAll(block), target) !== -1) {
                event.preventDefault()
                func(target, event)
                return;
            }

            let target_next = target?.parentElement
            if (target_next) push(block, target_next, func, event)
        }

    }

    clearAllEvents() {
        Object.entries(this.#eventList).forEach(entry => {
            const [eventName, value] = entry
            let func = value['func']
            this.#fullBlock.removeEventListener(eventName, func, false)
        })
        this.#eventList = {}
    }

}

window.App = new SPA('body', '.container_app')