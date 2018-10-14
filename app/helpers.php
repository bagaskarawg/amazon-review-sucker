<?php

function node_execute($name, array $params = [], $array = false) {
    $cwd = getcwd();
    chdir(base_path('node'));

    $cmd = "node " . $name . ".js '" . json_encode($params) . "'";
    if (config('app.debug')) {
	    echo $cmd . PHP_EOL;
	}
    exec($cmd, $lines);
    $output = preg_replace('/\x1b\[[0-9;]*[mG]/', '', implode("\n", $lines));
    $output = json_decode($output, $array);
    chdir($cwd);

    return $output;
}