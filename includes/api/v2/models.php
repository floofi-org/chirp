<?php

endpoint();
$models = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/models.json"), true);
$modelsList = [];

foreach ($models as $id => $model) {
    if ($model["enabled"]) {
        unset($model["enabled"]);
        $modelsList[] = [
            "id" => $id,
            ...$model
        ];
    }
}

output($modelsList);
