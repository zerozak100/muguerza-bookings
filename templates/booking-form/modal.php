<?php

/** @var MG_Booking_Form */
$form = $form;

?>

<style>
    #booking-modal .modal__container {
        max-width: 1200px;
        width: 100%;
    }

    .booking-form {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .booking-form__field {
        margin-bottom: 10px;
    }

    .booking-form__field.required span {
        color: red;
    }

    #booking-modal form {
        display: inline;
    }

    /* .booking-form__field--name {
		grid-column: 1 / 3;
	} */
</style>
<div class="modal micromodal-slide" id="booking-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        <form method="POST" class="modal__container" role="dialog" aria-modal="true" aria-labelledby="booking-modal-title">
            <header class="modal__header">
                <h2 class="modal__title" id="booking-modal-title">
                    Agendar
                </h2>
                <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content" id="booking-modal-content">
                <?php $form->getCalendar()->display(); ?>
                <?php $form->showFields(); ?>
            </main>
            <footer class="modal__footer">
                <button class="modal__btn modal__btn-primary" type="submit" name="mgb-booking-save" value="1">Añadir al carrito</button>
                <input type="hidden" name="product_id" value="<?php echo $product->get_id(); ?>">
                <input type="hidden" name="datetime" value="2023-03-02"> <!-- TODO -->
                <button class="modal__btn" data-micromodal-close aria-label="Close this dialog window">Cerrar</button>
            </footer>
        </form>
    </div>
</div>
<!-- <script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script> -->
<script>
	jQuery(document).ready(function($) {
		MicroModal.init({
			onShow: modal => console.info(`${modal.id} is shown`), // [1]
			onClose: modal => console.info(`${modal.id} is hidden`), // [2]
			// openTrigger: 'data-custom-open', // [3]
			// closeTrigger: 'data-custom-close', // [4]
			// openClass: 'is-open', // [5]
			disableScroll: true, // [6]
			disableFocus: false, // [7]
			awaitOpenAnimation: false, // [8]
			awaitCloseAnimation: false, // [9]
			debugMode: true, // [10]
		});
		// MicroModal.show('modal-1')
	});
</script>