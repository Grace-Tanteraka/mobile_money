<?php

echo "\n";
echo "EXCEPTION: " . $message . "\n";
if (!empty($exception)) {
    echo get_class($exception) . "\n";
}
echo "\n";
