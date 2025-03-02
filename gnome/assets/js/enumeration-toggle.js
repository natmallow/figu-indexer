/**
 * Enumeration this function toggles the enumerated segments on and off
 */
const toggleEnumeration = document.querySelector("#toggle-visability");
    const para = document.querySelectorAll(".eNum");

    toggleEnumeration.addEventListener("click", function(e) {
        document.querySelector("#toggle-visability-tag").innerHTML = e.target.checked ? " - On" : " - Off"
        para.forEach((element) => {
            element.classList.toggle("on")
        })
    });