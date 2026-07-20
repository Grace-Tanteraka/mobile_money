<?php

echo "\n";
echo "ERROR: " . $message . "\n";
if (!empty($exception)) {
    echo get_class($exception) . "\n";
}
echo "\n";
