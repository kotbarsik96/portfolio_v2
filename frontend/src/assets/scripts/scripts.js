export function onTransitionEnd(el, callback = function () { }) {
    return new Promise(resolve => {
        const elTransitionDuration = parseFloat(getComputedStyle(el).transitionDuration.replace(/[^.0-9]/g, ""));
        if (!elTransitionDuration)
            resolve();

        setTimeout(onTransEnd, elTransitionDuration * 1000);
        function onTransEnd() {
            el.removeEventListener("transitionend", onTransEnd);
            callback();
            resolve();
        }
    });
}

export function delay(timeout = 0) {
    return new Promise(resolve => {
        setTimeout(resolve, timeout);
    });
}

export function createElement(tagName, className = "", insertingHTML = "", attributes = {}) {
    let element = document.createElement(tagName);
    if (className) element.className = className;
    if (insertingHTML) element.insertAdjacentHTML("afterbegin", insertingHTML);
    if (attributes) {
        for (let key in attributes) {
            const value = attributes[key];
            element.setAttribute(key, value);
        }
    }
    return element;
}

export function doInFade(element, callback = function () { }) {
    return new Promise(async resolve => {
        element.style.transition = "all .2s";
        element.style.opacity = "0";
        await onTransitionEnd(element);

        try {
            callback();
        } catch (err) {
            resolve();
        }

        await onTransitionEnd(element);
        element.style.removeProperty("transition");

        resolve();
    });
}

export function getHeight(el, opts = {}) {
    let clone = el.cloneNode(true);
    clone.style.cssText = `
        width: ${opts.width || el.offsetWidth}px; 
        z-index: 100; 
        position: absolute; 
        top: 0; 
        left: 0;
        z-index: -999;
    `;
    document.body.append(clone);
    const height = clone.offsetHeight;
    clone.remove();
    clone = null;

    return height;
}

export function getCoords(el) {
    const box = el.getBoundingClientRect();
    return {
        top: box.top + window.pageYOffset,
        left: box.left + window.pageXOffset,
        right: box.right + window.pageXOffset,
        bottom: box.bottom + window.pageYOffset
    }
}

export function qsAll(selector, relative = document) {
    return Array.from(relative.querySelectorAll(selector));
}

export function formatSeconds(secondsValue, format = "ss:mm") {
    secondsValue = parseInt(secondsValue);
    if (!secondsValue && secondsValue !== 0)
        return;

    let seconds, minutes;
    switch (format) {
        case "ss:mm":
        default:
            minutes = parseInt(secondsValue / 60);
            seconds = secondsValue - minutes * 60;
            return `${startZero(minutes)}:${startZero(seconds)}`;
    }
}

export function startZero(num, length = 2) {
    if (parseInt(num) === NaN)
        return num;

    let newNum = num;
    const numLength = num.toString().length;
    if (numLength < length) {
        const diff = length - numLength;
        let zeros = "0";
        while (zeros.length < diff) {
            zeros += "0";
        }

        newNum = `${zeros}${num}`;
    }
    return newNum;
}

export async function setEditable(el) {
    el.setAttribute("contenteditable", true);

    await delay(0);

    el.focus();
    if (el.childNodes.length > 0) {
        const selection = window.getSelection();
        selection.removeAllRanges();
        const range = new Range();
        range.setStart(el, 1);
        range.setEnd(el, 1);
        selection.addRange(range);
    }
}

export function getTextContent(el) {
    return el.textContent.trim().replace(/\s{2,}/g, "");
}

export function getDataFromForms(form) {
    const data = {};
    qsAll("input[name]", form).forEach(input => {
        data[input.name] = input.value;
    });
    return data;
}

// ПОКА НЕ ИСПОЛЬЗОВАНЫ
function capitalizeFirstLetter(str) {
    return str.split("").map((substr, index) => index === 0 ? substr.toUpperCase() : substr).join("");
}

function camelCaseToKebab(str) {
    return str.split("").map((substr, index) => {
        if (index === 0) return substr;
        if (substr.match(/[A-Z]/)) return `-${substr.toLowerCase()}`;
        return substr;
    }).join("");
}

function trimLastPunctMark(str) {
    str = str.trim();
    if (str.match(/[.;,]$/)) {
        return str.slice(0, str.length - 1);
    }
    return str;
}

// получить длину/ширину элемента или потомка элемента по selector
function getSizes(el, params) {
    // params = { selector: "", setOriginalWidth: false/true, setOriginalHeight: false/true }
    const clone = el.cloneNode(true);
    clone.style.cssText = "position: absolute; left: -100vw; top: -100vh; max-width: unset; max-height: unset;";
    document.body.append(clone);
    let width = 0;
    let height = 0;
    if (params.setOriginalWidth) clone.style.width = `${el.offsetWidth}px`;
    if (params.setOriginalHeight) clone.style.height = `${el.offsetHeight}px`;

    if (params.selector) {
        const child = clone.querySelector(params.selector);
        child.style.cssText = "max-width: unset; max-height: unset;";
        width = child.offsetWidth;
        height = child.offsetHeight;
    } else {
        width = clone.offsetWidth;
        height = clone.offsetHeight;
    }
    clone.remove();

    return { width, height };
}

function getMaxHeight(node) {
    const clone = node.cloneNode(true);
    clone.style.removeProperty("max-height");
    const width = node.offsetWidth;
    clone.style.cssText = `position: absolute; top: 0; left: 0; transition: none; width: ${width}px;`;
    document.body.append(clone);
    const height = clone.offsetHeight;
    clone.remove();
    return height;
}

function setLoadingState(elOrArray, params = {}) {
    const timeout = parseInt(params.timeout) || 500;
    const timeoutSeconds = timeout / 1000;
    if (Array.isArray(elOrArray)) elOrArray.forEach(el => doSet(el));
    else doSet(elOrArray);

    function doSet(el) {
        el.classList.add("__loading-state");
        el.style.transition = `all ${timeoutSeconds}s`;
    }
}

function unsetLoadingState(elOrArray, params = {}) {
    const timeout = parseInt(params.timeout) || 500;

    if (Array.isArray(elOrArray)) elOrArray.forEach(el => doUnset(el));
    else doUnset(elOrArray);

    function doUnset(el) {
        el.classList.remove("__loading-state");
        setTimeout(() => {
            el.style.removeProperty("transition");
        }, timeout);
    }
}

function findClosest(relative, selector) {
    let closest = relative.querySelector(selector);
    let parent = relative.parentNode;
    while (!closest && parent !== document) {
        closest = parent.querySelector(selector);
        parent = parent.parentNode;
    }
    return closest;
}

function getScrollWidth() {
    const block = createElement("div");
    block.style.cssText = `position: absolute; top: -100vh, right: -100vw; z-index: -999; opacity: 0; width: 100px; height: 100px; overflow: scroll`;
    const blockInner = createElement("div");
    blockInner.style.cssText = `width: 100%; height: 200px`;
    block.append(blockInner);

    document.body.append(block);
    const width = block.offsetWidth - block.clientWidth;
    block.remove();
    return width;
}

class DateMethods {
    constructor() {
        this.months = ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"];
        this.monthsGenitive = ["Января", "Февраля", "Марта", "Апреля", "Мая", "Июня", "Июля", "Августа", "Сентября", "Октября", "Ноября", "Декабря"];
        this.zodiacSigns = [
            { name: "Водолей", start: { month: 1, day: 21 }, end: { month: 2, day: 19 }, iconName: "aquarius" },
            { name: "Рыбы", start: { month: 2, day: 20 }, end: { month: 3, day: 20 }, iconName: "pisces" },
            { name: "Овен", start: { month: 3, day: 21 }, end: { month: 4, day: 20 }, iconName: "aries" },
            { name: "Телец", start: { month: 4, day: 21 }, end: { month: 5, day: 21 }, iconName: "taurus" },
            { name: "Близнецы", start: { month: 5, day: 22 }, end: { month: 6, day: 21 }, iconName: "gemini" },
            { name: "Рак", start: { month: 6, day: 22 }, end: { month: 7, day: 22 }, iconName: "cancer" },
            { name: "Лев", start: { month: 7, day: 23 }, end: { month: 8, day: 21 }, iconName: "leo" },
            { name: "Дева", start: { month: 8, day: 22 }, end: { month: 9, day: 23 }, iconName: "virgo" },
            { name: "Весы", start: { month: 9, day: 24 }, end: { month: 10, day: 23 }, iconName: "libra" },
            { name: "Скорпион", start: { month: 10, day: 24 }, end: { month: 11, day: 22 }, iconName: "scorpio" },
            { name: "Стрелец", start: { month: 11, day: 23 }, end: { month: 12, day: 22 }, iconName: "sagittarius" },
            { name: "Козерог", start: { month: 12, day: 23 }, end: { month: 1, day: 20 }, iconName: "capicorn" },
        ];
    }
    getMaxMonthDay(month, year = null) {
        month = parseInt(month);
        year = parseInt(year) || null;
        if (!month) return 31;

        const highest = [1, 3, 5, 7, 8, 10, 12];
        if (highest.includes(month)) return 31;
        else if (month === 2) {
            const isLeap = year && year % 4 === 0;
            if (!year || isLeap) return 29;
            return 28;
        }

        return 30;
    }
    compare(dateStr1 = "01.01.2000", dateStr2 = "01.01.2000") {
        // format: dd.mm.yyyy
        const split1 = dateStr1.split(".");
        const split2 = dateStr2.split(".");
        const date1 = new Date(`${split1[2]}-${split1[1]}-${split1[0]}`);
        const date2 = new Date(`${split2[2]}-${split2[1]}-${split2[0]}`);

        const isEquals = date1.getFullYear() == date2.getFullYear()
            && date1.getMonth() === date2.getMonth()
            && date1.getDate() === date2.getDate();
        const isCorrectRange = date1 < date2 || isEquals;
        const hasIncorrectDate = !date1.getFullYear() || !date2.getFullYear();

        // isCorrectRange === true значит, что date1 меньше date2
        return { isEquals, isCorrectRange, hasIncorrectDate };
    }
    isInDateRange(dateStartStr, dateEndStr, dateBetweenStr = "") {
        // format: dd.mm.yyyy
        if (!dateStartStr) dateStartStr = "1900-01-01";
        if (!dateEndStr) dateEndStr = `${new Date().getFullYear()}-31-12`;
        const splitStart = dateStartStr.split(".");
        const splitEnd = dateEndStr.split(".");
        const splitBetween = dateBetweenStr.split(".");
        const dateStart = new Date(`${splitStart[2]}-${splitStart[1]}-${splitStart[0]}`);
        const dateEnd = new Date(`${splitEnd[2]}-${splitEnd[1]}-${splitEnd[0]}`);
        const dateBetween = new Date(`${splitBetween[2]}-${splitBetween[1]}-${splitBetween[0]}`);

        const hasIncorrectDate = !dateStart.getFullYear()
            || !dateEnd.getFullYear()
            || !dateBetween.getFullYear();
        const isInDateRange = dateStart <= dateBetween && dateBetween <= dateEnd;

        return { hasIncorrectDate, isInDateRange };
    }
}
const dateMethods = new DateMethods();

function cutText(text = "", maxlength = 50, params = { addEllipsis: false }) {
    if (!parseInt(maxlength)) return text;
    text = text.trim();

    if (text.length < maxlength) return text;
    let cut = text.slice(0, maxlength).trim();
    const match = cut.match(/.+\S(?=\s.+$)/);
    if (match && match[0]) cut = match[0];

    if (params.addEllipsis && cut.length < text.length)
        cut += "...";

    return cut;
}