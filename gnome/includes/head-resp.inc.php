<!-- action response from SS Call-->
<?php
if (isset($_SESSION['actionResponse']) && $_SESSION['actionResponse'] != '') :
?>
    <div class="alert alert-warning alert-dismissible fade show response-ban" role="alert">
        <span>
            <i class="bi bi-exclamation-triangle me-1"></i>
            <?= $_SESSION['actionResponse']; ?>
        </span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" deluminate_imagetype="unknown"></button>
    </div>
<?php
endif;
$_SESSION['actionResponse'] = '';
?>
<!-- action response from SS Call End-->
<div id="sys-node-holder"></div>
<!-- Ajax response from SS Call-->
<div class="sys-notification alert alert-warning alert-dismissible fade show d-none" role="alert">
    <i class="bi bi-exclamation-triangle me-1"></i>
    <span class="sys-response"></span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" deluminate_imagetype="unknown"></button>
    <div class="counter-box"></div>
</div>
<!-- Ajax response from SS Call End-->

<script>

    countdown = (clone, seconds) => {
        // const element = clone.querySelector('.counter-box');
    
        if (seconds < 0) {
            clone.remove();
            return;
        }
    
        clone.style.opacity = (1+(seconds * 20))/100;

        // element.textContent = seconds;
    
        setTimeout(() => {
            countdown(clone, seconds - 1);
        }, 1000);
    }

    // could be global
    toggleGen = (fuct) => {
        fetch(fetch(fuct)
            .then((response) => response.json())
            .then((text) => {
                const node = select('.sys-notification.d-none');
                const clone = node.cloneNode(true);
                clone.querySelector('.sys-response').innerHTML = text.response;
                clone.classList.remove('d-none')
                select('#sys-node-holder').appendChild(clone);
            })
            .catch(error => {
                console.log(error)
            })
        );
    }

    /**
     * @msg required
     * @autoRemove ? true | false
     * @klass ? alert-primary | alert-secondary | alert-success 
     *       | alert-danger | alert-warning | alert-info
     *       | alert-light | alert-dark
     */
    toggleGenErr = (msg, autoRemove = false, klass = 'alert-success') => {

        const node = select('.sys-notification.d-none');
              node.classList.remove('alert-warning');
              node.classList.add(klass);

        const clone = node.cloneNode(true);
              clone.querySelector('.sys-response').innerHTML = msg;
              clone.classList.remove('d-none');

        select('#sys-node-holder').prepend(clone);

        if (autoRemove) {
           countdown(clone, 5);
        }
    }
</script>