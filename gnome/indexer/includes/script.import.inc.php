<script>
    const initModal = (
        _url,
        modalNameId,
        openBtnId,
        closeBtnId,
        modalDataContainerId,
        handlers = []
    ) => {
        const modalElement = document.getElementById(modalNameId);
        const Modal = new bootstrap.Modal(modalElement, {
            keyboard: true
        });

        const modalData = document.getElementById(modalDataContainerId);
        const openBtn = document.getElementById(openBtnId);
        const closeBtn = document.getElementById(closeBtnId);

        const loadContent = async () => {
            try {
                const response = await fetch(_url, {
                    cache: 'no-cache'
                });
                const text = await response.text();

                const parser = new DOMParser();
                const parsedDocument = parser.parseFromString(text, 'text/html');
                // Insert the HTML content
                modalData.innerHTML = '';
                modalData.appendChild(parsedDocument.body);

                // Now handle the script tags
                const scripts = parsedDocument.getElementsByTagName('script');

                for (const script of scripts) {
                    const newScript = document.createElement('script');
                    if (script.src) {
                        newScript.src = script.src;
                    } else {
                        newScript.textContent = script.textContent;
                    }
                    // This will execute the script tags
                    document.head.appendChild(newScript).parentNode.removeChild(newScript);
                }

                Modal.show();
            } catch (error) {
                console.error('Error:', error);
            }
        };

        modalElement.addEventListener('shown.bs.modal', () => {
            // Execute script tags or handlers once the modal is shown.
            handlers.forEach((handlerName) => {
                if (typeof window[handlerName] === 'function') {
                    window[handlerName]();
                } else {
                    console.warn(`Handler ${handlerName} is not a function or is not defined.`);
                }
            });
        });

        const assignEventListeners = () => {
            openBtn.addEventListener('click', loadContent);

            // optional chaining added 
            closeBtn?.addEventListener('click', () => {
                Modal.hide();
            });

            window.addEventListener('click', event => {
                if (event.target === modalElement) {
                    Modal.hide();
                }
            });
        };

        assignEventListeners();
    };

    // Usage
    // Call this function with appropriate arguments to initialize the modal
    // initModal('your-url-here', 'modalId', 'openBtnId', 'closeBtnId', 'modalDataContainerId');
</script>