<script type="text/javascript">

callAgain = () => {
    // This is the id of the form

    document.getElementById('scrape-form').addEventListener('submit', function(e) {
        const form = e.target;
        if (form.id !== 'scrape-form') return;

        e.preventDefault(); // avoid to execute the actual submit of the form.
        toggleSubmitButtons();

        const url = form.action;
        const formData = new FormData(form);

        postFormData(url, formData);
    });

    document.getElementById('paste-back-btn').addEventListener('click', function() {
        processResponseData();
    });            
 } 



function toggleSubmitButtons() {
    const submitBtnLoad = document.getElementById('submit-btn-load');
    const submitBtn = document.getElementById('submit-btn');
    submitBtnLoad.classList.toggle('d-none');
    submitBtn.classList.toggle('d-none');
}

async function postFormData(url, formData) {
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData,
        });
        const data = await response.text();
        toggleSubmitButtons();
        document.getElementById('scraperResponse').innerHTML = data;
        
    } catch (error) {
        console.error('Error:', error);
        toggleSubmitButtons(); // Ensure buttons are toggled back even on error
    }
}

function processResponseData() {
    const responseData = Array.from(document.querySelectorAll('.toEnumerate > tbody > tr'));
    const newTable = document.createElement('table');
    responseData.forEach(item => {
        newTable.appendChild(item);
    });

    if (responseData.length > 0) {
        const newValue = newTable.outerHTML;
        // const editor = document.getElementById('raw_html'); // Assuming editor is an id, adjust if necessary
        editor.value = newValue;

        const sUrl = document.getElementById('url').value;
        document.getElementById('publication_source').value = sUrl;
        const modalEl = document.getElementById('importModal');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        modalInstance.hide();
    } else {
        alert('Nothing was selected');
    }
}
</script>
<style> 
.toEnumerate {
    background-color: rgb(255, 255, 102);
}
</style>
<script>
var currentTagets = [];
var currentTarget = null;
var currentType = null;
var formatType = 'FOMK';

var safe = ["body", "html", "hr", "br"];
var bubble = ["tbody", "tfoot", "thead", "tr", "td"];

 sectionSelectHandler = e => {

    if (safe.indexOf(e.target.tagName.toLowerCase()) != -1) {
        return;
    }

    // bubble to parent
    if (bubble.indexOf(e.target.tagName.toLowerCase()) != -1) {
        let temp = e.target;
        do {
            temp = temp.parentNode;
        }
        while (bubble.indexOf(temp.tagName.toLowerCase()) != -1)
        // currentTarget = temp;
        //currentType = temp.tagName.toLowerCase()

        if (temp.classList.contains('toEnumerate')) {
            temp.classList.remove('toEnumerate');
        } else {
            temp.classList.add('toEnumerate');
        }

        console.log(e.target.tagName);
        return;
    }


    // e.target.classList.add('toEnumerate');
    if (e.target.classList.contains('toEnumerate')) {
        e.target.classList.remove('toEnumerate');
    } else {
        e.target.classList.add('toEnumerate');
    }
    //  currentTarget = e.target;
    currentType = e.target.tagName.toLowerCase()
    // console.log(e.target);  // to get the element
    // console.log(e.target.tagName); // to get the element tag name alone
    return;


}


 selectionPromise = e => {
    let cleanup = function cleanUp() {
        return new Promise((resolve, reject) => {
            // let isMultiOn = document.querySelector('#multiSelect').checked;
            // if (!isMultiOn) {
            //     //  console.log('U are in')
            //     const eClass = document.querySelectorAll('.toEnumerate');
            //     // select all the tr shadow dom
            //     eClass.forEach(function(ele) {
            //         ele.classList.remove('toEnumerate');
            //     })
            // }
             resolve("Resolved");
        })
    }

    cleanup().then(() => {
        sectionSelectHandler(e);
    }).catch((error) => {
        console.log(`Handling error as we received ${error}`);
    });

}

initScraper = () => {
 areaBody = document.querySelector('#scraperResponse');
 areaBody.removeEventListener('click', selectionPromise );
 areaBody.addEventListener('click', selectionPromise, false );
}


</script>