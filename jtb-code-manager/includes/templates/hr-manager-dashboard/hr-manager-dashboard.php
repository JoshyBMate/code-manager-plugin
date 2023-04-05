<?php
require_once( plugin_dir_path( __FILE__ ) . 'hr-manager-dashboard-queries.php');
$hr_manager = cm_create_manager_object();
$hr_manager_products = $hr_manager->purchased_products;
$hr_manager_employees = $hr_manager->employees;
$total_seats = [];
$employee_count_array = [];
foreach($hr_manager_products as $index => $product){
    $employee_count = 0;
    foreach($hr_manager_employees as $employee){
        if($employee->product_id == $product->id){
            $employee_count++;
        }
    }
    $employee_count_array[] = $employee_count;
    $total_seats[]= count($product->codes)+$employee_count;
}
?>

<div class="cm-training-content" data-element="cm-training-content">
    <h1>Training</h1>
	<div class="cm-training-controls-container">
	    <div class="cm-filter-container">
	        <div class="cm-view-by">
	            VIEW BY:
	        </div>
	        <div class="cm-filter-item" data-all="true" data-element="cm-filter-item">
	            All
	        </div>
	        <div class="cm-filter-item" data-unused="true" data-element="cm-filter-item">
	            Unused
	        </div>
	        <div class="cm-filter-item" data-used="true" data-element="cm-filter-item">
	            Used
	        </div>
	    </div>
		<button class="cm-bulk-email-button" style="display: none" data-element="cm-bulk-email-button">Send Bulk</button>
	</div>
	<div class="cm-training-content-container">
	<?php if(empty($hr_manager_products)) : ?>
	<div style="padding-bottom: 10px;"><p>No Courses to display</p></div>
	<?php endif; ?>
	<?php foreach($hr_manager_products as $index => $product) : ?>
	<div class="cm-training-content-accordion">
		<div class="cm-accordion-header">
			<div class="cm-accordion-item mainTitle">
				<p><?php echo $product->name ?></p>
			</div>
			<div class="cm-accordion-item seatTitle">
				<p><?php echo "Seats ". $total_seats[$index] ?></p>
			</div>
			<div class="cm-accordion-item">
				<p class="cm-seats-used">Seats used <span data-element="cm-seats-used"><?php echo $employee_count_array[$index] ?></span><?php echo "/".$total_seats[$index] ?></p>
			</div>
			<div class="cm-accordion-item accordion-arrow"></div>
		</div>
		<div class="cm-accordion-body" data-product-id=<?php echo $product->id ?>>
		    <?php foreach($product->codes as $code_id => $code) : ?>
			<div class="cm-accordion-body-row" data-element="cm-accordion-body-row" data-code-id="<?php echo $code_id ?>">
				<input type="text" name="fullname" data-element="cm-user-input" placeholder="Full Name">
				<input type="email" name="email" data-element="cm-user-input" placeholder="Email">
				<input type="text" name="code" data-element="cm-read-only-input" readonly value="<?php echo $code['code'] ?>">
				<button class="cm-invite-button" type="button" name="invite" data-element="cm-invite-button" data-code-id="<?php echo $code_id ?>">Invite</button>
			</div>
			<?php endforeach; ?>
			<?php foreach($hr_manager_employees as $employee) : ?>
			<?php if($employee->product_id == $product->id) : ?>
			<div class="cm-accordion-body-row" data-element="cm-accordion-body-row" data-code-used="true">
				<input type="text" name="fullname" data-element="cm-user-input-used" placeholder="Full Name" readonly value="<?php echo $employee->name ?>">
				<input type="email" name="email" data-element="cm-user-input-used" placeholder="Email" readonly value="<?php echo $employee->email ?>">
				<input type="text" name="code" data-element="cm-read-only-input-used" readonly value="Used">
				<button disabled class="cm-invite-button" type="button" name="invite" data-element="cm-invite-button-used">Invite</button>
			</div>
			<?php endif; ?>
			<?php endforeach; ?>
		</div>
    </div>
    <?php endforeach; ?>
</div>
    
<div class="cm-email-menu" data-element="cm-email-menu">
	<button class="cm-button-outline" data-element="cm-email-menu-close-button">Close</button>
	<h2>SEND BULK EMAIL</h2>
	<p>Type each emaill address with comma seperation. E.g.: jeremy.s@business.co.uk, sarah.k@business.co.uk</p>
	<input type="text" name="bulkemail" data-element="cm-bulk-email-list">
	<p>Please Select a product</p>
	<select name="product-select" data-element="product-dropdown">
	    <option value="0" >Product</option>
	    <?php foreach ($hr_manager_products as $product) : ?>
	        <option value="<?php echo $product->id ?>"><?php echo $product->name ?></option>
	    <?php endforeach; ?>
	</select>
	<button class="cm-button-pink" data-element="cm-send-bulk-email-button">Send Bulk Email</button>
</div>