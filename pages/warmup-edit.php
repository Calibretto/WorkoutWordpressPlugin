<?php
require_once plugin_dir_path( __FILE__ ) . "../admin/db/warmups.php";
require_once plugin_dir_path( __FILE__ ) . "../equipment.php";
require_once plugin_dir_path( __FILE__ ) . "../warmup.php";
require_once plugin_dir_path( __FILE__ ) . "../common.php";

$warmup = NULL;
try {
    $warmup = BHWorkoutPlugin_WarmupsDB::get($_POST['warmup_edit']);
} catch (Exception $e) {
    BHWorkoutPlugin_Notice::error($e->getMessage());
    error_log($e);
    return;
}
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?> Edit</h1>
    <div class="notice notice-error warmup-general-error is-dismissible" id='warmup-general-error'>
        <p>Something weird went wrong - please try reloading the page.</p>
    </div>
    <div class="notice notice-error warmup-name-error is-dismissible" id='warmup-name-error'>
        <p>You must include a name for the warmup</p>
    </div>
    <?php
        if (isset($_POST['warmup_edit']) == FALSE) {
            echo "Nothing to edit.";
        } else {
            ?>
                <form action="<?php menu_page_url('pages/warmup.php') ?>" method="post" name='update-warmup' id='update-warmup'>
                    <table>
                        <tr>
                            <td>Name:</td>
                            <td colspan='2'><input type='text' name='warmup_name' id='warmup_name' value='<?php echo $warmup->name; ?>'/> (Required)</td>
                        </tr>
                        <tr>
                            <td>Description:</td>
                            <td colspan='2'><textarea id="warmup_description" name="warmup_description" rows="4" cols="50"><?php echo $warmup->description; ?></textarea></td>
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
                                                                                $warmup->equipment_ids(), 
                                                                                TRUE);
                                    }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan='3'>
                                <input type='hidden' name='warmup_id' id='warmup_id' value='<?php echo $warmup->id; ?>'/>
                                <input type='hidden' name='warmup_save' id='warmup_save' value='warmup_save'/>
                                <input type='button' value='Save' onclick='updateWarmup();'>
                                <input type='button' value='Cancel' onclick='cancelEdit();'>
                            </td>
                        </tr>
                    </table>
                </form>
            <?php
        }
    ?>
</div>