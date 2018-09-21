(function () {
    var win = window;
    var doc = win.document;

    function elById(id) {
        return doc.getElementById(id);
    }

    function onEvent(el, type, fn) {
        el.addEventListener(type, fn);
    }

    function onReady(fn) {
        onEvent(doc, 'DOMContentLoaded', fn);
    }

    function forEach(list, fn) {
        Array.prototype.forEach.call(list, fn);
    }

    function updateQrCode() {
        var options = {
            render: image,
            crisp: false,
            ecLevel: 'H',
            minVersion: 1,

            fill: '#333',
            text: '1637775f566ef1fbff997b0c6083361b', //22-05-2018-1-loyaltee-sufix
            size: 400,
            rounded: 100,
            quiet: 2,

            mode: 'label',

            mSize: 30,
            mPosX: 50,
            mPosY: 50,

            label: '',
            fontname: 'sans',
            fontcolor: '#ff0000',


        };

        var container = elById('container');
        var qrcode = kjua(options);
        forEach(container.childNodes, function (child) {
            container.removeChild(child);
        });
        if (qrcode) {
            container.appendChild(qrcode);
        }
    }

    function update() {
        updateQrCode();
    }

    onReady(function () {
        onEvent(win, 'load', update);
        // update();
    });
}());
