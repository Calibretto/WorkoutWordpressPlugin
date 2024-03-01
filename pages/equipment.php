<?php
require_once plugin_dir_path( __FILE__ ) . "../admin/db/equipment.php";
require_once plugin_dir_path( __FILE__ ) . "../common.php";
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <div class="notice notice-error equipment-general-error is-dismissible" id='equipment-general-error'>
        <p>Something weird went wrong - please try reloading the page.</p>
    </div>
    <div class="notice notice-error equipment-name-error is-dismissible" id='equipment-name-error'>
        <p>You must include a name for the equipment.</p>
    </div>
    <h2>Add Equipment</h2>
    <form action="<?php menu_page_url('pages/equipment.php') ?>" method="post" name='add-equipment' id='add-equipment'>
        <table>
            <tr>
                <td>Name:</td>
                <td colspan='2'><input type='text' name='equipment_name' id='equipment_name'/> (Required)</td>
            </tr>
            <tr>
                <td>Value:</td>
                <td>
                    <input type='number' min='0' max='100' step='0.25' name='equipment_value_min' id='equipment_value_min'/>
                     - 
                    <input type='number' min='0' max='100' step='0.25' name='equipment_value_max' id='equipment_value_max'/>
                     
                    <?php
                        $units = ["empty" => "-"];
                        foreach(EquipmentUnit::cases() as $unit) {
                            $units[$unit->value] = $unit->value;
                        }

                        echo BHWorkoutPlugin_Common::htmlSelect($units, 'equipment_units', NULL);
                    ?>

                    Step: 
                    <input type='number' min='0' max='100' step='0.25' name='equipment_value_step' id='equipment_value_step'/>
                </td>
            </tr>
            <tr>
                <td colspan='3'>
                    <input type='hidden' name='equipment_submit' id='equipment_submit' value='equipment_submit'/>
                    <input type='button' value='Add' onclick='addEquipment();'>
                </td>
            </tr>
        </table>
    </form>
    <h2>Equipment List</h2>
    <form action="<?php menu_page_url('pages/equipment.php') ?>" method="post" name='delete-equipment' id='delete-equipment'>
        <input type="hidden" name='equipment_delete' id='equipment_delete' value="equipment_delete"/>
    </form>
    <form action="<?php admin_url('pages/equipment.php') ?>" method="post" name='edit-equipment' id='edit-equipment'>
        <input type="hidden" name='equipment_edit' id='equipment_edit' value="equipment_edit"/>
    </form>
    <?php
        $equipment = BHWorkoutPlugin_EquipmentDB::get_all_equipment();
        if(count($equipment) == 0) {
            echo "<p>No equipment available.</p>";
        } else {
            ?> 
            <table class='equipment'>
                <tr class='equipment-header'>
                    <th class='equipment-header-name'>Name</th>
                    <th class='equipment-header-value'>Value</th>
                    <th class='equipment-header-step'>Step</th>
                    <th class='equipment-header-actions'>Actions</th>
                </tr>
            <?php
            $count = 0;
            foreach($equipment as $e) {
                $class = "equipment-odd";
                if (($count % 2) == 0) {
                    $class = "equipment-even";
                }

                echo  "<tr class='$class equipment-list'>";
                    echo "<td class='equipment-name'>$e->name</td>";
                    echo "<td class='equipment-value'>".$e->display_value()."</td>";
                    echo "<td class='equipment-step'>".$e->display_value_step()."</td>";
                    echo "<td class='equipment-actions'>";
                        echo "<input type='button' value='edit' onclick='editEquipment(\"$e->id\");'/>";
                        echo "<input type='button' value='delete' onclick='deleteEquipment(\"$e->id\");'/>";
                    echo "</td>";
                echo  "</tr>";

                $count++;
            }
            ?> </table> <?php
        }
    ?>
</div>