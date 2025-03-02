const SpinnerTemplate = document.createElement('template');
SpinnerTemplate.innerHTML = `
    <link rel="stylesheet" type="text/css" href="/css/bootstrap/bootstrap.min.css">
    <style>
        .spinner-container {
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1000;
            background-color: rgb(60 200 200 / 15%);
            height: 100%;
            width: 100%;
            display: table-cell;
            vertical-align: middle;                                
        }
        .spinner-ctr {
            position: absolute;
            margin: auto;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: large;
            color: blue;
        }
    </style>
    <div class="text-center spinner-container" id="spinner">
        <div class="spinner-border spinner-ctr" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>`;


class SpinnerComponent extends HTMLElement {
    constructor() {
        // Always call super first in constructor
        super();
        const shadow = this.attachShadow({ mode: "open"});
        shadow.appendChild(SpinnerTemplate.content.cloneNode(true));
        // Element functionality written in here
    }

    connectedCallback() {
        this.innerHTML = `tedddt`;
    }
}


customElements.define("spinner-comp", SpinnerComponent);