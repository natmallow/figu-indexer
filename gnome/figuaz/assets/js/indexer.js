class DecSpan extends HTMLSpanElement {
    constructor() {
        // Always call super first in constructor
        super();
        // Element functionality written in here
    }
}
class TrkSpan extends HTMLSpanElement {
    constructor() {
        // Always call super first in constructor
        super();
        // Element functionality written in here
    }
}

customElements.define("decision-span", DecSpan, { extends: "span" });
customElements.define("track-span", TrkSpan, { extends: "span" });