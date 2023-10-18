<?php

/**
 * @var string
 */
$importer_class = $importer_class;

/**
 * @var string
 */
$importer_type = $importer_type;

?>

<form method="post" action="" enctype="multipart/form-data" novalidate="novalidate">
    <!-- <input type="hidden" name="option_page" value="general">
    <input type="hidden" name="action" value="update">
    <input type="hidden" id="_wpnonce" name="_wpnonce" value="94a31b49a3">
    <input type="hidden" name="_wp_http_referer" value="/wp-admin/options-general.php"> -->

    <input type="hidden" name="importer_type" value="<?php echo $importer_type; ?>">
    <!-- <input type="hidden" name="do_import" value="1"> -->

    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="importer_data">Servicios File</label>
                </th>
                <td>
                    <input type="file" id="importer_data" name="importer_data">
                </td>
            </tr>
        </tbody>
    </table>
    <p class="submit">
        <!-- <input type="submit" name="submit" id="submit" class="button button-primary" value="Importar"> -->
        <button type="submit" name="importer_action" class="button button-primary" value="import">Importar</button>
    </p>
    <p class="submit">
        <button type="submit" name="importer_action" class="button button-primary" value="delete_all">Borrar todos</button>
    </p>
</form>
