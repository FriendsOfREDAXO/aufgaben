<?php

$qry = rex_sql::factory();
$qry->setTable('rex_aufgaben');
$qry->select('*');

if (rex_post('export', 'bool')) {
    $filename = 'aufgaben_' . date('Ymd_Hi');

    // kategorie

    $sql = rex_sql::factory();
    $sql->setTable('rex_aufgaben_categories');
    $sql->select("*");
    $kategorien = $sql->getArray();
    $kategorienArray = [];
    foreach ($kategorien as $kategorie) {
        $kategorienArray[$kategorie["id"]] = $kategorie["category"];
    }

    // user

    $sql->setTable('rex_user');
    $sql->select("*");
    $eigentuemer = $sql->getArray();
    $eigentuemerArray = [];
    foreach ($eigentuemer as $eigentuem) {
        $eigentuemerArray[$eigentuem["id"]] = $eigentuem["login"];
    }

    // status

    $sql->setTable('rex_aufgaben_status');
    $sql->select("*");
    $stati = $sql->getArray();
    $statiArray = [];
    foreach ($stati as $status) {
        $statiArray[$status["id"]] = $status["status"];
    }

    // get all todos

    $qryArray = [];
    $firstLineKeys = false;
    foreach ($qry->getArray() as $line) {
        if (empty($firstLineKeys)) {
            $firstLineKeys = array_keys($line);
            $firstLineKeys = array_flip($firstLineKeys);
        }

        // -----set values

        $line["kategorie"] = $kategorienArray[$line["category"]];
        $line["eigentuemer"] = $eigentuemerArray[$line["responsible"]];
        $line["prio"] = "Prio: " . $line["prio"];
        $line["status"] = $statiArray[$line["status"]];
        array_push($qryArray, array_merge($firstLineKeys, $line));
    }

    if (rex_post('type') === "json") {

        // ------JSON EXPORT

        ob_end_clean();
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '.json"');
        echo json_encode($qryArray, JSON_UNESCAPED_UNICODE);
        exit;

        // -----/JSON EXPORT

    }

    if (rex_post('type') === "csv") {

        // ------CSV EXPORT

        ob_end_clean();
        header('Content-Type: application/excel');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        $file = fopen('php://output', 'w');
        $firstLineKeys = false;
        function encode_items($array)
        {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $array[$key] = encode_items($value);
                } else {
                    $array[$key] = mb_convert_encoding($value, 'Windows-1252', 'UTF-8');
                }
            }

            return $array;
        }

        foreach (encode_items($qryArray) as $line) {
            if (empty($firstLineKeys)) {
                $firstLineKeys = array_keys($line);
                fputcsv($file, $firstLineKeys);
                $firstLineKeys = array_flip($firstLineKeys);
            }

            fputcsv($file, array_merge($firstLineKeys, $line));
        }

        fclose($file);
        exit;

        // -----/CSV EXPORT

    }
}

$content = '<div id="aufgaben">';
$content .= '<form action="' . rex_url::currentBackendPage() . '" data-pjax="false" method="post">';
$content .= '<fieldset>';
$content .= '<dl class="rex-form-group form-group">
    <dt>Dateityp wählen</dt>
        <dd>
            <dl class="rex-form-group form-group">
                <dd>
                    <div class="radio">
                        <input id="export-csv" type="radio" value="csv" name="type" checked/>
                        <label for="export-csv">' . $this->i18n('aufgaben_exim_dateityp_csv') . '</label>
                    </div>
                </dd>
            </dl>
            <dl class="rex-form-group form-group">
                <dd>
                    <div class="radio">
                        <input id="export-json" type="radio" value="json" name="type" />
                        <label for="export-json">' . $this->i18n('aufgaben_exim_dateityp_json') . '</label>
                    </div>
                </dd>
            </dl>
            <dl class="rex-form-group form-group">
                <dd>
                <br />
                    <button class="btn btn-save rex-form" type="submit" name="export" value="export">' . $this->i18n('aufgaben_exim_export_save') . '</button>
                </dd>
            </dl>
        </dd>
    </dl>';
$content .= '</fieldset>';
$content .= '</form>';
$content .= '</div>';
$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', $this->i18n('aufgaben_exim_export'));
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
