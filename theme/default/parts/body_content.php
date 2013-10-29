
<!-- body_content -->

	<div class="divider"></div>

 	<div id="body_wrapper">

 		<div id="main_wrapper">

 			<?php
 				/* by specifying the name of a 'part' directly in the tplGetPart function call,
 				 * we do not need to have used the tplAddPart() function in our Controller.
 				 * The part is assumed to reside in the current theme layout folder, but a
 				 * sub-folder can be specified in the call, eg. tplGetPart('members/formAdd')
                 *
                 * eg.
                 *     tplGetPart('someOtherStuff');
 				 */
            ?>

            <?php
                /*
                 * output any HTML content in the 'bodyContent' template variable
                 */
            ?>
            <?= tplGet('bodyContent'); ?>

 			<?php
				/* tplGetParts() displays ALL parts that have been added using tplAddPart() function
				 * within our Controller. Displayed in same order as added.
				 */
			?>
 			<?php tplGetParts(); ?>

 		</div>

	</div>

