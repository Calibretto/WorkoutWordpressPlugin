<?php
require_once plugin_dir_path( __FILE__ ) . "../admin/db/equipment.php";
require_once plugin_dir_path( __FILE__ ) . "../equipment.php";
require_once plugin_dir_path( __FILE__ ) . "../common.php";

$equipment = NULL;
try {
    $equipment = BHWorkoutPlugin_EquipmentDB::get_equipment($_POST['equipment_edit']);
} catch (Exception $e) {
    BHWorkoutPlugin_Notice::error($e->getMessage());
    error_log($e);
    return;
}
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?> Edit</h1>
    <div class="notice notice-error equipment-general-error is-dismissible" id='equipment-general-error'>
        <p>Something weird went wrong - please try reloading the page.</p>
    </div>
    <div class="notice notice-error equipment-name-error is-dismissible" id='equipment-name-error'>
        <p>You must include a name for the equipment</p>
    </div>
    <?php
        if (isset($_POST['equipment_edit']) == FALSE) {
            echo "Nothing to edit.";
        } else {
            ?>
                <form action="<?php menu_page_url('pages/equipment.php') ?>" method="post" name='update-equipment' id='update-equipment'>
                    <table>
                        <tr>
                            <td>Name:</td>
                            <td colspan='2'><input type='text' name='equipment_name' id='equipment_name' value='<?php echo $equipment->name; ?>'/> (Required)</td>
                        </tr>
                        <tr>
                            <td>Value:</td>
                            <td>
                                <input type='number' min='0' max='100' step='0.25' name='equipment_value_min' id='equipment_value_min' value='<?php echo $equipment->value_min; ?>'/>
                                - 
                                <input type='number' min='0' max='100' step='0.25' name='equipment_value_max' id='equipment_value_max' value='<?php echo $equipment->value_max; ?>'/>
                                
                                <?php 
                                    $selected = is_null($equipment->units) ? NULL : $equipment->units->value;
                                    $units = ["empty" => "-"];
                                    foreach(EquipmentUnit::cases() as $unit) {
                                        $units[$unit->value] = $unit->value;
                                    }

                                    echo BHWorkoutPlugin_Common::htmlSelect($units, 'equipment_units', $selected);
                                ?>

                                Step: 
                                <input type='number' min='0' max='100' step='0.25' name='equipment_value_step' id='equipment_value_step' value='<?php echo $equipment->value_step; ?>'/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan='3'>
                                <input type='hidden' name='equipment_id' id='equipment_id' value='<?php echo $equipment->id; ?>'/>
                                <input type='hidden' name='equipment_save' id='equipment_save' value='equipment_save'/>
                                <input type='button' value='Save' onclick='updateEquipment();'>
                                <input type='button' value='Cancel' onclick='cancelEquipmentEdit();'>
                            </td>
                        </tr>
                    </table>
                </form>
            <?php
        }
    ?>
</div>