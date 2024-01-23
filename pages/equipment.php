<?php
require_once plugin_dir_path( __FILE__ ) . "../database.php";
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <div class="notice notice-error equipment-general-error" id='equipment-general-error'>
        <p>Something weird went wrong - please try reloading the page.</p>
    </div>
    <div class="notice notice-error equipment-name-error" id='equipment-name-error'>
        <p>You must include a name for the equipment</p>
    </div>
    <div class="notice notice-success equipment-added-success" id='equipment-added-success'>
        <p>Equipment successfully added</p>
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
                     
                    <select name='equipment_units' id='equipment_units'>
                        <option value="empty">-</option>
                        <option value="none">None</option>
                        <option value="kg">Kilograms</option>
                    </select>

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
    <?php
        $equipment = BHWorkoutPlugin_DatabaseManager::get_all_equipment();
        if(count($equipment) == 0) {
            echo "<p>No equipment available.</p>";
        } else {
            ?> 
            <table class='equipment'>
                <tr class='equipment-header'>
                    <th class='equipment-header-name'>Name</th>
                    <th class='equipment-header-min'>Value</th>
                    <th class='equipment-header-step'>Step</th>
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
                    echo "<td class='equipment-value-step'>".$e->display_value_step()."</td>";
                echo  "</tr>";

                $count++;
            }
            ?> </table> <?php
        }
    ?>
</div>