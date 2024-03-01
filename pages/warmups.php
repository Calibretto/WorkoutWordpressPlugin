<?php
require_once plugin_dir_path( __FILE__ ) . "../admin/db/equipment.php";
require_once plugin_dir_path( __FILE__ ) . "../admin/db/warmups.php";
require_once plugin_dir_path( __FILE__ ) . "../common.php";
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <div class="notice notice-error warmup-general-error is-dismissible" id='warmup-general-error'>
        <p>Something weird went wrong - please try reloading the page.</p>
    </div>
    <div class="notice notice-error warmup-name-error is-dismissible" id='warmup-name-error'>
        <p>You must include a name for the warmup.</p>
    </div>
    <h2>Add Warmup</h2>
    <form action="<?php menu_page_url('pages/warmups.php') ?>" method="post" name='add-warmup' id='add-warmup'>
        <table>
            <tr>
                <td>Name:</td>
                <td><input type='text' name='warmup_name' id='warmup_name'/> (Required)</td>
            </tr>
            <tr>
                <td>Description:</td>
                <td><textarea id="warmup_description" name="warmup_description" rows="4" cols="50"></textarea></td>
            </tr>
            <tr>
                <td>Equipment:</td>
                <td>
                    <?php
                        $equipment = BHWorkoutPlugin_EquipmentDB::get_all();
                        if(count($equipment) == 0) {
                            echo "<p>No equipment available.</p>";
                        } else {
                            $equipment_map = [];
                            foreach($equipment as $e) {
                                $equipment_map[$e->id] = $e->name;
                            }

                            echo BHWorkoutPlugin_Common::htmlSelect($equipment_map, 
                                                                    'warmup_equipment', 
                                                                    NULL, 
                                                                    TRUE);
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan='2'>
                    <input type='hidden' name='warmup_submit' id='warmup_submit' value='warmup_submit'/>
                    <input type='button' value='Add' onclick='addWarmup();'>
                </td>
            </tr>
        </table>
    </form>
    <h2>Warmup List</h2>
    <form action="<?php menu_page_url('pages/warmups.php') ?>" method="post" name='delete-warmup' id='delete-warmup'>
        <input type="hidden" name='warmup_delete' id='warmup_delete' value="warmup_delete"/>
    </form>
    <form action="<?php admin_url('pages/warmups.php') ?>" method="post" name='edit-warmup' id='edit-warmup'>
        <input type="hidden" name='warmup_edit' id='warmup_edit' value="warmup_edit"/>
    </form>
    <?php
        $warmups = BHWorkoutPlugin_WarmupsDB::get_all();
        if(count($warmups) == 0) {
            echo "<p>No warmups available.</p>";
        } else {
            ?> 
            <table class='warmups'>
                <tr class='warmups-header'>
                    <th class='warmups-header-name'>Name</th>
                    <th class='warmups-header-description'>Description</th>
                    <th class='warmups-header-equipment'>Equipment</th>
                    <th class='warmups-header-actions'>Actions</th>
                </tr>
            <?php
            $count = 0;
            foreach($warmups as $w) {
                $class = "warmup-odd";
                if (($count % 2) == 0) {
                    $class = "warmup-even";
                }

                echo  "<tr class='$class warmups-list'>";
                    echo "<td class='warmup-name'>$w->name</td>";
                    echo "<td class='warmup-description'>".$w->description."</td>";
                    echo "<td class='warmup-equipment'>".$w->equipment_display_list()."</td>";
                    echo "<td class='warmup-actions'>";
                        echo "<input type='button' value='edit' onclick='editWarmup(\"$w->id\");'/>";
                        echo "<input type='button' value='delete' onclick='deleteWarmup(\"$w->id\");'/>";
                    echo "</td>";
                echo  "</tr>";

                $count++;
            }
            ?> </table> <?php
        }
    ?>
</div>