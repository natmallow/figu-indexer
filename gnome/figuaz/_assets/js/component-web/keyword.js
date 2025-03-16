const HighlightWordTemplate = document.createElement("template");
HighlightWordTemplate.innerHTML = `<style>
        span{
            background-color: yellow; 
            cursor: pointer;
            border: solid 1px red;
            color: black;
        }
        /* Styles for toggled state */
        .toggled {
          background-color: transparent; /* Hide background */
          border: none; /* Remove border */
          color: inherit;
          cursor: inherit;
        }
    </style>
    <span><slot></slot></span>`;

class HighlightWord extends HTMLElement {
  constructor() {
    super();
    const shadow = this.attachShadow({ mode: "open" });
    shadow.appendChild(HighlightWordTemplate.content.cloneNode(true));

    // Initial setup
    this.wordSpan = shadow.querySelector("span");
    const word = this.innerText.trim();
    this.wordSpan.setAttribute("data-word", word);
  }
  // Method to toggle the styles
  toggle() {
    this.wordSpan.classList.toggle("toggled");
  }
}

customElements.define("k-word", HighlightWord);
